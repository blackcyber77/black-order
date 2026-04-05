<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\MenuItem;

class DashboardController extends Controller
{
    public function index()
    {
        $todayOrders = Order::today()->count();
        $todayRevenue = Order::today()
            ->where('status', 'completed')
            ->sum('total');

        $pendingOrders = Order::whereIn('status', ['pending', 'confirmed', 'processing', 'delivering'])->count();

        $monthlyRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total');

        $recentOrders = Order::query()
            ->latest()
            ->take(10)
            ->get();

        $totalMenuItems = MenuItem::count();

        return view('admin.dashboard', compact(
            'todayOrders',
            'todayRevenue',
            'pendingOrders',
            'monthlyRevenue',
            'recentOrders',
            'totalMenuItems'
        ));
    }
}
