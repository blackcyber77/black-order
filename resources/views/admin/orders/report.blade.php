@extends('layouts.admin')

@section('title', 'Laporan & Riwayat Pesanan')

@section('content')
<div class="mb-6">
    <h2 class="text-lg font-semibold text-navy-900">Laporan Pesanan</h2>
    <p class="text-sm text-gray-500 mt-1">Filter berdasarkan sumber pesanan, item menu, dan rentang waktu.</p>
</div>

<form method="GET" action="{{ route('admin.orders.report') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Sumber Pesanan</label>
            <select name="source" class="w-full px-3 py-2 border rounded-lg text-sm">
                <option value="all" {{ request('source', 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                <option value="walk-in" {{ request('source') === 'walk-in' ? 'selected' : '' }}>Walk-in (POS)</option>
                <option value="qr" {{ request('source') === 'qr' ? 'selected' : '' }}>QR Code</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Per Item</label>
            <select name="menu_item_id" class="w-full px-3 py-2 border rounded-lg text-sm">
                <option value="">Semua Item</option>
                @foreach($menuItems as $menu)
                <option value="{{ $menu->id }}" {{ (string) request('menu_item_id') === (string) $menu->id ? 'selected' : '' }}>
                    {{ $menu->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Range Waktu</label>
            <select name="period" id="period" class="w-full px-3 py-2 border rounded-lg text-sm">
                <option value="today" {{ request('period', 'today') === 'today' ? 'selected' : '' }}>Hari ini</option>
                <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Minggu ini</option>
                <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan ini</option>
                <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Custom</option>
            </select>
        </div>
        <div id="start-date-wrap">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 border rounded-lg text-sm">
        </div>
        <div id="end-date-wrap">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Selesai</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 border rounded-lg text-sm">
        </div>
    </div>
    <div class="mt-4 flex gap-2">
        <button type="submit" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm hover:bg-navy-800 transition">Terapkan Filter</button>
        <a href="{{ route('admin.orders.report') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">Reset</a>
    </div>
</form>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase text-gray-500">Total Pesanan</p>
        <p class="text-2xl font-bold text-navy-900 mt-1">{{ number_format($summary['total_orders']) }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase text-gray-500">Pesanan Walk-in</p>
        <p class="text-2xl font-bold text-orange-600 mt-1">{{ number_format($summary['walk_in_orders']) }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase text-gray-500">Pesanan QR Code</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($summary['qr_orders']) }}</p>
    </div>
    <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
        <p class="text-xs uppercase text-gray-500">Total Omzet</p>
        <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format((float) $summary['total_revenue'], 0, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pesanan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sumber</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $order)
            <tr>
                <td class="px-6 py-4 text-sm font-semibold text-navy-900">{{ $order->order_number }}</td>
                <td class="px-6 py-4 text-sm">
                    @php $isWalkIn = !is_null($order->cashier_id); @endphp
                    <span class="px-2 py-1 text-xs rounded-full {{ $isWalkIn ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $isWalkIn ? 'Walk-in' : 'QR Code' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">{{ $order->customer_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">
                    @foreach($order->items->take(2) as $item)
                        <div>{{ $item->menuItem?->name ?? 'Item' }} x{{ $item->quantity }}</div>
                    @endforeach
                    @if($order->items->count() > 2)
                        <div class="text-xs text-gray-500">+{{ $order->items->count() - 2 }} item lainnya</div>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm font-semibold text-green-700">{{ $order->formatted_total }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y H:i') }}</td>
                <td class="px-6 py-4 text-sm">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-navy-900 hover:underline">Detail</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada data pesanan untuk filter ini</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const periodSelect = document.getElementById('period');
        const startDateWrap = document.getElementById('start-date-wrap');
        const endDateWrap = document.getElementById('end-date-wrap');

        function toggleCustomDateInputs() {
            const isCustom = periodSelect.value === 'custom';
            startDateWrap.style.display = isCustom ? 'block' : 'none';
            endDateWrap.style.display = isCustom ? 'block' : 'none';
        }

        toggleCustomDateInputs();
        periodSelect.addEventListener('change', toggleCustomDateInputs);
    });
</script>
@endpush
