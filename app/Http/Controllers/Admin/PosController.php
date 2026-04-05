<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Tampilkan antarmuka POS (Daftar Menu & Keranjang)
     */
    public function create(Request $request)
    {
        $categories = Category::all();
        $tables = DiningTable::where('is_active', true)
            ->orderBy('table_number')
            ->get();
        $tablesData = $tables->map(function ($table) {
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'status' => $table->status,
            ];
        })->values();
        
        $query = MenuItem::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $menuItems = $query->where('is_available', true)->get();

        // POS payment methods (includes tunai for walk-in)
        $paymentMethods = Order::posPaymentMethods();

        return view('admin.pos.create', compact('categories', 'tables', 'tablesData', 'menuItems', 'paymentMethods'));
    }

    /**
     * Dashboard Pesanan Aktif (Meja yang sedang bersantap atau pending)
     */
    public function index()
    {
        $orders = Order::with(['items.menuItem'])
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->paginate(20);

        return view('admin.pos.index', compact('orders'));
    }

    /**
     * Proses input pesanan dari kasir
     */
    public function store(Request $request)
    {
        // POS accepts tunai (cash) and qris
        $allowedMethods = implode(',', Order::posPaymentMethods());

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'order_items' => 'required|string', // JSON string from frontend
            'payment_method' => "required|in:{$allowedMethods}",
            'table_number' => 'nullable|string|max:50|exists:dining_tables,table_number',
        ]);

        $orderItemsData = json_decode($request->order_items, true);
        
        if (empty($orderItemsData)) {
            return back()->with('error', 'Keranjang belanja kosong');
        }

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($orderItemsData as $item) {
                $menuItem = MenuItem::find($item['id']);
                if (!$menuItem) continue;

                $itemSubtotal = $menuItem->price * $item['quantity'];
                $itemsToCreate[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'price' => $menuItem->price,
                    'subtotal' => $itemSubtotal,
                ];
                $subtotal += $itemSubtotal;
            }

            if (empty($itemsToCreate)) {
                throw new \Exception('Tidak ada item valid');
            }

            $serviceFee = Setting::getServiceFee();
            $deliveryFee = 0; 

            $tableNumber = $request->table_number;
            if ($tableNumber) {
                DiningTable::where('table_number', $tableNumber)
                    ->update(['status' => 'terisi']);
            }

            $total = $subtotal + $serviceFee + $deliveryFee;

            // POS: Tunai = paid immediately, QRIS = pending verification
            $isTunai = $request->payment_method === Order::PAYMENT_TUNAI;
            $paymentStatus = $isTunai ? 'paid' : 'pending';

            $order = Order::create([
                'tower_id' => null,
                'table_number' => $tableNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone ?? '-',
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'paid_at' => $isTunai ? now() : null,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => 'processing',
                'notes' => $request->notes,
            ]);

            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            $order->transaction()->create([
                'total_price' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
            ]);

            DB::commit();

            return redirect()->route('admin.pos.receipt', $order->id)->with('success', 'Pesanan POS berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan POS: ' . $e->getMessage());
        }
    }

    /**
     * Lunasi pesanan (Ubah ke Paid & Selesai & Kosongkan Meja)
     */
    public function markAsPaid(Order $order)
    {
        DB::beginTransaction();
        try {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => 'completed'
            ]);

            if ($order->transaction) {
                $order->transaction->update(['payment_status' => 'paid']);
            } else {
                $order->transaction()->create([
                    'total_price' => $order->total,
                    'payment_method' => $order->payment_method,
                    'payment_status' => 'paid',
                ]);
            }

            // Reset table status
            if ($order->table_number) {
                DiningTable::where('table_number', $order->table_number)
                    ->update(['status' => 'kosong']);
            }

            DB::commit();

            return redirect()->route('admin.pos.receipt', $order->id)->with('success', 'Pesanan dilunasi. Meja kini kosong.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal melunasi pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman cetak struk
     */
    public function receipt(Order $order)
    {
        $order->load(['items.menuItem']);
        return view('admin.pos.receipt', compact('order'));
    }
}
