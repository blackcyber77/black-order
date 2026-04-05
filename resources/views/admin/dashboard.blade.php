@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    <!-- Stat Card 2 -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-slate-100 group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-shopping-bag text-orange-600 text-xl"></i>
            </div>
            @if($pendingOrders > 0)
            <span class="text-xs font-semibold px-2.5 py-1 bg-orange-50 text-orange-600 rounded-lg border border-orange-100 animate-pulse">
                {{ $pendingOrders }} baru
            </span>
            @endif
        </div>
        <div>
            <p class="text-slate-500 text-sm font-medium mb-1">Pesanan Hari Ini</p>
            <h3 class="text-3xl font-bold text-navy-900">{{ $todayOrders }}</h3>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-slate-100 group">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-line text-green-600 text-xl"></i>
            </div>
            <span class="text-xs text-slate-400">Hari ini</span>
        </div>
        <div>
            <p class="text-slate-500 text-sm font-medium mb-1">Pendapatan</p>
            <h3 class="text-3xl font-bold text-green-600 tracking-tight">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>



<!-- Secondary Stats -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-gradient-to-br from-navy-800 to-navy-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
        <h3 class="font-medium text-white/70 mb-2">Total Pendapatan Bulan Ini</h3>
        <p class="text-4xl font-bold tracking-tight mb-4">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</p>
        <div class="flex items-center gap-2 text-sm text-white/50">
            <i class="fas fa-check-circle"></i> Termasuk biaya layanan aplikasi
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-center">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-2xl font-bold">
                {{ $totalMenuItems }}
            </div>
            <div>
                <h3 class="font-bold text-navy-900 text-lg">Total Menu Terdaftar</h3>
                <p class="text-slate-500 text-sm">Menu aktif yang terdaftar</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
        <div>
            <h3 class="font-bold text-navy-900 text-lg">Pesanan Terbaru</h3>
            <p class="text-slate-500 text-sm">5 transaksi terakhir yang masuk</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-orange-600 hover:text-orange-700 flex items-center gap-1 transition">
            Lihat Semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50/50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Order ID</th>

                    <th class="px-8 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Lokasi</th>
                    <th class="px-8 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Total</th>
                    <th class="px-8 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($recentOrders as $order)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-4 whitespace-nowrap">
                        <span class="font-mono text-sm font-medium text-navy-900">{{ $order->order_number }}</span>
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap text-sm text-slate-600">
                        <span class="font-medium text-navy-900">{{ $order->full_location }}</span>
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap text-sm font-bold text-navy-900">
                        {{ $order->formatted_total }}
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                            @switch($order->status)
                                @case('pending') bg-yellow-50 text-yellow-700 border border-yellow-100 @break
                                @case('confirmed') bg-blue-50 text-blue-700 border border-blue-100 @break
                                @case('processing') bg-indigo-50 text-indigo-700 border border-indigo-100 @break
                                @case('delivering') bg-purple-50 text-purple-700 border border-purple-100 @break
                                @case('completed') bg-green-50 text-green-700 border border-green-100 @break
                                @case('cancelled') bg-red-50 text-red-700 border border-red-100 @break
                            @endswitch
                        ">
                            {{ $order->status_label }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-12 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-inbox text-4xl text-slate-200 mb-3"></i>
                            <p>Belum ada pesanan terbaru</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
