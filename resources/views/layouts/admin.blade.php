<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Kantin</title>
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
                    }
                }
            }
        }
    </script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .sidebar-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #F97316;
            padding-left: 1rem;
        }
        .sidebar-link i {
            width: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased text-slate-800">
    <div class="flex h-screen overflow-hidden">
        <!-- Modern Sidebar -->
        <aside class="w-64 bg-navy-900 text-white flex-shrink-0 hidden md:flex flex-col transition-all duration-300 shadow-xl z-20">
            <div class="h-20 flex items-center px-8 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg transform rotate-3 hover:rotate-6 transition">
                        <i class="fas fa-utensils text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-xl tracking-wide">Order KITB</h1>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">Admin Panel</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Utama</p>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-home mr-3 opacity-70"></i> Dashboard
                </a>

                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Manajemen</p>
    
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.categories.*') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-tag mr-3 opacity-70"></i> Kategori
                </a>
                <a href="{{ route('admin.towers.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.towers.*') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-chair mr-3 opacity-70"></i> Meja
                </a>

                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Transaksi</p>
                <a href="{{ route('admin.pos.create') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.pos.create') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-cash-register mr-3 opacity-70 text-green-400"></i> POS Kasir (Input)
                </a>
                <a href="{{ route('admin.pos.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.pos.index') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-clipboard-list mr-3 opacity-70 text-orange-400"></i> Pesanan Aktif
                </a>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.orders.*') && !request()->routeIs('admin.pos.*') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-shopping-cart mr-3 opacity-70"></i> Pesanan
                </a>


                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mt-6 mb-2">Sistem</p>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link flex items-center px-4 py-3 rounded-lg text-slate-300 hover:text-white {{ request()->routeIs('admin.settings.*') ? 'active bg-white/5 text-white' : '' }}">
                    <i class="fas fa-cog mr-3 opacity-70"></i> Pengaturan
                </a>
            </nav>

            <div class="p-4 border-t border-white/10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('aside');
            const hamburgerBtn = document.querySelector('button.md\\:hidden');
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
        });
    </script>
</body>
</html>
