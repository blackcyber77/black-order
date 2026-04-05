<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['tower']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['tower', 'items.menuItem', 'transaction']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Verify payment — now works for all cashless payment methods (QRIS, Bank Transfer)
     */
    public function verifyPayment(Order $order)
    {
        if (!$order->isCashless()) {
            return back()->with('error', 'Verifikasi hanya untuk pembayaran cashless');
        }

        $order->update([
            'payment_status' => 'verified',
            'paid_at' => $order->paid_at ?? now(),
        ]);

        if ($order->transaction) {
            $order->transaction->update(['payment_status' => 'verified']);
        }

        return back()->with('success', 'Pembayaran terverifikasi');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,delivering,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan diperbarui');
    }
}
