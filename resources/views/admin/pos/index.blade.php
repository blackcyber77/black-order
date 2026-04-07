@extends('layouts.admin')

@section('title', 'Daftar Pesanan Aktif')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-bold text-navy-900">Pesanan Aktif</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola pesanan dari meja pelanggan atau walk-in kasir.</p>
        </div>
        <a href="{{ route('admin.pos.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-xl font-bold transition shadow-lg shadow-orange-500/30 flex items-center gap-2">
            <i class="fas fa-plus"></i> Kasir Manual
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($orders as $order)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group relative {{ $order->payment_status !== 'paid' ? 'ring-2 ring-red-100' : '' }}">
        <!-- Header -->
        <div class="p-4 bg-slate-50 border-b border-gray-100 flex justify-between items-center relative overflow-hidden">
            <div>
                <h3 class="font-bold text-navy-900 text-lg">{{ $order->order_number }}</h3>
                <p class="text-xs font-semibold text-gray-500 mt-1">
                    @if($order->table_number)
                        <i class="fas fa-chair text-orange-500 mr-1"></i> Meja {{ $order->table_number }}
                    @else
                        <i class="fas fa-walking text-blue-500 mr-1"></i> Walk-In / Takeaway
                    @endif
                </p>
            </div>
            <div class="text-right flex flex-col items-end gap-2">
                @if($order->isHeld())
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-700">
                    HOLD
                </span>
                @else
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-indigo-100 text-indigo-700">
                    AKTIF
                </span>
                @endif
                <span class="px-3 py-1 text-xs font-bold rounded-full 
                    {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM DIBAYAR' }}
                </span>
                <span class="text-[10px] text-gray-400 font-mono">{{ $order->created_at->format('d M H:i') }}</span>
            </div>
        </div>

        <!-- Body -->
        <div class="p-4 flex-1">
            <div class="space-y-3 mb-4 max-h-[150px] overflow-y-auto pr-1">
                @foreach($order->items as $item)
                <div class="flex justify-between items-start text-sm border-b border-dashed border-gray-100 pb-2">
                    <span class="text-gray-700 font-medium pr-2">{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                    <span class="text-gray-900 break-keep">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <div class="bg-orange-50/50 p-3 rounded-lg border border-orange-100">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-navy-900">Total Tagihan</span>
                    <span class="text-lg font-bold text-orange-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mt-1 text-xs text-gray-500">
                    <span>Metode</span>
                    <span class="uppercase font-semibold">{{ $order->payment_method_label }}</span>
                </div>
                @if($order->cashier)
                <div class="flex justify-between items-center mt-1 text-xs text-gray-500">
                    <span>Kasir</span>
                    <span class="font-semibold">{{ $order->cashier->name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 border-t border-gray-100 bg-gray-50 flex flex-wrap gap-2">
            @if(!$order->isHeld() && $order->status !== 'completed' && $order->status !== 'cancelled')
            <form action="{{ route('admin.pos.hold', $order->id) }}" method="POST" class="flex-1 min-w-[48%]">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-white py-2 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-pause-circle"></i> Hold
                </button>
            </form>
            @endif

            @if($order->isHeld() && $order->status !== 'completed' && $order->status !== 'cancelled')
            <form action="{{ route('admin.pos.recall', $order->id) }}" method="POST" class="flex-1 min-w-[48%]">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full bg-indigo-500 hover:bg-indigo-600 text-white py-2 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-play-circle"></i> Recall
                </button>
            </form>
            @endif

            @if($order->payment_status !== 'paid' || $order->status !== 'completed')
            <form action="{{ route('admin.pos.pay', $order->id) }}" method="POST" class="flex-1 min-w-[48%]" onsubmit="return confirm('Tandai pesanan ini LUNAS, selesai, dan kosongkan meja?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm shadow-green-500/30">
                    <i class="fas fa-check-double"></i> Tandai Selesai
                </button>
            </form>
            @endif

            @if($order->status !== 'completed' && $order->status !== 'cancelled')
            <form action="{{ route('admin.pos.void', $order->id) }}" method="POST" class="flex-1 min-w-[48%]" onsubmit="return promptVoidReason(this)">
                @csrf
                @method('PATCH')
                <input type="hidden" name="void_reason" value="">
                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-xl text-sm font-bold transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-ban"></i> Void
                </button>
            </form>
            @endif
            
            <a href="{{ route('admin.pos.receipt', $order->id) }}" target="_blank" class="bg-navy-900 hover:bg-navy-800 text-white w-12 flex items-center justify-center rounded-xl transition shadow-sm" title="Cetak Struk">
                <i class="fas fa-print"></i>
            </a>
            
            <a href="{{ route('admin.orders.show', $order->id) }}" class="bg-white border border-gray-200 text-gray-600 hover:text-navy-900 hover:bg-gray-100 w-12 flex items-center justify-center rounded-xl transition shadow-sm" title="Detail Pesanan">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-white">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 rounded-full mb-4">
            <i class="fas fa-glass-cheers text-gray-300 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Semua Meja Bersih!</h3>
        <p class="text-gray-500">Saat ini tidak ada pesanan aktif yang perlu diproses.</p>
    </div>
    @endforelse
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>

<script>
function promptVoidReason(form) {
    const reason = prompt('Masukkan alasan void pesanan:');
    if (!reason || reason.trim().length < 3) {
        alert('Alasan void minimal 3 karakter.');
        return false;
    }

    form.querySelector('input[name="void_reason"]').value = reason.trim();
    return true;
}
</script>
@endsection
