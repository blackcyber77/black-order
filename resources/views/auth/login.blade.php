<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Order KITB</title>
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
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center relative overflow-hidden font-sans">

    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute top-10 left-10 w-72 h-72 bg-orange-400/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-float"
            style="animation-delay: 2s;"></div>
    </div>

    <div class="w-full max-w-md p-6 relative z-10">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 p-8 md:p-10">
            <div class="text-center mb-10">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-navy-800 to-navy-900 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-navy-900/20 transform rotate-3">
                    <i class="fas fa-utensils text-orange-500 text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-navy-900 mb-2">sugeng enjing</h1>
                <p class="text-slate-500 text-sm">Masuk untuk mengelola kantin Anda</p>
            </div>

            @if(session('error'))
                <div
                    class="bg-red-50 border border-red-100 text-red-600 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5 ml-1">Email</label>
                    <div class="relative group">
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-5 py-3.5 pl-12 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all font-medium text-navy-900 shadow-sm group-hover:bg-white"
                            placeholder="nama@email.com">
                        <i
                            class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-hover:text-orange-500 transition-colors"></i>
                    </div>
                    @error('email')<p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy-900 mb-1.5 ml-1">Password</label>
                    <div class="relative group">
                        <input type="password" name="password" required
                            class="w-full px-5 py-3.5 pl-12 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all font-medium text-navy-900 shadow-sm group-hover:bg-white"
                            placeholder="••••••••">
                        <i
                            class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-hover:text-orange-500 transition-colors"></i>
                    </div>
                    @error('password')<p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-between ml-1">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded text-orange-500 focus:ring-orange-500 border-slate-300">
                        <span class="ml-2 text-sm text-slate-600 font-medium">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-gradient-to-r from-navy-800 to-navy-900 text-white rounded-xl font-bold shadow-lg shadow-navy-900/20 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    Masuk Sekarang
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 text-sm mt-8 font-medium">
            <a href="{{ route('home') }}" class="hover:text-orange-600 transition inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Kembali ke Menu Pelanggan
            </a>
        </p>
    </div>
</body>

</html>