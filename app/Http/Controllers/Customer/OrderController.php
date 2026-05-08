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
            'tableNumber',
            'paymentMethods'
        ));
    }

    public function store(Request $request)
    {
        // Validate: only cashless methods allowed for customer QR ordering
        $allowedMethods = implode(',', Order::customerPaymentMethods());
        
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => ['required', 'string', 'min:9', 'max:15', 'regex:/^[0-9]+$/'],
            'customer_email' => 'required|email|max:255',
            'table_number'   => 'required|string|max:50',
            'payment_method' => "required|in:{$allowedMethods}",
            'notes'          => 'nullable|string|max:1000',
        ], [
            'customer_name.required'  => 'Nama lengkap wajib diisi.',
            'customer_phone.required' => 'Nomor HP wajib diisi.',
            'customer_phone.min'      => 'Nomor HP minimal 9 digit.',
            'customer_phone.regex'    => 'Nomor HP hanya boleh berisi angka.',
            'customer_email.required' => 'Email wajib diisi untuk konfirmasi pembayaran.',
            'customer_email.email'    => 'Format email tidak valid.',
            'table_number.required'   => 'Nomor meja tidak terdeteksi. Silakan scan ulang QR.',
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

            // Normalize phone: strip leading 0, +62, or 62 so iPaymu gets clean number
            $rawPhone = preg_replace('/\D/', '', (string) $request->customer_phone);
            if (str_starts_with($rawPhone, '62')) {
                $rawPhone = substr($rawPhone, 2);
            } elseif (str_starts_with($rawPhone, '0')) {
                $rawPhone = substr($rawPhone, 1);
            }
            $phone = '0' . $rawPhone; // store with leading 0 (e.g. 081234567890)

            // Create order — payment always starts as 'pending' for cashless
            // Will be updated by payment gateway callback or admin verification
            $order = Order::create([
                'tower_id'        => null,
                'table_number'    => $request->table_number,
                'customer_name'   => $request->customer_name,
                'customer_phone'  => $phone,
                'customer_email'  => $request->customer_email,
                'payment_method'  => $request->payment_method,
                'payment_status'  => 'pending',
                'payment_gateway' => 'ipaymu',
                'subtotal'        => $subtotal,
                'service_fee'     => $serviceFee,
                'delivery_fee'    => $deliveryFee,
                'total'           => $total,
                'status'          => 'pending',
                'notes'           => $request->notes,
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

            return redirect()->route('payment.create', $order)
                ->with('success', 'Pesanan berhasil dibuat. Lanjutkan pembayaran di iPaymu.');

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

        return view('customer.orders.confirmation', compact('order'));
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
