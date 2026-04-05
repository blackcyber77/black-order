@extends('layouts.admin')

@section('title', 'POS Kasir')

@section('content')
<!-- We use Alpine.js for Cart Logic on this view -->
<div class="h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-6" x-data="posSystem()">
    
    <!-- Left: Menu Selection -->
    <div class="flex-1 flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Search & Filters -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/80 flex gap-2">
            <div class="relative flex-1">
                <input type="text" x-model="searchQuery" placeholder="Cari menu..." 
                    class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <select x-model="selectedCategory" class="px-4 py-2 text-sm rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-orange-500/20">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Menu Grid -->
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($menuItems as $item)
                <div x-show="(selectedCategory === '' || '{{ $item->category_id }}' == selectedCategory) && ('{{ strtolower($item->name) }}'.includes(searchQuery.toLowerCase()))"
                     class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden cursor-pointer hover:border-orange-300 hover:shadow-md transition group flex flex-col"
                     @click="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})">
                    <div class="relative aspect-[4/3] bg-gray-100">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-utensils"></i></div>
                        @endif
                    </div>
                    <div class="p-3 flex-1 flex flex-col justify-between">
                        <p class="font-bold text-navy-900 text-sm leading-tight mb-2 line-clamp-2">{{ $item->name }}</p>
                        <p class="font-bold text-orange-600 text-sm">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right: Cart & Checkout -->
    <div class="w-full lg:w-[400px] flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Cart Header -->
        <div class="p-4 bg-navy-900 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg"><i class="fas fa-shopping-cart mr-2 opacity-70"></i> Pesanan Baru</h3>
            <button @click="clearCart()" class="text-sm px-3 py-1 bg-white/10 hover:bg-white/20 rounded-lg transition text-red-300">Clear</button>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-gray-400 space-y-3">
                    <i class="fas fa-basket-shopping text-4xl"></i>
                    <p class="text-sm">Pilih menu di samping</p>
                </div>
            </template>
            
            <template x-for="(item, index) in cart" :key="index">
                <div class="flex gap-3 items-center p-2 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-navy-900 truncate" x-text="item.name"></p>
                        <p class="text-xs font-semibold text-orange-600" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></p>
                    </div>
                    <div class="flex items-center gap-2 bg-white rounded-lg border border-gray-200 p-1 shadow-sm">
                        <button @click="updateQty(index, -1)" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-orange-600 rounded bg-gray-50 hover:bg-white"><i class="fas fa-minus text-[10px]"></i></button>
                        <span class="w-6 text-center text-xs font-bold text-navy-900" x-text="item.quantity"></span>
                        <button @click="updateQty(index, 1)" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-orange-600 rounded bg-gray-50 hover:bg-white"><i class="fas fa-plus text-[10px]"></i></button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Checkout Form -->
        <div class="p-4 border-t border-gray-100 bg-gray-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <form action="{{ route('admin.pos.store') }}" method="POST" id="posForm">
                @csrf
                <input type="hidden" name="order_items" :value="JSON.stringify(cart)">
                
                <div class="mb-3">
                    <input type="text" name="customer_name" required placeholder="Nama Pemesan (Wajib)" class="w-full px-4 py-2 text-sm rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500/20 shadow-sm">
                </div>

                <div class="grid grid-cols-1 gap-3 mb-4">
                    <select name="table_number" x-model="selectedTable" class="px-3 py-2 text-sm rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-500/20 shadow-sm">
                        <option value="">Pilih Meja</option>
                        <template x-for="table in tables" :key="table.id">
                            <option :value="table.table_number" x-text="'Meja ' + table.table_number + (table.status == 'terisi' ? ' (Terisi)' : '')"></option>
                        </template>
                    </select>
                </div>

                <!-- Payment Methods: Tunai (walk-in) + QRIS -->
                <div class="flex gap-2 mb-4">
                    <label class="flex-1 relative cursor-pointer group">
                        <input type="radio" name="payment_method" value="tunai" class="peer sr-only" checked>
                        <div class="text-center p-2 rounded-xl border border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 text-gray-500 shadow-sm transition">
                            <i class="fas fa-money-bill-wave mb-1"></i>
                            <div class="text-xs font-bold">TUNAI</div>
                        </div>
                    </label>
                    <label class="flex-1 relative cursor-pointer group">
                        <input type="radio" name="payment_method" value="qris" class="peer sr-only">
                        <div class="text-center p-2 rounded-xl border border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 text-gray-500 shadow-sm transition">
                            <i class="fas fa-qrcode mb-1"></i>
                            <div class="text-xs font-bold">QRIS</div>
                        </div>
                    </label>
                </div>

                <div class="flex justify-between items-center mb-4">
                    <div class="text-sm text-gray-500">
                        <p>Total Item: <span class="font-bold text-navy-900" x-text="totalItems"></span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-orange-600 leading-none" x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></p>
                    </div>
                </div>

                <button type="submit" :disabled="cart.length === 0" class="w-full bg-navy-900 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-navy-900/20 hover:bg-navy-800 disabled:opacity-50 disabled:cursor-not-allowed transition flex justify-center items-center gap-2">
                    <i class="fas fa-check-circle"></i> Bayar & Cetak
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('posSystem', () => ({
            searchQuery: '',
            selectedCategory: '',
            cart: [],
            selectedTable: '',
            serviceFee: {{ \App\Models\Setting::getServiceFee() }},
            tables: @json($tablesData),

            addToCart(id, name, price) {
                const existing = this.cart.find(i => i.id === id);
                if(existing) {
                    existing.quantity++;
                    existing.subtotal = existing.quantity * existing.price;
                } else {
                    this.cart.unshift({ id, name, price, quantity: 1, subtotal: price });
                }
            },

            updateQty(index, change) {
                const item = this.cart[index];
                item.quantity += change;
                if(item.quantity <= 0) {
                    this.cart.splice(index, 1);
                } else {
                    item.subtotal = item.quantity * item.price;
                }
            },

            clearCart() {
                if(confirm('Kosongkan pesanan saat ini?')) {
                    this.cart = [];
                    this.selectedTable = '';
                }
            },

            get subtotals() {
                return this.cart.reduce((sum, item) => sum + item.subtotal, 0);
            },

            get totalItems() {
                return this.cart.reduce((sum, item) => sum + item.quantity, 0);
            },

            get grandTotal() {
                if(this.cart.length === 0) return 0;
                return this.subtotals + this.serviceFee;
            }
        }))
    });
</script>
@endpush
@endsection
