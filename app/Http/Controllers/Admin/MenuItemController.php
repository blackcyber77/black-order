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
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available', true);

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
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $validated['image'] = $request->file('image')->store('menus', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available');
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
