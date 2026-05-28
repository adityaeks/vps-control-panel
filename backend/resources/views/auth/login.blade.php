@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-6">
    <div class="max-w-md w-full glass glass-glow rounded-3xl p-8 md:p-10 border border-slate-800/80">
        <div class="text-center mb-8">
            <div class="inline-flex h-14 w-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 items-center justify-center shadow-lg shadow-indigo-500/25 mb-4">
                <i class="fa-solid fa-lock text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Welcome Back</h1>
            <p class="text-slate-400 text-sm mt-2">Log in to manage your decentralized infrastructure</p>
        </div>

        <form action="{{ url('/login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-500">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 pl-11 pr-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none" 
                           placeholder="you@domain.com">
                </div>
                @error('email')
                    <p class="text-rose-400 text-xs mt-1.5 font-semibold"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400">Password</label>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-500">
                        <i class="fa-solid fa-key"></i>
                    </span>
                    <input type="password" id="password" name="password" required 
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 pl-11 pr-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none" 
                           placeholder="••••••••">
                </div>
                @error('password')
                    <p class="text-rose-400 text-xs mt-1.5 font-semibold"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer select-none text-slate-300">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0 focus:ring-offset-0">
                    Remember me
                </label>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 px-4 rounded-xl transition duration-300 transform active:scale-[0.98] shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30 text-sm">
                Log In
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-900 text-center text-sm text-slate-400">
            Don't have an account? 
            <a href="{{ url('/register') }}" class="text-indigo-400 font-semibold hover:text-indigo-300 transition">Create an Account</a>
        </div>
    </div>
</div>
@endsection
