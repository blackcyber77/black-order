<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Kantin Industri Batang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            800: '#1E293B',
                            900: '#0F172A',
                        },
                        orange: {
                            500: '#F97316',
                            600: '#EA580C',
                        }
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-slate-800 pb-20 md:pb-0">

    <!-- Modern Header -->
    <header class="fixed w-full top-0 z-50 glass-nav transition-all duration-300" id="main-header">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-orange-500/30">
                        <i class="fas fa-utensils text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-navy-900 tracking-tight leading-none">Order KITB</h1>
                        <p class="text-[10px] text-slate-500 font-medium uppercase tracking-widest">Kantin Industri Batang</p>
                    </div>
                </div>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex gap-8 items-center">
                    <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-orange-600' : 'text-slate-600 hover:text-navy-900' }} transition">Menu</a>
                    <a href="{{ route('orders.track') }}" class="text-sm font-medium {{ request()->routeIs('orders.track') ? 'text-orange-600' : 'text-slate-600 hover:text-navy-900' }} transition">Lacak Pesanan</a>
                    
                    <a href="{{ route('cart.index') }}" class="relative group">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-orange-50 group-hover:text-orange-600 transition">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white {{ session('cart') ? '' : 'hidden' }}">
                            {{ count(session('cart', [])) }}
                        </span>
                    </a>
                </nav>

                <!-- Mobile Cart Button -->
                <div class="md:hidden">
                    <a href="{{ route('cart.index') }}" class="relative inline-block">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <span id="mobile-cart-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white {{ session('cart') ? '' : 'hidden' }}">
                            {{ count(session('cart', [])) }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-24 min-h-screen">
        @yield('content')
    </main>

    <!-- Footer Removed -->

    <!-- Mobile Bottom Nav -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t px-6 py-3 flex justify-between items-center z-50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('home') ? 'text-orange-600' : 'text-slate-400' }}">
            <i class="fas fa-utensils text-xl"></i>
            <span class="text-[10px] font-medium">Menu</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('cart.index') ? 'text-orange-600' : 'text-slate-400' }}">
            <div class="relative">
                <i class="fas fa-shopping-bag text-xl"></i>
                <span id="bottom-cart-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white {{ session('cart') ? '' : 'hidden' }}">
                    {{ count(session('cart', [])) }}
                </span>
            </div>
            <span class="text-[10px] font-medium">Keranjang</span>
        </a>
        <a href="{{ route('orders.track') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('orders.track') ? 'text-orange-600' : 'text-slate-400' }}">
            <i class="fas fa-clock text-xl"></i>
            <span class="text-[10px] font-medium">Pesanan</span>
        </a>
        <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-slate-400">
            <i class="fas fa-user text-xl"></i>
            <span class="text-[10px] font-medium">Akun</span>
        </a>
    </div>

    @stack('scripts')
    <script>
        // Update Floating Badges logic if needed
        function updateCartBadge(count) {
            const badges = ['cart-badge', 'mobile-cart-badge', 'bottom-cart-badge'];
            badges.forEach(id => {
                const badge = document.getElementById(id);
                if (badge) {
                    badge.textContent = count;
                    if (count > 0) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            });
        }
    </script>
</body>
</html>
