@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <a href="{{ route('admin.orders.report', ['period' => 'today']) }}" class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 p-5 border border-slate-100 group block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-shopping-bag text-orange-600"></i>
                </div>
                <i class="fas fa-arrow-up-right-from-square text-slate-300 text-xs"></i>
            </div>
            <p class="text-slate-500 text-sm">Pesanan Hari Ini</p>
            <h3 class="text-3xl font-bold text-navy-900 mt-1">{{ $todayOrders }}</h3>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 p-5 border border-slate-100 group block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-yellow-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <i class="fas fa-arrow-up-right-from-square text-slate-300 text-xs"></i>
            </div>
            <p class="text-slate-500 text-sm">Pesanan Pending</p>
            <h3 class="text-3xl font-bold text-yellow-600 mt-1">{{ $pendingOrders }}</h3>
        </a>

        <a href="{{ route('admin.orders.report', ['period' => 'today']) }}" class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 p-5 border border-slate-100 group block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                <i class="fas fa-arrow-up-right-from-square text-slate-300 text-xs"></i>
            </div>
            <p class="text-slate-500 text-sm">Pendapatan Hari Ini</p>
            <h3 class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format((float) $todayRevenue, 0, ',', '.') }}</h3>
        </a>

        <a href="{{ route('admin.menus.index') }}" class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 p-5 border border-slate-100 group block">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fas fa-utensils text-blue-600"></i>
                </div>
                <i class="fas fa-arrow-up-right-from-square text-slate-300 text-xs"></i>
            </div>
            <p class="text-slate-500 text-sm">Total Menu Aktif</p>
            <h3 class="text-3xl font-bold text-blue-600 mt-1">{{ $totalMenuItems }}</h3>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <h3 class="font-bold text-navy-900 text-lg">Riwayat Pesanan Masuk</h3>
                <p class="text-slate-500 text-sm">Klik ikon/filter untuk langsung ke detail fitur yang dipilih.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.orders.report', ['source' => 'all', 'period' => 'today']) }}" class="px-3 py-2 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">Keseluruhan</a>
                <a href="{{ route('admin.orders.report', ['source' => 'walk-in', 'period' => 'today']) }}" class="px-3 py-2 text-xs font-semibold rounded-lg bg-orange-100 text-orange-700 hover:bg-orange-200">Walk-in</a>
                <a href="{{ route('admin.orders.report', ['source' => 'qr', 'period' => 'today']) }}" class="px-3 py-2 text-xs font-semibold rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200">QR Code</a>
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 text-xs font-semibold rounded-lg bg-navy-900 text-white hover:bg-navy-800">Detail Pesanan</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Sumber</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-mono text-sm font-semibold text-navy-900">{{ $order->order_number }}</p>
                            <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @php $isWalkIn = !is_null($order->cashier_id); @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $isWalkIn ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $isWalkIn ? 'Walk-in' : 'QR Code' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700">{{ $order->full_location }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-navy-900">{{ $order->formatted_total }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @switch($order->status)
                                    @case('pending') bg-yellow-50 text-yellow-700 border border-yellow-100 @break
                                    @case('confirmed') bg-blue-50 text-blue-700 border border-blue-100 @break
                                    @case('processing') bg-indigo-50 text-indigo-700 border border-indigo-100 @break
                                    @case('delivering') bg-purple-50 text-purple-700 border border-purple-100 @break
                                    @case('completed') bg-green-50 text-green-700 border border-green-100 @break
                                    @case('cancelled') bg-red-50 text-red-700 border border-red-100 @break
                                    @default bg-slate-100 text-slate-700 border border-slate-200
                                @endswitch
                            ">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-navy-900 hover:underline">Lihat Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500">Belum ada riwayat pesanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-gradient-to-br from-navy-800 to-navy-900 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold">Laporan Pesanan</h3>
                <p class="text-white/70 text-sm mt-1">Fitur laporan diposisikan di bagian bawah dashboard sesuai alur review harian.</p>
            </div>
            <a href="{{ route('admin.orders.report') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-navy-900 text-sm font-semibold hover:bg-slate-100 transition">
                Buka Laporan Lengkap
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
            <a href="{{ route('admin.orders.report', ['source' => 'all']) }}" class="bg-white/10 rounded-xl p-4 hover:bg-white/15 transition block">
                <p class="text-xs uppercase text-white/60">Total Pesanan</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($totalOrders) }}</p>
            </a>
            <a href="{{ route('admin.orders.report', ['source' => 'walk-in']) }}" class="bg-white/10 rounded-xl p-4 hover:bg-white/15 transition block">
                <p class="text-xs uppercase text-white/60">Walk-in</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($walkInOrders) }}</p>
            </a>
            <a href="{{ route('admin.orders.report', ['source' => 'qr']) }}" class="bg-white/10 rounded-xl p-4 hover:bg-white/15 transition block">
                <p class="text-xs uppercase text-white/60">QR Code</p>
                <p class="text-2xl font-bold mt-1">{{ number_format($qrOrders) }}</p>
            </a>
        </div>

        <div class="mt-5 text-sm text-white/70">
            Pendapatan bulan ini: <span class="font-semibold text-white">Rp {{ number_format((float) $monthlyRevenue, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection
