<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@hasSection('title')@yield('title') | @endif order sinom by z</title>
    <meta name="theme-color" content="#141413">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="OrderKITB">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('icons/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Nunito', 'Arial', 'sans-serif'],
                        serif: ['Nunito', 'Arial', 'sans-serif'],
                    },
                    colors: {
                        parchment: '#F5F4ED',
                        ivory: '#FAF9F5',
                        warm: {
                            100: '#F0EEE6',
                            200: '#E8E6DC',
                            400: '#B0AEA5',
                            500: '#87867F',
                            700: '#5E5D59',
                        },
                        navy: {
                            800: '#30302E',
                            900: '#141413',
                        },
                        orange: {
                            500: '#E89A48',
                            600: '#D98935',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --paper: #f5f4ed;
            --ivory: #faf9f5;
            --near-black: #141413;
            --dark-surface: #30302e;
            --terracotta: #e89a48;
            --terracotta-dark: #d98935;
            --border-cream: #f0eee6;
            --border-warm: #e8e6dc;
            --text-secondary: #5e5d59;
            --text-muted: #87867f;
        }
        .glass {
            background: rgba(245, 244, 237, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-warm);
        }
        .sidebar-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(232, 154, 72, 0.14);
            border-left: 4px solid var(--terracotta);
            padding-left: 1rem;
        }
        .sidebar-link i {
            width: 1.5rem;
            text-align: center;
        }
        body.sidebar-collapsed aside {
            width: 5rem;
        }
        body.sidebar-collapsed aside .brand-text,
        body.sidebar-collapsed aside .sidebar-section-label,
        body.sidebar-collapsed aside .sidebar-label {
            display: none;
        }
        body.sidebar-collapsed aside .sidebar-link {
            justify-content: center;
            border-left: none;
            padding-left: 1rem;
        }
        body.sidebar-collapsed aside .sidebar-link i {
            margin-right: 0 !important;
        }
        body.sidebar-collapsed aside .h-20 {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }
        .qr-notification-enter {
            animation: qrNotifSlideIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes qrNotifSlideIn {
            from { opacity: 0; transform: translateX(60px) scale(0.92); }
            to   { opacity: 1; transform: translateX(0) scale(1); }
        }
        .notif-pulse {
            animation: notifPulseGlow 2s ease-in-out infinite;
        }
        @keyframes notifPulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(232, 154, 72, 0.5); }
            50%       { box-shadow: 0 0 20px 6px rgba(232, 154, 72, 0.35); }
        }
        .notif-shake {
            animation: notifShake 0.6s ease-in-out;
        }
        @keyframes notifShake {
            0%, 100% { transform: translateX(0); }
            15%      { transform: translateX(-6px) rotate(-1deg); }
            30%      { transform: translateX(5px) rotate(1deg); }
            45%      { transform: translateX(-4px); }
            60%      { transform: translateX(3px); }
            75%      { transform: translateX(-2px); }
        }
        .notif-exit {
            animation: notifSlideOut 0.3s ease-in forwards;
        }
        @keyframes notifSlideOut {
            to { opacity: 0; transform: translateX(60px) scale(0.92); }
        }
        #order-notif-badge {
            animation: badgeBounce 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes badgeBounce {
            from { transform: scale(0); }
            to   { transform: scale(1); }
        }
        body {
            background: var(--paper);
            color: var(--near-black);
        }
        h1, h2, h3, .font-bold.text-navy-800, .font-bold.text-navy-900 {
            font-family: "Nunito", Arial, sans-serif;
            letter-spacing: 0;
        }
        main .bg-white {
            background: var(--ivory) !important;
            border: 1px solid var(--border-cream);
        }
        main .text-gray-500,
        main .text-slate-500,
        main .text-gray-600,
        main .text-slate-600 {
            color: var(--text-secondary) !important;
        }
        main .border,
        main .border-gray-100,
        main .border-gray-200,
        main .divide-y > * + * {
            border-color: var(--border-cream) !important;
        }
        main .bg-gray-50,
        main .bg-slate-50 {
            background: #f0eee6 !important;
        }
        main .shadow,
        main .shadow-sm,
        main .shadow-lg,
        main .shadow-xl {
            box-shadow: rgba(20, 20, 19, 0.05) 0 4px 24px !important;
        }
        button,
        .btn,
        input,
        select,
        textarea {
            border-radius: 12px;
        }
        input,
        select,
        textarea {
            border-color: var(--border-warm) !important;
            background: #fffdf8;
        }
        .bg-orange-600,
        .bg-orange-500 {
            background-color: var(--terracotta) !important;
        }
        .hover\:bg-orange-700:hover,
        .hover\:bg-orange-600:hover,
        .hover\:bg-orange-500:hover {
            background-color: var(--terracotta-dark) !important;
        }
        .text-orange-600,
        .text-orange {
            color: var(--terracotta) !important;
        }
        .bg-navy-900 {
            background-color: var(--near-black) !important;
        }
        .bg-navy-800 {
            background-color: var(--dark-surface) !important;
        }
        #printer-toggle-btn.connected {
            border-color: #2e7d32;
            color: #2e7d32;
            background: #eef8ef;
        }
        @media (max-width: 767px) {
            header.glass {
                height: 4rem;
                padding-left: 1rem;
                padding-right: 1rem;
            }
            header.glass h2 {
                font-size: 1.05rem;
                line-height: 1.2;
            }
            main {
                padding: 1rem !important;
            }
            main table.w-full {
                min-width: 760px;
            }
            main .overflow-hidden {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            main .flex.justify-between.items-center.mb-6 {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
            main form.flex.gap-2.flex-wrap,
            main form.bg-white.rounded-xl.shadow-sm.border.border-gray-100.p-4.mb-4.flex.flex-wrap.gap-3.items-center {
                width: 100%;
            }
            main form.flex.gap-2.flex-wrap > *,
            main form.bg-white.rounded-xl.shadow-sm.border.border-gray-100.p-4.mb-4.flex.flex-wrap.gap-3.items-center > * {
                width: 100%;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Sidebar -->
        <aside class="w-64 bg-navy-900 text-[#b0aea5] flex-shrink-0 hidden md:flex flex-col transition-all duration-300 shadow-xl z-20">
            <div class="h-20 flex items-center px-8 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-sinom.png') }}" alt="SINOM" class="w-11 h-11 object-contain rounded-lg">
                    <div class="brand-text">
                        <h1 class="font-bold text-xl tracking-wide">SINOM</h1>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Admin Panel</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 sidebar-section-label">Utama</p>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-home mr-3 opacity-70"></i><span class="sidebar-label">Dashboard</span>
                </a>

                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2 sidebar-section-label">Manajemen</p>
    
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.categories.*') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-tag mr-3 opacity-70"></i><span class="sidebar-label">Kategori</span>
                </a>
                <a href="{{ route('admin.menus.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.menus.*') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-utensils mr-3 opacity-70"></i><span class="sidebar-label">Manajemen Menu</span>
                </a>
                <a href="{{ route('admin.menus.promos') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.menus.promos') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-tags mr-3 opacity-70"></i><span class="sidebar-label">Promo & Bundling</span>
                </a>
                <a href="{{ route('admin.towers.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.towers.*') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-chair mr-3 opacity-70"></i><span class="sidebar-label">Meja</span>
                </a>

                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2 sidebar-section-label">Transaksi</p>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.orders.report') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-shopping-cart mr-3 opacity-70"></i><span class="sidebar-label">Pesanan</span>
                </a>
                <a href="{{ route('admin.orders.report') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.orders.report') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-chart-line mr-3 opacity-70"></i><span class="sidebar-label">Laporan Pesanan</span>
                </a>


                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2 sidebar-section-label">Sistem</p>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.settings.*') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-cog mr-3 opacity-70"></i><span class="sidebar-label">Pengaturan</span>
                </a>
            </nav>

            <div class="p-4 border-t border-white/10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i><span class="sidebar-label">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50 relative">
            <!-- Glass Header -->
            <header class="h-16 md:h-20 glass flex items-center justify-between px-4 md:px-8 sticky top-0 z-10 w-full">
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-slate-600 hover:text-navy-800 transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <button id="sidebar-toggle-desktop" class="hidden md:inline-flex text-slate-600 hover:text-navy-800 transition" title="Minimize Sidebar">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-lg md:text-xl font-bold text-navy-800">@yield('title')</h2>
                </div>
                
                <div class="flex items-center gap-2 md:gap-4">
                    <button id="notif-sound-toggle" type="button" class="w-10 h-10 rounded-full bg-white border border-[var(--border-warm)] text-[var(--text-secondary)] hover:text-[var(--near-black)] hover:border-[var(--terracotta)] transition relative" title="Suara notifikasi aktif">
                        <i id="notif-sound-icon" class="fas fa-volume-up text-sm"></i>
                    </button>
                    <button id="notif-bell-btn" type="button" class="w-10 h-10 rounded-full bg-white border border-[var(--border-warm)] text-[var(--text-secondary)] hover:text-[var(--near-black)] hover:border-[var(--terracotta)] transition relative" title="Notifikasi pesanan">
                        <i class="fas fa-bell text-sm"></i>
                        <span id="order-notif-badge" class="hidden absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"></span>
                    </button>
                    <button id="printer-toggle-btn" type="button" class="w-10 h-10 rounded-full bg-white border border-[var(--border-warm)] text-[var(--text-secondary)] hover:text-[var(--near-black)] hover:border-[var(--terracotta)] transition" title="Hubungkan printer thermal">
                        <i id="printer-toggle-icon" class="fas fa-print text-sm"></i>
                    </button>
                    <div class="flex flex-col text-right hidden sm:block">
                        <span class="text-sm font-semibold text-navy-800">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-slate-500">Administrator</span>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-navy-800 to-slate-700 text-white flex items-center justify-center text-sm font-bold shadow ring-4 ring-white">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
                @if(session('success'))
                    <div class="mb-6 bg-green-50/50 backdrop-blur border border-green-200 text-green-700 px-6 py-4 rounded-xl shadow-sm flex items-center gap-3 animate-fade-in-down">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-red-50/50 backdrop-blur border border-red-200 text-red-700 px-6 py-4 rounded-xl shadow-sm flex items-center gap-3 animate-fade-in-down">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation text-sm"></i>
                        </div>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js').catch(function () {});
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('aside');
            const hamburgerBtn = document.querySelector('button.md\\:hidden');
            const desktopToggleBtn = document.getElementById('sidebar-toggle-desktop');
            const contentOverlay = document.createElement('div');
            
            // Create overlay
            contentOverlay.className = 'fixed inset-0 bg-black/50 z-10 hidden md:hidden transition-opacity opacity-0';
            document.body.appendChild(contentOverlay);

            hamburgerBtn.addEventListener('click', () => {
                const isHidden = sidebar.classList.contains('hidden');
                if (isHidden) {
                    sidebar.classList.remove('hidden');
                    sidebar.classList.add('fixed', 'inset-y-0', 'left-0');
                    contentOverlay.classList.remove('hidden');
                    setTimeout(() => contentOverlay.classList.remove('opacity-0'), 10);
                } else {
                    sidebar.classList.add('hidden');
                    sidebar.classList.remove('fixed', 'inset-y-0', 'left-0');
                    contentOverlay.classList.add('opacity-0');
                    setTimeout(() => contentOverlay.classList.add('hidden'), 300);
                }
            });

            contentOverlay.addEventListener('click', () => {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('fixed', 'inset-y-0', 'left-0');
                contentOverlay.classList.add('opacity-0');
                setTimeout(() => contentOverlay.classList.add('hidden'), 300);
            });

            if (desktopToggleBtn) {
                desktopToggleBtn.addEventListener('click', () => {
                    document.body.classList.toggle('sidebar-collapsed');
                });
            }

            // ── Notification system (external) ──
            // URLs are passed via data-* attributes on the script tag.
        });
    </script>
    <script
        id="admin-notif-script"
        src="{{ asset('js/admin-notifications.js') }}"
        data-all-notifications-url="{{ route('admin.orders.all-notifications') }}"
        data-qr-notifications-url="{{ route('admin.orders.qr-notifications') }}"
        data-order-detail-base-url="{{ url('/admin/orders') }}"
    ></script>
</body>
</html>
