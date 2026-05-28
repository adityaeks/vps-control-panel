@extends('layouts.app')

@section('title', 'Edit VPS')

@section('content')
<div class="max-w-2xl mx-auto py-6 animate-fade-in">
    <!-- Back to Server Show -->
    <a href="{{ route('server.show', $server->id) }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-200 transition text-sm font-semibold mb-6">
        <i class="fa-solid fa-arrow-left"></i> Back to Server Node
    </a>

    <div class="glass glass-glow rounded-3xl p-8 border border-slate-800/80">
        <div class="border-b border-slate-900 pb-6 mb-6">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Edit Server Node</h1>
            <p class="text-slate-400 text-sm mt-1">Modify credentials and network specifications for server: <span class="text-indigo-400 font-bold">{{ $server->name }}</span></p>
        </div>

        <form action="{{ route('server.edit', $server->id) }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Server Name -->
                <div>
                    <label for="name" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">Server Display Name</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $server->name) }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('name')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- IP Address -->
                <div>
                    <label for="ip_address" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">IP Address / Host</label>
                    <input type="text" id="ip_address" name="ip_address" required value="{{ old('ip_address', $server->ip_address) }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('ip_address')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Agent Port -->
                <div>
                    <label for="agent_port" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">WebSocket Gateway Port</label>
                    <input type="number" id="agent_port" name="agent_port" required value="{{ old('agent_port', $server->agent_port) }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('agent_port')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Security Token -->
                <div>
                    <label for="auth_token" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">Authentication Token</label>
                    <input type="text" id="auth_token" name="auth_token" required value="{{ old('auth_token', $server->auth_token) }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('auth_token')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900">
                <a href="{{ route('server.show', $server->id) }}" class="px-5 py-3 rounded-xl border border-slate-800 hover:border-slate-700 hover:bg-slate-900 text-sm font-bold text-slate-400 hover:text-white transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-indigo-600/20 text-sm transition">
                    Update Server
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
