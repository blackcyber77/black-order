@extends('layouts.customer')

@section('title', 'asu')

@section('content')
    <!-- Hero Section -->
    <div
        class="bg-gradient-to-br from-navy-900 via-navy-800 to-slate-900 text-white min-h-[300px] flex items-center relative overflow-hidden -mt-24 pt-24 pb-12">
        <!-- Abstract Shapes -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-orange-500/20 rounded-full blur-3xl -mr-32 -mt-32 animate-float">
        </div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -ml-20 -mb-20"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-2xl">
                <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">Lapar? <br><span class="text-orange-500">Pesan
                        Sekarang</span>, Diantar Langsung.</h1>
                <p class="text-slate-300 text-lg mb-8">Nikmati berbagai pilihan kuliner terbaik dari tenant kami, pesan
                    langsung dari meja Anda.</p>

                <!-- Table Selector -->
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-2 rounded-2xl flex gap-2 max-w-md">
                    <div class="flex-1 bg-white rounded-xl flex items-center px-4 py-2">
                        <i class="fas fa-building text-orange-500 mr-3"></i>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Lokasi Anda</span>
                            <div class="font-bold text-navy-900 text-sm">
                                @if($tableNumber)
                                    Meja {{ $tableNumber }}
                                @else
                                    Scan QR Meja
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(!$tableNumber)
                        <button onclick="openScanner()"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 rounded-xl font-bold transition shadow-lg shadow-orange-500/30">
                            <i class="fas fa-qrcode mr-2"></i> Scan
                        </button>
                    @else
                        <a href="{{ route('location.clear') }}"
                            class="bg-red-50 hover:bg-red-100 text-red-500 px-4 rounded-xl font-bold transition flex items-center justify-center border border-red-200">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Menu Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Search & Filters -->
        <div class="sticky top-20 z-30 bg-gray-50/95 backdrop-blur-sm py-4 -mx-4 px-4 mb-6 border-b border-gray-200/50">
            <form action="{{ route('home') }}" method="GET" class="flex flex-col md:flex-row gap-4 max-w-4xl mx-auto">
                @if(request('table'))
                    <input type="hidden" name="table" value="{{ request('table') }}">
                @endif

                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari makanan atau minuman..."
                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 shadow-sm transition-all">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 hide-scrollbar">
                    <select name="category" onchange="this.form.submit()"
                        class="px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500/20 bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>


                </div>
            </form>
        </div>

        <!-- Menu Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            @forelse($menuItems as $item)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full">
                    <!-- Image -->
                    <div class="relative aspect-[4/3] overflow-hidden">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <i class="fas fa-utensils text-gray-300 text-3xl"></i>
                            </div>
                        @endif
                        <div class="absolute top-2 right-2">
                            <span
                                class="bg-white/90 backdrop-blur text-[10px] font-bold px-2 py-1 rounded-full text-navy-900 shadow-sm border border-white/50">
                                {{ $item->category->icon }} {{ $item->category->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-2">
                            <h3 class="font-bold text-navy-900 leading-tight mb-1 line-clamp-1">{{ $item->name }}</h3>

                        </div>

                        <p class="text-xs text-gray-600 mb-4 line-clamp-2 min-h-[2.5em]">{{ $item->description }}</p>

                        <div class="mt-auto flex items-center justify-between gap-3">
                            <span class="font-bold text-lg text-navy-900">{{ $item->formatted_price }}</span>
                            <button onclick="addToCart({{ $item->id }})"
                                class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center hover:bg-orange-600 hover:text-white transition-all transform active:scale-95 shadow-sm">
                                <i class="fas fa-plus text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-search text-gray-300 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Menu Tidak Ditemukan</h3>
                    <p class="text-gray-500">Coba ubah kata kunci pencarian atau filter kategori Anda.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick View / Cart Toast (Placeholder for JS logic) -->
    <div id="toast-container" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 pointer-events-none"></div>

    <!-- QR Scanner Modal -->
    <div id="scanner-modal" class="fixed inset-0 z-[60] bg-black/90 hidden flex flex-col items-center justify-center p-4">
        <div class="relative w-full max-w-sm bg-white rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-4 bg-navy-900 text-white flex justify-between items-center">
                <h3 class="font-bold">Scan QR Code Meja</h3>
                <button onclick="closeScanner()" class="text-white hover:text-orange-500 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-0 bg-black">
                <div id="reader" class="w-full h-[300px]"></div>
            </div>
            <div class="p-4 bg-white text-center">
                <p class="text-sm text-gray-500 mb-2">Arahkan kamera ke QR Code yang ada di meja.</p>
                <div id="scan-error" class="text-xs text-red-500 hidden"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            let html5QrcodeScanner = null;

            function openScanner() {
                const modal = document.getElementById('scanner-modal');
                modal.classList.remove('hidden');

                if (!html5QrcodeScanner) {
                    html5QrcodeScanner = new Html5Qrcode("reader");
                }

                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
                    .catch(err => {
                        console.error("Error starting scanner", err);
                        document.getElementById('scan-error').textContent = "Gagal membuka kamera: " + err;
                        document.getElementById('scan-error').classList.remove('hidden');
                    });
            }

            function closeScanner() {
                // Force hide modal immediately for better UX
                document.getElementById('scanner-modal').classList.add('hidden');
                document.getElementById('scan-error').classList.add('hidden');

                if (html5QrcodeScanner) {
                    try {
                        html5QrcodeScanner.stop().then(() => {
                            html5QrcodeScanner.clear();
                        }).catch(err => {
                            console.warn("Scanner failed to stop (likely not running):", err);
                            html5QrcodeScanner.clear();
                        });
                    } catch (e) {
                        console.error("Scanner error:", e);
                    }
                }
            }

            function onScanSuccess(decodedText, decodedResult) {
                // Handle the scanned code
                console.log(`Scan result: ${decodedText}`);

                // Stop scanning
                html5QrcodeScanner.stop().then(() => {
                    document.getElementById('scanner-modal').classList.add('hidden');

                    // Redirect to the scanned URL
                    window.location.href = decodedText;
                }).catch(err => {
                    console.error("Failed to stop scanner", err);
                });
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning.
                // console.warn(`Code scan error = ${error}`);
            }

            function addToCart(itemId) {

                fetch(`/cart/add/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ quantity: 1 })
                })
                    .then(response => {
                        if (response.status === 403) { // Location required
                            return response.json().then(data => {
                                throw new Error(data.message || 'Silakan scan QR Code meja terlebih dahulu.');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            updateCartBadge(data.cart_count);
                            showToast('Menu berhasil ditambahkan!');
                        } else {
                            showToast(data.message || 'Gagal menambahkan ke keranjang', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(error.message, 'error');
                        // Optional: Trigger location modal here if error relates to missing location
                    });
            }

            function showToast(message) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = 'bg-navy-900 text-white px-6 py-3 rounded-full shadow-lg text-sm font-medium animate-fade-in-up flex items-center gap-3 backdrop-blur-md bg-opacity-90';
                toast.innerHTML = `<i class="fas fa-check-circle text-green-400"></i> ${message}`;

                container.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
        </script>
    @endpush
@endsection