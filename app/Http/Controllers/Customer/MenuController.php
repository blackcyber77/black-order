<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::active()->ordered()->get();
        
        $query = MenuItem::with(['category'])
            ->where('is_available', true);

        $categoryId = $request->input('category', $request->input('category_id'));
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menuItems = $query->latest()->get();

        // Handle table session logic from QR URL (/menu?table=XX)
        if ($request->filled('table')) {
            $tableNumber = trim((string) $request->query('table'));
            $table = DiningTable::where('table_number', $tableNumber)
                ->where('is_active', true)
                ->first();

            if ($table) {
                session(['table_number' => $table->table_number]);
                \Illuminate\Support\Facades\Log::info('Session set for table: ' . $table->table_number);
            } else {
                \Illuminate\Support\Facades\Log::warning('Table not found for number: ' . $tableNumber);
            }
        }

        $tableNumber = session('table_number');

        return view('customer.menu.index', compact(
            'menuItems', 
            'categories', 
            'tableNumber'
        ));
    }

    public function show(MenuItem $menuItem)
    {
        if (!$menuItem->is_available) {
            abort(404);
        }

        return view('customer.menu.show', compact('menuItem'));
    }

    public function clearLocation()
    {
        session()->forget(['table_number']);
        return redirect()->route('home')->with('success', 'Lokasi berhasil dihapus. Silakan scan ulang.');
    }
}
