@extends('layouts.app')

@section('title', 'Deployment #' . $deployment->id)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto py-6 animate-fade-in" x-data="liveLogger()">
    
    <!-- Top Action / Info Banner -->
    <div class="flex items-center justify-between border-b border-slate-900 pb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('project.show', $project->id) }}" class="h-9 w-9 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-rocket text-indigo-400 text-sm"></i> Live Deployment Logs
                </h1>
                <p class="text-slate-400 text-xs mt-0.5">Project: <span class="font-bold text-slate-300">{{ $project->name }}</span> | Run: #{{ $deployment->id }}</p>
            </div>
        </div>
        
        <div>
            <!-- Dynamically animated status tag -->
            <span :class="status === 'running' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 animate-pulse' :
                          (status === 'success' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                           (status === 'failed' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 
                            'bg-slate-800/40 text-slate-400 border border-slate-800/60'))"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold uppercase transition border">
                <i class="fa-solid fa-circle text-[6px]" :class="status === 'running' ? 'animate-pulse' : ''"></i>
                <span x-text="status.toUpperCase()"></span>
            </span>
        </div>
    </div>

    <!-- Live Console Terminal -->
    <div class="glass rounded-3xl border border-slate-800/80 overflow-hidden flex flex-col h-[550px] shadow-2xl">
        <!-- Console Window Header -->
        <div class="px-5 py-3 border-b border-slate-900 bg-slate-950/40 flex items-center justify-between text-xs text-slate-500 font-mono select-none">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-rose-500/80 inline-block"></span>
                <span class="w-3 h-3 rounded-full bg-amber-500/80 inline-block"></span>
                <span class="w-3 h-3 rounded-full bg-emerald-500/80 inline-block"></span>
                <span class="ml-2 text-slate-400 font-bold">bash-deploy-sandbox.sh</span>
            </div>
            <div x-show="status === 'running'" class="flex items-center gap-1.5 text-indigo-400 font-bold">
                <i class="fa-solid fa-circle-notch animate-spin text-[10px]"></i> Streaming...
            </div>
        </div>

        <!-- Terminal Output Container -->
        <div class="flex-1 bg-slate-950 p-6 overflow-y-auto font-mono text-xs leading-relaxed text-slate-300 whitespace-pre-wrap select-text selection:bg-indigo-600/30" id="terminal-console">
            <span x-text="logs"></span>
            <!-- Blinking shell cursor -->
            <span x-show="status === 'running'" class="w-1.5 h-3 bg-indigo-400 inline-block animate-pulse ml-0.5 align-middle"></span>
        </div>
    </div>

    <!-- Back to Project link -->
    <div class="flex justify-end pt-2">
        <a href="{{ route('project.show', $project->id) }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 font-bold py-2.5 px-5 rounded-xl text-sm transition">
            <i class="fa-solid fa-circle-chevron-left"></i> Back to Project Panel
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function liveLogger() {
        return {
            logs: '',
            status: "{{ $deployment->status }}",
            ws: null,

            init() {
                // Initialize the logger logs if they already exist in database
                this.logs = `{!! addslashes($deployment->log_output ?? 'Initializing real-time connection stream...\n') !!}`;

                if (this.status !== 'running') {
                    // Deployment completed, no need for active WS connection
                    return;
                }

                const token = '{{ env("GATEWAY_TOKEN", "secure-vps-token-12345") }}';
                const gatewayWsUrl = '{{ env("GATEWAY_WS_URL", "ws://127.0.0.1:3000") }}';
                const serverId = "{{ $project->server_id }}";
                const deployId = "{{ $deployment->id }}";
                
                // Open real-time WS logs bridge
                this.ws = new WebSocket(`${gatewayWsUrl}?role=client&token=${token}&server_id=${serverId}&type=deploy&deployment_id=${deployId}`);

                this.ws.onmessage = (event) => {
                    try {
                        const payload = JSON.parse(event.data);
                        
                        if (payload.type === 'deploy_log') {
                            this.logs += payload.data;
                            this.scrollToBottom();
                        } 
                        
                        else if (payload.type === 'metrics') {
                            // ignore metrics
                        }
                    } catch (e) {
                        this.logs += event.data;
                        this.scrollToBottom();
                    }
                };

                this.ws.onclose = () => {
                    // Check database status updates periodically or when socket closes
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                };

                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            },

            scrollToBottom() {
                const consoleBox = document.getElementById('terminal-console');
                consoleBox.scrollTop = consoleBox.scrollHeight;
            }
        };
    }
</script>
@endsection
