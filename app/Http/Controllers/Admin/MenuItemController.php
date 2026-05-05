<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
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
        $promoMenus = MenuItem::with('category')
            ->where('is_promo', true)
            ->promoFirst()
            ->get();

        $menuItems = MenuItem::with('category')
            ->orderBy('name')
            ->get();

        return view('admin.menus.promos', compact('promoMenus', 'menuItems'));
    }

    public function storePromo(Request $request)
    {
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
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
            'is_best_seller' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');

        MenuItem::create($validated);

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
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'nullable|boolean',
            'is_best_seller' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $menu->update($validated);

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
}
