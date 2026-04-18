<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Kantin</title>
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
                            500: '#C96442',
                            600: '#B85A3D',
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
            --terracotta: #c96442;
            --terracotta-dark: #b85a3d;
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
            background: rgba(201, 100, 66, 0.12);
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
            animation: qrNotifSlideIn 0.35s ease-out;
        }
        @keyframes qrNotifSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        body {
            background: var(--paper);
            color: var(--near-black);
        }
        h1, h2, h3, .font-bold.text-navy-800, .font-bold.text-navy-900 {
            font-family: "Source Serif 4", Georgia, serif;
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
    </style>
</head>
<body class="font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Sidebar -->
        <aside class="w-64 bg-navy-900 text-[#b0aea5] flex-shrink-0 hidden md:flex flex-col transition-all duration-300 shadow-xl z-20">
            <div class="h-20 flex items-center px-8 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-500 flex items-center justify-center shadow-lg transform rotate-3 hover:rotate-6 transition">
                        <i class="fas fa-utensils text-white text-lg"></i>
                    </div>
                    <div class="brand-text">
                        <h1 class="font-bold text-xl tracking-wide">Order KITB</h1>
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
            <header class="h-20 glass flex items-center justify-between px-8 sticky top-0 z-10 w-full">
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-slate-600 hover:text-navy-800 transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <button id="sidebar-toggle-desktop" class="hidden md:inline-flex text-slate-600 hover:text-navy-800 transition" title="Minimize Sidebar">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-xl font-bold text-navy-800">@yield('title')</h2>
                </div>
                
                <div class="flex items-center gap-4">
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
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-8">
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

            const notificationsUrl = @json(route('admin.orders.qr-notifications'));
            const orderDetailBaseUrl = @json(url('/admin/orders'));
            const thermalPrintStorageKey = 'admin_qr_auto_thermal_print';
            const pollIntervalMs = 10000;
            const sinceStorageKey = 'admin_qr_notif_since';
            const notifiedIdsStorageKey = 'admin_qr_notified_ids';

            const notifContainer = document.createElement('div');
            notifContainer.className = 'fixed top-24 right-4 md:right-8 z-50 space-y-3 w-[92vw] max-w-sm pointer-events-none';
            document.body.appendChild(notifContainer);

            let lastSince = localStorage.getItem(sinceStorageKey);
            if (!lastSince) {
                lastSince = new Date().toISOString();
                localStorage.setItem(sinceStorageKey, lastSince);
            }

            const savedIds = sessionStorage.getItem(notifiedIdsStorageKey);
            const notifiedIds = new Set(savedIds ? JSON.parse(savedIds) : []);
            const autoThermalPrintEnabled = localStorage.getItem(thermalPrintStorageKey) !== '0';

            function persistNotifiedIds() {
                const ids = Array.from(notifiedIds).slice(-200);
                sessionStorage.setItem(notifiedIdsStorageKey, JSON.stringify(ids));
            }

            function playNotificationSound() {
                try {
                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContextClass) return;

                    const ctx = new AudioContextClass();
                    const oscillator = ctx.createOscillator();
                    const gainNode = ctx.createGain();

                    oscillator.type = 'triangle';
                    oscillator.frequency.setValueAtTime(880, ctx.currentTime);
                    gainNode.gain.setValueAtTime(0.0001, ctx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.18, ctx.currentTime + 0.02);
                    gainNode.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.25);

                    oscillator.connect(gainNode);
                    gainNode.connect(ctx.destination);

                    oscillator.start();
                    oscillator.stop(ctx.currentTime + 0.26);
                } catch (error) {
                    // Audio may be blocked by browser policies.
                }
            }

            function createNotificationCard(order) {
                const card = document.createElement('div');
                card.className = 'pointer-events-auto qr-notification-enter rounded-2xl border border-blue-100 shadow-xl bg-white overflow-hidden';
                card.innerHTML = `
                    <div class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-4 py-2 text-xs font-semibold tracking-wide uppercase">
                        Pesanan Baru QR
                    </div>
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-bold text-slate-900 text-sm">${order.order_number}</p>
                                <p class="text-xs text-slate-500 mt-0.5">${order.created_at_label} • Meja ${order.table_number || '-'}</p>
                            </div>
                            <button class="text-slate-300 hover:text-slate-500 transition text-sm" aria-label="Tutup notifikasi">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <p class="text-sm text-slate-700 mt-2">${order.customer_name || 'Pelanggan'}</p>
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-sm font-semibold text-green-600">${order.formatted_total}</span>
                            <div class="flex items-center gap-2">
                                <a href="${order.thermal_print_url}?autoprint=1" target="_blank" class="text-xs px-2.5 py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                                    Print
                                </a>
                                <a href="${orderDetailBaseUrl}/${order.id}" class="text-xs px-3 py-1.5 rounded-lg bg-slate-900 text-white hover:bg-slate-700 transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                const closeButton = card.querySelector('button');
                closeButton.addEventListener('click', () => {
                    card.remove();
                });

                setTimeout(() => {
                    card.remove();
                }, 12000);

                return card;
            }

            function triggerThermalAutoPrint(order) {
                if (!autoThermalPrintEnabled || !order.thermal_print_url) return;

                const iframe = document.createElement('iframe');
                iframe.style.position = 'fixed';
                iframe.style.width = '1px';
                iframe.style.height = '1px';
                iframe.style.opacity = '0';
                iframe.style.pointerEvents = 'none';
                iframe.style.bottom = '0';
                iframe.style.right = '0';
                iframe.src = `${order.thermal_print_url}?autoprint=1`;

                document.body.appendChild(iframe);
                setTimeout(() => iframe.remove(), 45000);
            }

            async function fetchQrNotifications() {
                try {
                    const url = `${notificationsUrl}?since=${encodeURIComponent(lastSince)}`;
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) return;
                    const payload = await response.json();
                    const orders = Array.isArray(payload.orders) ? payload.orders : [];

                    const newOrders = orders.filter((order) => !notifiedIds.has(order.id));
                    if (newOrders.length > 0) {
                        playNotificationSound();
                    }

                    newOrders.reverse().forEach((order) => {
                        notifiedIds.add(order.id);
                        triggerThermalAutoPrint(order);
                        notifContainer.appendChild(createNotificationCard(order));
                    });

                    if (payload.latest_created_at) {
                        lastSince = payload.latest_created_at;
                        localStorage.setItem(sinceStorageKey, lastSince);
                    }

                    persistNotifiedIds();
                } catch (error) {
                    // Ignore polling errors; next tick will retry.
                }
            }

            fetchQrNotifications();
            setInterval(fetchQrNotifications, pollIntervalMs);
        });
    </script>
</body>
</html>
