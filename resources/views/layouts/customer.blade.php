<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Kantin Industri Batang</title>
    <meta name="theme-color" content="#141413">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="OrderKITB">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Source+Serif+4:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'Arial', 'sans-serif'],
                        serif: ['Source Serif 4', 'Georgia', 'serif'],
                    },
                    colors: {
                        parchment: '#F5F4ED',
                        ivory: '#FAF9F5',
                        navy: {
                            800: '#30302E',
                            900: '#141413',
                        },
                        orange: {
                            500: '#C96442',
                            600: '#B85A3D',
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
        :root {
            --paper: #f5f4ed;
            --ivory: #faf9f5;
            --near-black: #141413;
            --terracotta: #c96442;
            --terracotta-dark: #b85a3d;
            --border-cream: #f0eee6;
            --border-warm: #e8e6dc;
            --text-secondary: #5e5d59;
        }
        .glass-nav {
            background: rgba(245, 244, 237, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-warm);
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        body {
            background: var(--paper);
            color: var(--near-black);
        }
        h1, h2, h3, .font-bold {
            font-family: "Source Serif 4", Georgia, serif;
        }
        .bg-white {
            background: var(--ivory) !important;
            border-color: var(--border-cream) !important;
        }
        .text-slate-500, .text-slate-600, .text-gray-500, .text-gray-600 {
            color: var(--text-secondary) !important;
        }
        .bg-orange-500, .bg-orange-600 {
            background-color: var(--terracotta) !important;
        }
        .hover\:bg-orange-600:hover, .hover\:bg-orange-700:hover {
            background-color: var(--terracotta-dark) !important;
        }
        .text-orange-600, .text-orange {
            color: var(--terracotta) !important;
        }
        .border, .border-gray-100, .border-gray-200, .border-slate-100, .border-slate-200 {
            border-color: var(--border-cream) !important;
        }
        .bg-slate-100, .bg-gray-100, .bg-gray-50 {
            background: #f0eee6 !important;
        }
    </style>
</head>
<body class="font-sans pb-20 md:pb-0">

    <!-- Modern Header -->
    <header class="fixed w-full top-0 z-50 glass-nav transition-all duration-300" id="main-header">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg">
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
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function () {});
            });
        }

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
