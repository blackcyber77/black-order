@extends('layouts.admin')

@section('title', 'Semua Pesanan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold text-navy">Daftar Pesanan</h2>
    
    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. order / pelanggan / telepon" class="px-4 py-2 border rounded-lg text-sm min-w-[220px]">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
            <option value="delivering" {{ request('status') == 'delivering' ? 'selected' : '' }}>Diantar</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <select name="payment_status" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Semua Status Bayar</option>
            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="verified" {{ request('payment_status') == 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="expired" {{ request('payment_status') == 'expired' ? 'selected' : '' }}>Expired</option>
        </select>
        <select name="payment_method" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Semua Metode</option>
            <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transfer</option>
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 border rounded-lg">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2 border rounded-lg">
        <button type="submit" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 border rounded-lg text-sm">Reset</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pesanan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meja</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gateway Ref</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-navy">{{ $order->order_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->customer_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $order->full_location }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-orange">{{ $order->formatted_total }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full
                        @switch($order->status)
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('completed') bg-green-100 text-green-800 @break
                            @case('cancelled') bg-red-100 text-red-800 @break
                            @default bg-blue-100 text-blue-800
                        @endswitch
                    ">{{ $order->status_label }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="px-2 py-0.5 text-xs rounded-full font-semibold
                            @if($order->payment_method === 'qris') bg-blue-100 text-blue-700
                            @else bg-purple-100 text-purple-700
                            @endif
                        ">{{ $order->payment_method_label }}</span>
                        <span class="text-xs font-medium">{{ $order->payment_status_label }}</span>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-600">
                    {{ $order->payment_gateway_ref ?: '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-navy hover:underline">Detail</a>
                    <a href="{{ route('admin.orders.thermal-print', ['order' => $order->id, 'autoprint' => 1]) }}" target="_blank" class="text-blue-600 hover:underline ml-3">Print</a>
                    @if(in_array($order->payment_status, ['pending', 'failed', 'expired']) && $order->payment_method === 'qris')
                        <a href="{{ route('payment.create', $order) }}" target="_blank" class="text-orange-600 hover:underline ml-3">Bayar Ulang</a>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-8 text-center text-gray-500">Belum ada pesanan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->appends(request()->query())->links() }}
</div>
@endsection
