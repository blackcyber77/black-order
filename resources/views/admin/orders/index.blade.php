@extends('layouts.admin')

@section('title', 'Semua Pesanan')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold text-navy">Daftar Pesanan</h2>
    
    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex gap-2 flex-wrap">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
            <option value="delivering" {{ request('status') == 'delivering' ? 'selected' : '' }}>Diantar</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <select name="payment_method" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Semua Metode</option>
            <option value="tunai" {{ request('payment_method') == 'tunai' ? 'selected' : '' }}>Tunai</option>
            <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transfer</option>
        </select>
        <select name="cashier_id" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg text-sm">
            <option value="">Semua Kasir</option>
            @foreach($cashiers as $cashier)
            <option value="{{ $cashier->id }}" {{ (string) request('cashier_id') === (string) $cashier->id ? 'selected' : '' }}>
                {{ $cashier->name }}
            </option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="px-4 py-2 border rounded-lg">
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pesanan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($orders as $order)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-navy">{{ $order->order_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->customer_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->cashier?->name ?? '-' }}</td>
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
                        {{-- Payment method badge --}}
                        <span class="px-2 py-0.5 text-xs rounded-full font-semibold
                            @if($order->payment_method === 'tunai') bg-green-100 text-green-700
                            @elseif($order->payment_method === 'qris') bg-blue-100 text-blue-700
                            @else bg-purple-100 text-purple-700
                            @endif
                        ">{{ $order->payment_method_label }}</span>

                        {{-- Payment status --}}
                        @if($order->payment_status === 'verified')
                            <i class="fas fa-check-circle text-green-500 text-xs" title="Terverifikasi"></i>
                        @elseif($order->payment_status === 'paid')
                            <i class="fas fa-check text-blue-500 text-xs" title="Dibayar"></i>
                        @elseif($order->payment_status === 'pending' && $order->isCashless())
                            <form action="{{ route('admin.orders.verify', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 hover:underline text-xs font-semibold ml-1" title="Verifikasi pembayaran">
                                    Verifikasi
                                </button>
                            </form>
                        @endif
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-navy hover:underline">Detail</a>
                    <a href="{{ route('admin.orders.thermal-print', ['order' => $order->id, 'autoprint' => 1]) }}" target="_blank" class="text-blue-600 hover:underline ml-3">Print</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada pesanan</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->appends(request()->query())->links() }}
</div>
@endsection
