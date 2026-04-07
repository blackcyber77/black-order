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
use Illuminate\Support\Facades\Schema;

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
        $orders = Order::with(['items.menuItem', 'cashier'])
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
            'payment_method' => "nullable|in:{$allowedMethods}",
            'table_number' => 'nullable|string|max:50|exists:dining_tables,table_number',
            'submit_action' => 'nullable|in:pay,hold',
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

            $submitAction = $request->input('submit_action', 'pay');
            $paymentMethod = $request->input('payment_method', Order::PAYMENT_TUNAI);
            $isHold = $submitAction === 'hold';
            $isTunai = $paymentMethod === Order::PAYMENT_TUNAI;
            $paymentStatus = (!$isHold && $isTunai) ? 'paid' : 'pending';
            $status = $isHold ? Order::STATUS_PENDING : Order::STATUS_PROCESSING;

            $order = Order::create([
                'tower_id' => null,
                'table_number' => $tableNumber,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone ?? '-',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'paid_at' => (!$isHold && $isTunai) ? now() : null,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => $status,
                'notes' => $request->notes,
            ] + $this->optionalLifecyclePayload($isHold));

            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            $order->transaction()->create([
                'total_price' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
            ]);

            DB::commit();

            if ($isHold) {
                return redirect()->route('admin.pos.index')->with('success', 'Pesanan berhasil ditahan dan bisa dipanggil kembali.');
            }

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
            if ($order->status === Order::STATUS_CANCELLED) {
                return back()->with('error', 'Pesanan void tidak dapat dilunasi.');
            }

            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => Order::STATUS_COMPLETED
            ] + $this->optionalLifecycleResetPayload());

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

    public function hold(Order $order)
    {
        if ($order->status === Order::STATUS_COMPLETED || $order->status === Order::STATUS_CANCELLED) {
            return back()->with('error', 'Pesanan tidak bisa ditahan.');
        }

        $order->update([
            'status' => Order::STATUS_PENDING,
        ] + $this->optionalHeldPayload(now()));

        return back()->with('success', 'Pesanan berhasil ditahan.');
    }

    public function recall(Order $order)
    {
        if (!$order->isHeld()) {
            return back()->with('error', 'Pesanan ini tidak dalam status hold.');
        }

        if ($order->status === Order::STATUS_COMPLETED || $order->status === Order::STATUS_CANCELLED) {
            return back()->with('error', 'Pesanan tidak bisa dipanggil kembali.');
        }

        $order->update([
            'status' => Order::STATUS_PROCESSING,
        ] + $this->optionalHeldPayload(null));

        return back()->with('success', 'Pesanan berhasil dipanggil kembali.');
    }

    public function void(Request $request, Order $order)
    {
        $request->validate([
            'void_reason' => 'required|string|min:3|max:500',
        ]);

        if ($order->status === Order::STATUS_COMPLETED) {
            return back()->with('error', 'Pesanan yang sudah selesai tidak bisa di-void.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => Order::STATUS_CANCELLED,
            ] + $this->optionalVoidPayload($request->void_reason));

            if ($order->table_number) {
                DiningTable::where('table_number', $order->table_number)
                    ->update(['status' => 'kosong']);
            }

            DB::commit();
            return back()->with('success', 'Pesanan berhasil di-void.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal void pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman cetak struk
     */
    public function receipt(Order $order)
    {
        $order->load(['items.menuItem', 'cashier']);
        return view('admin.pos.receipt', compact('order'));
    }

    private function optionalLifecyclePayload(bool $isHold): array
    {
        $payload = [];
        if (Schema::hasColumn('orders', 'cashier_id')) {
            $payload['cashier_id'] = auth()->id();
        }
        if (Schema::hasColumn('orders', 'held_at')) {
            $payload['held_at'] = $isHold ? now() : null;
        }

        return $payload;
    }

    private function optionalLifecycleResetPayload(): array
    {
        if (!Schema::hasColumn('orders', 'held_at')) {
            return [];
        }

        return ['held_at' => null];
    }

    private function optionalHeldPayload($value): array
    {
        if (!Schema::hasColumn('orders', 'held_at')) {
            return [];
        }

        return ['held_at' => $value];
    }

    private function optionalVoidPayload(string $reason): array
    {
        $payload = [];
        if (Schema::hasColumn('orders', 'held_at')) {
            $payload['held_at'] = null;
        }
        if (Schema::hasColumn('orders', 'voided_at')) {
            $payload['voided_at'] = now();
        }
        if (Schema::hasColumn('orders', 'void_reason')) {
            $payload['void_reason'] = $reason;
        }

        return $payload;
    }
}
