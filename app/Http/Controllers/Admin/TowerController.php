<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tower;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TowerController extends Controller
{
    public function index()
    {
        $tables = DiningTable::with('tower')
            ->latest()
            ->paginate(20);

        return view('admin.towers.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.towers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'delivery_fee' => 'required|integer|min:0',
        ]);

        Tower::create($request->only(['name', 'delivery_fee']));

        if ($request->has('save_and_create_another')) {
            return back()->with('success', 'Tower berhasil ditambahkan. Silakan tambah tower baru.');
        }

        return redirect()->route('admin.towers.index')->with('success', 'Tower berhasil ditambahkan');
    }

    public function edit(Tower $tower)
    {
        return view('admin.towers.edit', compact('tower'));
    }

    public function update(Request $request, Tower $tower)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'delivery_fee' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $tower->update([
            'name' => $request->name,
            'delivery_fee' => $request->delivery_fee,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.towers.index')->with('success', 'Tower berhasil diperbarui');
    }

    public function destroy(Tower $tower)
    {
        $tower->delete();
        return redirect()->route('admin.towers.index')->with('success', 'Tower berhasil dihapus');
    }

    // Table Management
    public function storeTableGlobal(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:50|unique:dining_tables,table_number',
        ]);

        // Keep DB compatibility: each table still belongs to one tower internally.
        $tower = Tower::where('is_active', true)->first()
            ?? Tower::first()
            ?? Tower::create([
                'name' => 'Default',
                'delivery_fee' => 0,
                'is_active' => true,
            ]);

        $table = DiningTable::create([
            'tower_id' => $tower->id,
            'table_number' => $request->table_number,
            'is_active' => true,
            'status' => 'kosong',
        ]);

        $qrUrl = rtrim(config('app.url'), '/') . '/menu?table=' . $table->table_number;
        $table->update(['qr_code' => $qrUrl]);

        return back()->with('success', 'Meja berhasil ditambahkan');
    }

    public function tables(Tower $tower)
    {
        $tables = $tower->diningTables()->latest()->paginate(20);
        return view('admin.towers.tables', compact('tower', 'tables'));
    }

    public function storeTable(Request $request, Tower $tower)
    {
        $request->validate([
            'table_number' => 'required|string|max:50',
        ]);

        $table = $tower->diningTables()->create([
            'table_number' => $request->table_number,
        ]);

        // Generate QR Code URL
        $qrUrl = rtrim(config('app.url'), '/') . '/menu?table=' . $table->table_number;
        $table->update(['qr_code' => $qrUrl]);

        return back()->with('success', 'Meja berhasil ditambahkan');
    }

    public function destroyTable(DiningTable $table)
    {
        $table->delete();
        return back()->with('success', 'Meja berhasil dihapus');
    }

    public function generateQr(DiningTable $table)
    {
        $qrUrl = rtrim(config('app.url'), '/') . '/menu?table=' . $table->table_number;
        
        return view('admin.towers.qr', compact('table', 'qrUrl'));
    }
}
