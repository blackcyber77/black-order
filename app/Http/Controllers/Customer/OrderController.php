<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Keranjang kosong');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $id => $item) {
            $menuItem = MenuItem::find($id);
            if ($menuItem && $menuItem->is_available) {
                $itemSubtotal = $menuItem->price * $item['quantity'];
                $cartItems[] = [
                    'id' => $id,
                    'name' => $menuItem->name,
                    'price' => $menuItem->price,
                    'image' => $menuItem->image,
                    'menu_item' => $menuItem,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
                $subtotal += $itemSubtotal;
            }
        }

        if (empty($cartItems)) {
            return redirect()->route('menu.index')->with('error', 'Tidak ada item valid di keranjang');
        }

        $serviceFee = Setting::getServiceFee();
        $qrisImage = Setting::getQrisImage();

        $tableNumber = session('table_number');
        $deliveryFee = 0;
        
        $total = $subtotal + $serviceFee + $deliveryFee;

        // Available payment methods for customer (QR ordering = cashless only)
        $paymentMethods = Order::customerPaymentMethods();

        return view('customer.orders.checkout', compact(
            'cartItems',
            'subtotal',
            'serviceFee',
            'deliveryFee',
            'total',
            'qrisImage',
            'tableNumber',
            'paymentMethods'
        ));
    }

    public function store(Request $request)
    {
        // Validate: only cashless methods allowed for customer QR ordering
        $allowedMethods = implode(',', Order::customerPaymentMethods());
        
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'table_number' => 'required|string|max:50',
            'payment_method' => "required|in:{$allowedMethods}",
            'payment_proof' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Keranjang kosong');
        }

        try {
            DB::beginTransaction();

            $table = DiningTable::where('table_number', $request->table_number)
                ->where('is_active', true)
                ->firstOrFail();
            $subtotal = 0;
            $orderItems = [];

            foreach ($cart as $id => $item) {
                $menuItem = MenuItem::find($id);
                if ($menuItem && $menuItem->is_available) {
                    $itemSubtotal = $menuItem->price * $item['quantity'];
                    $orderItems[] = [
                        'menu_item_id' => $id,
                        'quantity' => $item['quantity'],
                        'price' => $menuItem->price,
                        'subtotal' => $itemSubtotal,
                    ];
                    $subtotal += $itemSubtotal;
                }
            }

            if (empty($orderItems)) {
                throw new \Exception('Tidak ada item valid');
            }

            $serviceFee = Setting::getServiceFee();
            $deliveryFee = 0;
            $total = $subtotal + $serviceFee + $deliveryFee;

            // Handle payment proof upload
            $paymentProof = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProof = $request->file('payment_proof')->store('payments', 'public');
            }

            // Create order — payment always starts as 'pending' for cashless
            // Will be updated by payment gateway callback or admin verification
            $order = Order::create([
                'tower_id' => null,
                'table_number' => $request->table_number,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'payment_proof' => $paymentProof,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Create transaction record
            $order->transaction()->create([
                'total_price' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
            ]);

            // Clear cart
            session()->forget('cart');

            // Update dining table status to occupied
            DiningTable::where('table_number', $table->table_number)
                ->update(['status' => 'terisi']);

            DB::commit();

            return redirect()->route('orders.confirmation', $order->order_number)
                ->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    public function confirmation($orderNumber)
    {
        $order = Order::with(['items.menuItem'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $qrisImage = Setting::getQrisImage();

        return view('customer.orders.confirmation', compact('order', 'qrisImage'));
    }

    public function track(Request $request)
    {
        $order = null;
        
        if ($request->filled('order_number')) {
            $order = Order::with(['items.menuItem'])
                ->where('order_number', $request->order_number)
                ->first();
        }

        return view('customer.orders.track', compact('order'));
    }
}
