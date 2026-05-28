@extends('layouts.app')

@section('title', 'Web Terminal - ' . $server->name)

@section('styles')
<!-- XTerm CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/xterm@5.3.0/css/xterm.min.css" />
<style>
    .terminal-container {
        background-color: #020617; /* slate-950 */
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.5);
    }
    .xterm-viewport {
        background-color: #020617 !important;
        scrollbar-width: thin;
        scrollbar-color: #1e293b #020617;
    }
</style>
@endsection

@section('content')
<div class="space-y-6 animate-fade-in">
    <!-- Back / Navigation -->
    <div class="flex items-center justify-between border-b border-slate-900 pb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('server.show', $server->id) }}" class="h-9 w-9 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-terminal text-indigo-400 text-sm"></i> Interactive Web Terminal
                </h1>
                <p class="text-slate-400 text-xs mt-0.5">Secure CLI session connected to: <span class="font-bold text-slate-300">{{ $server->name }}</span> ({{ $server->ip_address }})</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <i class="fa-solid fa-circle text-[6px] animate-pulse"></i> CONNECTED VIA GATEWAY
            </span>
        </div>
    </div>

    <!-- Quick Navigation Tabs -->
    <div class="flex border-b border-slate-900 gap-6 text-sm font-semibold">
        <a href="{{ route('server.show', $server->id) }}" class="pb-4 text-slate-400 hover:text-slate-200 transition">
            <i class="fa-solid fa-circle-nodes mr-1.5"></i> Node Overview
        </a>
        <a href="{{ route('server.terminal', $server->id) }}" class="pb-4 border-b-2 border-indigo-500 text-indigo-400">
            <i class="fa-solid fa-terminal mr-1.5"></i> Web Terminal
        </a>
        <a href="{{ route('server.docker', $server->id) }}" class="pb-4 text-slate-400 hover:text-slate-200 transition">
            <i class="fa-brands fa-docker mr-1.5 text-lg"></i> Docker Management
        </a>
    </div>

    <!-- Terminal Board -->
    <div class="terminal-container rounded-3xl overflow-hidden p-5">
        <div id="terminal" class="w-full h-[500px]"></div>
    </div>
</div>
@endsection

@section('scripts')
<!-- XTerm JS -->
<script src="https://cdn.jsdelivr.net/npm/xterm@5.3.0/lib/xterm.min.js"></script>
<script>
    // Initialize Xterm.js
    const term = new Terminal({
        cursorBlink: true,
        fontSize: 14,
        fontFamily: 'JetBrains Mono, monospace',
        theme: {
            background: '#020617', // slate-950
            foreground: '#f8fafc',
            cursor: '#818cf8',
            black: '#0f172a',
            red: '#f43f5e',
            green: '#10b981',
            yellow: '#f59e0b',
            blue: '#3b82f6',
            magenta: '#8b5cf6',
            cyan: '#06b6d4',
            white: '#f1f5f9'
        },
        convertEol: true
    });

    term.open(document.getElementById('terminal'));
    term.write('Connecting to secure remote terminal bridge...\r\n');

    // Gateway config from .env
    const token = '{{ env("GATEWAY_TOKEN", "secure-vps-token-12345") }}';
    const gatewayWsUrl = '{{ env("GATEWAY_WS_URL", "ws://127.0.0.1:3000") }}';
    const serverId = "{{ $server->id }}";
    
    const ws = new WebSocket(`${gatewayWsUrl}?role=client&token=${token}&server_id=${serverId}&type=terminal`);

    ws.onopen = () => {
        term.write('Connection established! Welcome to your interactive shell.\r\n\r\n');
    };

    ws.onmessage = (event) => {
        try {
            const payload = JSON.parse(event.data);
            if (payload.type === 'terminal_output') {
                term.write(payload.data);
            } else if (payload.type === 'error') {
                term.write(`\r\n\x1b[31m[Error] ${payload.message}\x1b[0m\r\n`);
            }
        } catch (e) {
            // Fallback for raw outputs
            term.write(event.data);
        }
    };

    ws.onclose = () => {
        term.write('\r\n\x1b[31mTerminal session disconnected from host.\x1b[0m\r\n');
    };

    // Listen for keys in Xterm and write to socket
    term.onData(data => {
        if (ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify({
                type: 'terminal_input',
                data: data
            }));
        }
    });

    // Resize handling (simplified)
    window.addEventListener('resize', () => {
        // Here we could calculate dimensions and send terminal_resize event
    });
</script>
@endsection
