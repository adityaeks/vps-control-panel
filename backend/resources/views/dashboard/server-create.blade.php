@extends('layouts.app')

@section('title', 'Add VPS')

@section('content')
<div class="max-w-2xl mx-auto py-6 animate-fade-in">
    <!-- Back to Dashboard -->
    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-200 transition text-sm font-semibold mb-6">
        <i class="fa-solid fa-arrow-left"></i> Back to Infrastructure Overview
    </a>

    <div class="glass glass-glow rounded-3xl p-8 border border-slate-800/80">
        <div class="border-b border-slate-900 pb-6 mb-6">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Register VPS Node</h1>
            <p class="text-slate-400 text-sm mt-1">Connect a remote virtual private server to orchestrate micro-services and docker containers</p>
        </div>

        <form action="{{ route('server.create') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Server Name -->
                <div>
                    <label for="name" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">Server Display Name</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', 'Production Server') }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('name')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- IP Address -->
                <div>
                    <label for="ip_address" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">IP Address / Host</label>
                    <input type="text" id="ip_address" name="ip_address" required value="{{ old('ip_address', '127.0.0.1') }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none"
                           placeholder="e.g. 192.168.1.100">
                    @error('ip_address')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Agent Port -->
                <div>
                    <label for="agent_port" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">WebSocket Gateway Port</label>
                    <input type="number" id="agent_port" name="agent_port" required value="{{ old('agent_port', 3000) }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('agent_port')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Security Token -->
                <div>
                    <label for="auth_token" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">Authentication Token</label>
                    <input type="text" id="auth_token" name="auth_token" required value="{{ old('auth_token', 'secure-vps-token-12345') }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('auth_token')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-indigo-950/20 border border-indigo-500/15 rounded-2xl p-5 text-indigo-300 text-xs leading-relaxed space-y-2">
                <p class="font-extrabold uppercase tracking-wider text-indigo-400 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i> How to connect this VPS:
                </p>
                <p>1. Copy the VPS Agent files to your target VPS server inside any folder.</p>
                <p>2. Run <code>npm install</code> to download required libraries.</p>
                <p>3. Start the agent daemon using <code>GATEWAY_URL=ws://[YOUR_GATEWAY_IP]:3000 SERVER_ID=[ID] AUTH_TOKEN=[TOKEN] npm start</code>.</p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900">
                <a href="{{ route('dashboard') }}" class="px-5 py-3 rounded-xl border border-slate-800 hover:border-slate-700 hover:bg-slate-900 text-sm font-bold text-slate-400 hover:text-white transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-indigo-600/20 text-sm transition">
                    Register Server
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
