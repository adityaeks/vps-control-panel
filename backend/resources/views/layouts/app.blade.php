<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Antigravity VPS Panel - @yield('title', 'Dashboard')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #030712; /* slate-950 */
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(244, 63, 94, 0.05) 0px, transparent 50%),
                radial-gradient(at 50% 0%, rgba(168, 85, 247, 0.06) 0px, transparent 50%);
            background-attachment: fixed;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #030712;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
        .glass {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-glow {
            box-shadow: 0 0 25px -5px rgba(99, 102, 241, 0.15);
        }
    </style>
    @yield('styles')
</head>
<body class="h-full flex flex-col font-sans overflow-x-hidden">

    <!-- Top Navigation Bar -->
    <nav class="glass border-b border-slate-800/60 sticky top-0 z-50 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-server text-white text-lg"></i>
            </div>
            <div>
                <span class="font-extrabold text-xl bg-gradient-to-r from-white via-slate-100 to-indigo-400 bg-clip-text text-transparent tracking-tight">ANTIGRAVITY</span>
                <span class="text-[10px] uppercase font-bold tracking-widest text-indigo-400 ml-1">vps panel</span>
            </div>
        </div>

        @auth
        <div class="flex items-center gap-6">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold transition hover:text-indigo-400 flex items-center gap-2 {{ request()->routeIs('dashboard') ? 'text-indigo-400' : 'text-slate-300' }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            
            <div class="h-5 w-[1px] bg-slate-800"></div>

            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-full bg-slate-800 flex items-center justify-center border border-slate-700">
                    <i class="fa-solid fa-user text-slate-300 text-sm"></i>
                </div>
                <div class="hidden md:block">
                    <p class="text-xs font-bold text-slate-200">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-slate-400">{{ Auth::user()->email }}</p>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="ml-2">
                    @csrf
                    <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition duration-200" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </nav>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 md:p-8">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 flex items-center gap-3 shadow-lg shadow-emerald-500/5 animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                <div class="flex-1 text-sm font-semibold">{{ session('success') }}</div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-200 transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl border border-rose-500/30 bg-rose-500/10 text-rose-300 flex items-center gap-3 shadow-lg shadow-rose-500/5 animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg"></i>
                <div class="flex-1 text-sm font-semibold">{{ session('error') }}</div>
                <button @click="show = false" class="text-rose-400 hover:text-rose-200 transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-900 bg-slate-950/40 py-6 text-center text-xs text-slate-500">
        <p>&copy; 2026 Antigravity VPS Panel. Crafted with ❤️ for modern server orchestration.</p>
    </footer>

    @yield('scripts')
</body>
</html>
