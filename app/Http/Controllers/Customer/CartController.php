<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
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

        $serviceFee = Setting::getServiceFee();
        $deliveryFee = 0;
        $tableNumber = session('table_number');

        $total = $subtotal + $serviceFee + $deliveryFee;

        return view('customer.cart.index', compact(
            'cartItems',
            'subtotal',
            'serviceFee',
            'deliveryFee',
            'total',
            'tableNumber'
        ));
    }

    public function add(Request $request, MenuItem $menuItem)
    {
        if (!$menuItem->is_available) {
            return back()->with('error', 'Menu tidak tersedia.');
        }

        // Validate table location
        if (!session()->has('table_number')) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Silakan scan QR Code meja terlebih dahulu.'], 403);
            }
            return back()->with('error', 'Silakan scan QR Code meja terlebih dahulu.');
        }

        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        if (isset($cart[$menuItem->id])) {
            $cart[$menuItem->id]['quantity'] += $quantity;
        } else {
            $cart[$menuItem->id] = [
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $menuItem->name . ' ditambahkan ke keranjang',
                'cart_count' => array_sum(array_column($cart, 'quantity')),
            ]);
        }

        return back()->with('success', $menuItem->name . ' ditambahkan ke keranjang');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $quantity = max(1, (int) $request->input('quantity', 1));

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Keranjang diperbarui');
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => array_sum(array_column($cart, 'quantity')),
            ]);
        }

        return back()->with('success', 'Item dihapus dari keranjang');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Keranjang dikosongkan');
    }

    public function count()
    {
        $cart = session()->get('cart', []);
        return response()->json([
            'count' => array_sum(array_column($cart, 'quantity')),
        ]);
    }
}
