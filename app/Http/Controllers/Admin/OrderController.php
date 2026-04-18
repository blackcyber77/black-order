<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function qrNotifications(Request $request)
    {
        $query = Order::query()
            ->whereNull('cashier_id')
            ->where('payment_method', Order::PAYMENT_QRIS)
            ->latest('created_at')
            ->latest('id');

        if ($request->filled('since')) {
            try {
                $since = Carbon::parse($request->query('since'));
                $query->where('created_at', '>', $since);
            } catch (\Throwable $e) {
                // Ignore invalid timestamp and return the latest snapshot.
            }
        }

        $orders = $query->take(5)->get();
        $latestCreatedAt = null;
        $latestOrderTime = $orders->max('created_at');
        if ($latestOrderTime) {
            $latestCreatedAt = $latestOrderTime->toISOString();
        }

        return response()->json([
            'orders' => $orders->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'table_number' => $order->table_number,
                    'status_label' => $order->status_label,
                    'formatted_total' => $order->formatted_total,
                    'created_at_label' => $order->created_at->format('H:i'),
                    'thermal_print_url' => route('admin.orders.thermal-print', $order),
                ];
            })->values(),
            'latest_created_at' => $latestCreatedAt,
        ]);
    }

    public function report(Request $request)
    {
        $source = $request->input('source', 'all');
        $period = $request->input('period', 'today');
        $menuItemId = $request->input('menu_item_id');

        $query = Order::with(['cashier', 'items.menuItem']);

        $this->applySourceFilter($query, $source);
        $this->applyMenuFilter($query, $menuItemId);
        $this->applyDateFilter($query, $request, $period);

        $orders = $query->latest()->paginate(20)->appends($request->query());

        $summaryQuery = Order::query();
        $this->applyDateFilter($summaryQuery, $request, $period);
        $summary = [
            'total_orders' => (clone $summaryQuery)->count(),
            'walk_in_orders' => (clone $summaryQuery)->whereNotNull('cashier_id')->count(),
            'qr_orders' => (clone $summaryQuery)->whereNull('cashier_id')->count(),
            'total_revenue' => (clone $summaryQuery)->sum('total'),
        ];

        $menuItems = MenuItem::orderBy('name')->get(['id', 'name']);

        return view('admin.orders.report', compact('orders', 'menuItems', 'summary', 'source', 'period', 'menuItemId'));
    }

    public function index(Request $request)
    {
        $query = Order::with(['cashier']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(20);
        $cashiers = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('admin.orders.index', compact('orders', 'cashiers'));
    }

    public function show(Order $order)
    {
        $order->load(['cashier', 'items.menuItem', 'transaction']);
        return view('admin.orders.show', compact('order'));
    }

    public function thermalPrint(Order $order)
    {
        $order->load(['items.menuItem']);
        return view('admin.orders.thermal', compact('order'));
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

    private function applySourceFilter($query, string $source): void
    {
        if ($source === 'walk-in') {
            $query->whereNotNull('cashier_id');
            return;
        }

        if ($source === 'qr') {
            $query->whereNull('cashier_id');
        }
    }

    private function applyMenuFilter($query, $menuItemId): void
    {
        if (empty($menuItemId)) {
            return;
        }

        $query->whereHas('items', function ($itemQuery) use ($menuItemId) {
            $itemQuery->where('menu_item_id', $menuItemId);
        });
    }

    private function applyDateFilter($query, Request $request, string $period): void
    {
        if ($period === 'today') {
            $query->whereDate('created_at', today());
            return;
        }

        if ($period === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            return;
        }

        if ($period === 'month') {
            $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
            return;
        }

        if ($period === 'custom') {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }
    }
}
