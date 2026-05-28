@extends('layouts.app')

@section('title', 'Docker Panel - ' . $server->name)

@section('content')
<div class="space-y-6 animate-fade-in" x-data="dockerPanel()">
    
    <!-- Top Action / Info Banner -->
    <div class="flex items-center justify-between border-b border-slate-900 pb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('server.show', $server->id) }}" class="h-9 w-9 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <i class="fa-brands fa-docker text-cyan-400 text-lg"></i> Docker Orchestrator
                </h1>
                <p class="text-slate-400 text-xs mt-0.5">Manage containers, view logs, and restart services on: <span class="font-bold text-slate-300">{{ $server->name }}</span></p>
            </div>
        </div>
        
        <div>
            <button @click="fetchContainers()" class="bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 font-bold py-2 px-4 rounded-xl text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate" :class="loading ? 'animate-spin' : ''"></i> Refresh Containers
            </button>
        </div>
    </div>

    <!-- Quick Navigation Tabs -->
    <div class="flex border-b border-slate-900 gap-6 text-sm font-semibold">
        <a href="{{ route('server.show', $server->id) }}" class="pb-4 text-slate-400 hover:text-slate-200 transition">
            <i class="fa-solid fa-circle-nodes mr-1.5"></i> Node Overview
        </a>
        <a href="{{ route('server.terminal', $server->id) }}" class="pb-4 text-slate-400 hover:text-slate-200 transition">
            <i class="fa-solid fa-terminal mr-1.5"></i> Web Terminal
        </a>
        <a href="{{ route('server.docker', $server->id) }}" class="pb-4 border-b-2 border-indigo-500 text-indigo-400">
            <i class="fa-brands fa-docker mr-1.5 text-lg"></i> Docker Management
        </a>
    </div>

    <!-- Table of Containers -->
    <div class="glass rounded-3xl border border-slate-800/80 overflow-hidden min-h-[250px] relative">
        <!-- Loader -->
        <div x-show="loading" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm z-10 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <i class="fa-solid fa-circle-notch text-indigo-500 text-3xl animate-spin"></i>
                <p class="text-xs uppercase font-extrabold tracking-widest text-slate-400">Querying Docker Daemon...</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-900/60 text-xs uppercase font-extrabold tracking-widest text-slate-500 bg-slate-950/20">
                        <th class="py-4 px-6">Container ID</th>
                        <th class="py-4 px-6">Name</th>
                        <th class="py-4 px-6">Image</th>
                        <th class="py-4 px-6">State / Status</th>
                        <th class="py-4 px-6">IP / Ports</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-900/30 text-sm font-semibold">
                    <template x-for="c in containers" :key="c.ID">
                        <tr class="hover:bg-slate-900/10 transition">
                            <td class="py-4 px-6 font-mono text-xs text-slate-400" x-text="c.ID.slice(0, 12)"></td>
                            <td class="py-4 px-6 font-bold text-white" x-text="c.Names"></td>
                            <td class="py-4 px-6 font-mono text-xs text-slate-400" x-text="c.Image"></td>
                            <td class="py-4 px-6">
                                <span :class="c.State === 'running' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'"
                                      class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase">
                                    <i class="fa-solid fa-circle text-[6px]" :class="c.State === 'running' ? 'animate-pulse' : ''"></i>
                                    <span x-text="c.State"></span>
                                </span>
                                <div class="text-[10px] text-slate-500 mt-0.5" x-text="c.Status"></div>
                            </td>
                            <td class="py-4 px-6 font-mono text-xs text-slate-400" x-text="c.Ports || 'N/A'"></td>
                            <td class="py-4 px-6 text-right flex items-center justify-end gap-2.5">
                                <button @click="viewLogs(c.ID, c.Names)" class="text-xs font-bold bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white transition">
                                    <i class="fa-solid fa-file-lines mr-1 text-slate-400"></i> Logs
                                </button>
                                <button @click="restartContainer(c.ID)" class="text-xs font-bold bg-cyan-950/20 hover:bg-cyan-900/25 border border-cyan-500/20 hover:border-cyan-500/35 px-3 py-1.5 rounded-lg text-cyan-400 transition">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Restart
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="containers.length === 0 && !loading">
                        <td colspan="6" class="text-center py-12 text-slate-500 font-bold">
                            <i class="fa-brands fa-docker text-4xl text-slate-700 mb-4 block"></i>
                            No containers found or Docker is offline.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Logs Modal Overlay -->
    <div x-show="showLogsModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;" x-transition>
        <div class="max-w-4xl w-full glass rounded-3xl overflow-hidden border border-slate-800 flex flex-col h-[80vh]">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-900 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-white tracking-tight flex items-center gap-2">
                        <i class="fa-brands fa-docker text-cyan-400"></i> Container Logs: <span class="text-indigo-400" x-text="logsContainerName"></span>
                    </h3>
                </div>
                <button @click="showLogsModal = false" class="text-slate-400 hover:text-slate-200 transition"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <!-- Body Console -->
            <div class="flex-1 bg-slate-950 p-5 overflow-y-auto font-mono text-xs leading-relaxed text-slate-300 whitespace-pre-wrap select-text selection:bg-indigo-600/30" id="logs-console">
                <span x-text="containerLogs"></span>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-slate-900 bg-slate-900/20 flex justify-end">
                <button @click="showLogsModal = false" class="bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 font-bold py-2 px-5 rounded-xl text-xs transition">
                    Close Logs
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function dockerPanel() {
        return {
            containers: [],
            loading: true,
            showLogsModal: false,
            logsContainerName: '',
            containerLogs: '',
            ws: null,

            init() {
                const token = '{{ env("GATEWAY_TOKEN", "secure-vps-token-12345") }}';
                const gatewayWsUrl = '{{ env("GATEWAY_WS_URL", "ws://127.0.0.1:3000") }}';
                const serverId = "{{ $server->id }}";
                
                // Initialize WebSocket link
                this.ws = new WebSocket(`${gatewayWsUrl}?role=client&token=${token}&server_id=${serverId}&type=terminal`);

                this.ws.onopen = () => {
                    console.log('Docker Socket bridge connected.');
                    this.fetchContainers();
                };

                this.ws.onmessage = (event) => {
                    try {
                        const payload = JSON.parse(event.data);
                        
                        if (payload.type === 'docker_list_res') {
                            this.loading = false;
                            if (payload.data.error) {
                                console.error(payload.data.error);
                                this.containers = [];
                            } else {
                                this.containers = payload.data;
                            }
                        } 
                        
                        else if (payload.type === 'docker_logs_res') {
                            this.containerLogs = payload.data.logs;
                            this.$nextTick(() => {
                                const consoleBox = document.getElementById('logs-console');
                                consoleBox.scrollTop = consoleBox.scrollHeight;
                            });
                        }
                    } catch (e) {
                        console.error('Docker parsing error:', e);
                    }
                };

                this.ws.onclose = () => {
                    console.log('Docker Socket disconnected.');
                };
            },

            fetchContainers() {
                if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                    this.loading = true;
                    this.ws.send(JSON.stringify({ type: 'docker_list' }));
                }
            },

            restartContainer(id) {
                if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                    this.loading = true;
                    this.ws.send(JSON.stringify({ type: 'docker_restart', container_id: id }));
                }
            },

            viewLogs(id, name) {
                this.logsContainerName = name;
                this.containerLogs = 'Fetching container logs...';
                this.showLogsModal = true;
                
                if (this.ws && this.ws.readyState === WebSocket.OPEN) {
                    this.ws.send(JSON.stringify({ type: 'docker_logs', container_id: id }));
                }
            }
        };
    }
</script>
@endsection
