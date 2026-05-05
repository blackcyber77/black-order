<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('availability')) {
            $isAvailable = $request->availability === 'available';
            $query->where('is_available', $isAvailable);
        }

        $menus = $query->latest()->paginate(20);
        $categories = Category::ordered()->get();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function promos()
    {
        $promoFeatureReady = $this->promoColumnsReady();

        if (!$promoFeatureReady) {
            return view('admin.menus.promos', [
                'promoMenus' => collect(),
                'menuItems' => collect(),
                'promoFeatureReady' => false,
            ])->with('error', 'Fitur promo/bundling belum aktif di database. Jalankan migrasi terbaru terlebih dahulu.');
        }

        $promoMenus = MenuItem::with('category')
            ->where('is_promo', true)
            ->promoFirst()
            ->get();

        $menuItems = MenuItem::with('category')
            ->orderBy('name')
            ->get();

        return view('admin.menus.promos', compact('promoMenus', 'menuItems', 'promoFeatureReady'));
    }

    public function storePromo(Request $request)
    {
        if (!$this->promoColumnsReady()) {
            return back()->with('error', 'Fitur promo/bundling belum aktif di database. Jalankan migrasi terbaru terlebih dahulu.');
        }

        $request->merge([
            'promo_title' => trim((string) $request->input('promo_title')),
            'promo_original_price' => $this->normalizeMoneyInput($request->input('promo_original_price')),
            'promo_sort_order' => $this->normalizeIntegerInput($request->input('promo_sort_order')),
        ]);

        $validated = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'promo_type' => 'required|in:promo,bundling',
            'promo_title' => 'required|string|max:120',
            'promo_original_price' => 'nullable|numeric|min:0',
            'promo_sort_order' => 'nullable|integer|min:0',
        ]);

        $menu = MenuItem::findOrFail($validated['menu_item_id']);
        $menu->update([
            'is_promo' => true,
            'promo_type' => $validated['promo_type'],
            'promo_title' => $validated['promo_title'],
            'promo_original_price' => $validated['promo_original_price'] ?? null,
            'promo_sort_order' => $validated['promo_sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Promo/bundling berhasil disimpan dan otomatis tampil di urutan atas menu.');
    }

    public function destroyPromo(MenuItem $menu)
    {
        if (!$this->promoColumnsReady()) {
            return back()->with('error', 'Fitur promo/bundling belum aktif di database. Jalankan migrasi terbaru terlebih dahulu.');
        }

        $menu->update([
            'is_promo' => false,
            'promo_type' => null,
            'promo_title' => null,
            'promo_original_price' => null,
            'promo_sort_order' => 0,
        ]);

        return back()->with('success', 'Promo/bundling berhasil dihapus.');
    }

    public function create()
    {
        $categories = Category::ordered()->get();
        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
        ];

        if ($this->bestSellerColumnReady()) {
            $rules['is_best_seller'] = 'nullable|boolean';
        }

        $validated = $request->validate($rules);

        try {
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('menus', 'public');
            }

            $validated['is_available'] = $request->boolean('is_available', true);
            if ($this->bestSellerColumnReady()) {
                $validated['is_best_seller'] = $request->boolean('is_best_seller');
            }

            MenuItem::create($validated);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan menu. Pastikan migrasi terbaru sudah dijalankan dan folder storage dapat ditulis.');
        }

        if ($request->has('save_and_create_another')) {
            return back()->with('success', 'Menu berhasil ditambahkan. Silakan tambah menu baru.');
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit(MenuItem $menu)
    {
        $categories = Category::ordered()->get();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
        ];

        if ($this->bestSellerColumnReady()) {
            $rules['is_best_seller'] = 'nullable|boolean';
        }

        $validated = $request->validate($rules);

        try {
            if ($request->hasFile('image')) {
                if ($menu->image) {
                    Storage::disk('public')->delete($menu->image);
                }
                $validated['image'] = $request->file('image')->store('menus', 'public');
            }

            $validated['is_available'] = $request->boolean('is_available');
            if ($this->bestSellerColumnReady()) {
                $validated['is_best_seller'] = $request->boolean('is_best_seller');
            }
            $menu->update($validated);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui menu. Pastikan migrasi terbaru sudah dijalankan dan folder storage dapat ditulis.');
        }

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui');
    }

    public function destroy(MenuItem $menu)
    {
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus');
    }

    public function toggleAvailability(MenuItem $menu)
    {
        $menu->update([
            'is_available' => !$menu->is_available,
        ]);

        $message = $menu->is_available
            ? "{$menu->name} tersedia untuk POS & QR order."
            : "{$menu->name} disembunyikan dari POS & QR order.";

        return back()->with('success', $message);
    }

    private function promoColumnsReady(): bool
    {
        static $ready = null;
        if ($ready !== null) {
            return $ready;
        }

        $ready = Schema::hasColumns('menu_items', [
            'is_promo',
            'promo_type',
            'promo_title',
            'promo_original_price',
            'promo_sort_order',
        ]);

        return $ready;
    }

    private function bestSellerColumnReady(): bool
    {
        static $ready = null;
        if ($ready !== null) {
            return $ready;
        }

        $ready = Schema::hasColumn('menu_items', 'is_best_seller');

        return $ready;
    }

    private function normalizeMoneyInput(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        // Support input formats: 25000, 25.000, 25,000, Rp 25.000
        $normalized = preg_replace('/[^0-9,.\-]/', '', $raw);
        if ($normalized === null || $normalized === '') {
            return $value;
        }

        $hasDot = str_contains($normalized, '.');
        $hasComma = str_contains($normalized, ',');

        if ($hasDot && $hasComma) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } elseif ($hasComma) {
            $parts = explode(',', $normalized);
            $last = end($parts);
            if ($last !== false && strlen($last) <= 2) {
                $normalized = implode('', array_slice($parts, 0, -1)) . '.' . $last;
            } else {
                $normalized = str_replace(',', '', $normalized);
            }
        } elseif ($hasDot) {
            $parts = explode('.', $normalized);
            $last = end($parts);
            if ($last !== false && strlen($last) > 2) {
                $normalized = str_replace('.', '', $normalized);
            }
        }

        return is_numeric($normalized) ? (float) $normalized : $value;
    }

    private function normalizeIntegerInput(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $normalized = preg_replace('/[^0-9\-]/', '', $raw);
        if ($normalized === null || $normalized === '' || !is_numeric($normalized)) {
            return null;
        }

        return (int) $normalized;
    }
}
