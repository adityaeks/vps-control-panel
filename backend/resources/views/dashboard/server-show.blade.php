@extends('layouts.app')

@section('title', $server->name)

@section('styles')
<!-- ChartJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in" x-data="{ activeTab: 'overview' }">
    
    <!-- Top Action / Info Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/40 p-6 rounded-3xl border border-slate-800/80 glass">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center border border-indigo-500/20 text-white shadow-lg">
                <i class="fa-solid fa-hard-drive text-xl"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ $server->name }}</h1>
                    <span class="inline-flex h-2.5 w-2.5 relative">
                        @if($server->status === 'online')
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        @else
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-slate-600"></span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-400 font-mono mt-1">
                    <span><i class="fa-solid fa-globe mr-1"></i>{{ $server->ip_address }}</span>
                    <span>•</span>
                    <span id="uptime-display"><i class="fa-solid fa-clock mr-1"></i>{{ $server->status === 'online' ? 'Uptime: ' . round($server->uptime / 3600) . ' hrs' : 'Offline' }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('server.edit', $server->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-bold text-slate-300 hover:text-white transition">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Server
            </a>
            
            <form action="{{ route('server.delete', $server->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this server? All linked projects and deployments will be removed.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-950/20 hover:bg-rose-900/25 border border-rose-500/20 hover:border-rose-500/35 text-xs font-bold text-rose-400 hover:text-rose-300 transition">
                    <i class="fa-solid fa-trash-can mr-1"></i> Delete Server
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Navigation Tabs -->
    @if($server->status === 'online')
    <div class="flex border-b border-slate-900 gap-6 text-sm font-semibold">
        <a href="{{ route('server.show', $server->id) }}" class="pb-4 border-b-2 border-indigo-500 text-indigo-400">
            <i class="fa-solid fa-circle-nodes mr-1.5"></i> Node Overview
        </a>
        <a href="{{ route('server.terminal', $server->id) }}" class="pb-4 text-slate-400 hover:text-slate-200 transition">
            <i class="fa-solid fa-terminal mr-1.5"></i> Web Terminal
        </a>
        <a href="{{ route('server.docker', $server->id) }}" class="pb-4 text-slate-400 hover:text-slate-200 transition">
            <i class="fa-brands fa-docker mr-1.5 text-lg"></i> Docker Management
        </a>
    </div>
    @endif

    <!-- Realtime Monitor Dashboard -->
    @if($server->status === 'online')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- CPU Realtime Gauges -->
            <div class="glass rounded-3xl p-6 border border-slate-800/80">
                <h3 class="text-xs uppercase font-extrabold tracking-widest text-slate-500 flex justify-between items-center">
                    <span>CPU LOAD</span>
                    <span id="cpu-metric-val" class="text-white font-mono font-bold text-sm">{{ $server->cpu_usage }}%</span>
                </h3>
                <div class="h-44 mt-4">
                    <canvas id="cpuChart"></canvas>
                </div>
            </div>

            <!-- Memory Realtime Gauges -->
            <div class="glass rounded-3xl p-6 border border-slate-800/80">
                <h3 class="text-xs uppercase font-extrabold tracking-widest text-slate-500 flex justify-between items-center">
                    <span>RAM ALLOCATION</span>
                    <span id="ram-metric-val" class="text-white font-mono font-bold text-sm">{{ $server->ram_usage }}%</span>
                </h3>
                <div class="h-44 mt-4">
                    <canvas id="ramChart"></canvas>
                </div>
            </div>

            <!-- Disk Gauge -->
            <div class="glass rounded-3xl p-6 border border-slate-800/80 flex flex-col justify-between">
                <div>
                    <h3 class="text-xs uppercase font-extrabold tracking-widest text-slate-500">STORAGE UTILIZATION</h3>
                    <p class="text-4xl font-extrabold text-white mt-4 font-mono" id="disk-metric-val">{{ $server->disk_usage }}%</p>
                    <p class="text-xs text-slate-400 font-semibold mt-1">Total disk footprint active on root partition</p>
                </div>
                <div class="w-full bg-slate-900 rounded-full h-3 overflow-hidden border border-slate-800 mt-6">
                    <div id="disk-progress-bar" class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 transition duration-500" style="width: {{ $server->disk_usage }}%"></div>
                </div>
            </div>
        </div>
    @else
        <!-- Agent Offline Banner -->
        <div class="glass rounded-3xl p-8 border border-slate-900/80 text-center flex flex-col items-center justify-center min-h-[220px]">
            <div class="h-12 w-12 rounded-xl bg-slate-900/80 flex items-center justify-center text-slate-500 border border-slate-800 mb-4 animate-pulse">
                <i class="fa-solid fa-plug-circle-xmark text-lg"></i>
            </div>
            <h3 class="text-lg font-extrabold text-white">VPS Agent Offline</h3>
            <p class="text-slate-400 text-xs max-w-sm mx-auto mt-1">The system is not receiving heartbeat data. Ensure the VPS agent script is active and connected to gateway port <span class="font-bold text-slate-300">3000</span>.</p>
        </div>
    @endif

    <!-- Project List Section -->
    <div class="glass rounded-3xl border border-slate-800/80 p-6 md:p-8">
        <div class="flex justify-between items-center border-b border-slate-900 pb-5 mb-5">
            <div>
                <h2 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-code text-indigo-400 text-lg"></i> Deployed Projects
                </h2>
                <p class="text-slate-400 text-xs mt-0.5">Microservices deployed and managed on this server node</p>
            </div>
            <a href="{{ route('project.create', $server->id) }}" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-3.5 rounded-xl text-xs transition transform active:scale-95 shadow">
                <i class="fa-solid fa-circle-plus"></i> Deploy Project
            </a>
        </div>

        @if($projects->isEmpty())
            <!-- Projects Empty State -->
            <div class="text-center py-10 flex flex-col items-center">
                <div class="h-12 w-12 rounded-xl bg-slate-950/40 flex items-center justify-center text-slate-600 border border-slate-900 mb-4">
                    <i class="fa-solid fa-folder-closed text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-300">No active projects</h4>
                <p class="text-slate-500 text-xs max-w-xs mx-auto mt-1">Start running code on this VPS by configuring git deployments and execution triggers.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-900/60 text-xs uppercase font-extrabold tracking-widest text-slate-500">
                            <th class="py-3 px-4">Project</th>
                            <th class="py-3 px-4">Branch</th>
                            <th class="py-3 px-4">Repository</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900/30 text-sm font-semibold">
                        @foreach($projects as $project)
                            <tr class="hover:bg-slate-900/10 transition group">
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('project.show', $project->id) }}" class="text-white hover:text-indigo-400 font-bold transition">
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs text-indigo-400">
                                    <i class="fa-solid fa-code-branch mr-1 text-[10px]"></i>{{ $project->branch }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs text-slate-400">
                                    {{ Str::limit($project->repository_url, 35) }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                                        @if($project->status === 'deploying') bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 animate-pulse
                                        @elseif($project->status === 'success') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                        @elseif($project->status === 'failed') bg-rose-500/10 text-rose-400 border border-rose-500/20
                                        @else bg-slate-800/40 text-slate-400 border border-slate-800/60
                                        @endif">
                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right flex items-center justify-end gap-2.5">
                                    <a href="{{ route('project.show', $project->id) }}" class="p-1 text-slate-400 hover:text-white transition" title="Project Details">
                                        <i class="fa-solid fa-chevron-right text-xs"></i>
                                    </a>
                                    
                                    @if($server->status === 'online')
                                        <form action="{{ route('project.deploy', $project->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-1.5 px-3 rounded-lg text-xs transition transform active:scale-95 shadow flex items-center gap-1">
                                                <i class="fa-solid fa-rocket text-[10px]"></i> Deploy
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Local Directories Section -->
    @if(!empty($localDirectories))
    <div class="glass rounded-3xl border border-slate-800/80 p-6 md:p-8 mt-8">
        <div class="flex justify-between items-center border-b border-slate-900 pb-5 mb-5">
            <div>
                <h2 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-indigo-400 text-lg"></i> Local Directories on VPS
                </h2>
                <p class="text-slate-400 text-xs mt-0.5 font-semibold">Physical directories detected in <code class="bg-slate-950 px-1.5 py-0.5 rounded text-indigo-300 font-mono text-[11px]">/var/www/html/</code></p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($localDirectories as $dir)
                @php
                    // Check if this directory is already registered as a project
                    $isRegistered = $projects->contains('name', $dir);
                @endphp
                <div class="bg-slate-950/40 hover:bg-slate-900/30 border border-slate-800/80 rounded-2xl p-4 transition group flex flex-col justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm transition group-hover:scale-110">
                            <i class="fa-solid fa-folder"></i>
                        </div>
                        <div class="truncate">
                            <h4 class="text-sm font-bold text-white tracking-tight group-hover:text-indigo-400 transition truncate">{{ $dir }}</h4>
                            <p class="text-[10px] text-slate-500 font-mono mt-0.5 truncate">/var/www/html/{{ $dir }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mt-2 pt-3 border-t border-slate-900/60">
                        @if($isRegistered)
                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase text-emerald-400">
                                <i class="fa-solid fa-circle-check"></i> Registered
                            </span>
                            @php
                                $linkedProject = $projects->firstWhere('name', $dir);
                            @endphp
                            @if($linkedProject)
                                <div class="flex items-center gap-3">
                                    @if($server->status === 'online')
                                        <form action="{{ route('project.deploy', $linkedProject->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-indigo-600/30 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/20 hover:border-indigo-500/40 font-bold py-1 px-2.5 rounded-lg text-[10px] transition transform active:scale-95 flex items-center gap-1 shadow-sm">
                                                <i class="fa-solid fa-rocket text-[9px]"></i> Deploy
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('project.show', $linkedProject->id) }}" class="text-xs text-indigo-400 hover:text-indigo-300 font-bold transition flex items-center gap-0.5">
                                        Manage <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                    </a>
                                </div>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase text-slate-500">
                                <i class="fa-solid fa-circle-question"></i> Unregistered
                            </span>
                            <a href="{{ route('project.create', $server->id) }}?name={{ urlencode($dir) }}&repo_url={{ urlencode('https://github.com/adityaeks/' . $dir . '.git') }}" 
                               class="inline-flex items-center gap-1 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 font-bold py-1.5 px-3 rounded-lg text-[11px] transition">
                                <i class="fa-solid fa-plus text-[9px]"></i> Add Project
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
@if($server->status === 'online')
<script>
    // Gateway config from .env
    const GATEWAY_WS_URL = '{{ env("GATEWAY_WS_URL", "ws://127.0.0.1:3000") }}';
    const GATEWAY_TOKEN = '{{ env("GATEWAY_TOKEN", "secure-vps-token-12345") }}';

    // Initialize empty charts for realtime metrics streaming
    const maxDataPoints = 15;
    const labels = Array(maxDataPoints).fill('');
    const cpuData = Array(maxDataPoints).fill(0);
    const ramData = Array(maxDataPoints).fill(0);

    const chartConfig = (label, data, color) => ({
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                borderColor: color,
                backgroundColor: color + '05',
                borderWidth: 2,
                pointRadius: 0,
                tension: 0.35,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false },
                y: {
                    min: 0,
                    max: 100,
                    grid: { color: 'rgba(255,255,255,0.02)' },
                    ticks: { color: 'rgba(255,255,255,0.3)', font: { size: 9, family: 'JetBrains Mono' } }
                }
            }
        }
    });

    const cpuChartCtx = document.getElementById('cpuChart').getContext('2d');
    const cpuChart = new Chart(cpuChartCtx, chartConfig('CPU', cpuData, '#06b6d4')); // cyan

    const ramChartCtx = document.getElementById('ramChart').getContext('2d');
    const ramChart = new Chart(ramChartCtx, chartConfig('RAM', ramData, '#a855f7')); // purple

    // WebSocket metric client bridge
    const wsToken = GATEWAY_TOKEN;
    const serverId = "{{ $server->id }}";
    
    // Gateway connection
    const ws = new WebSocket(`${GATEWAY_WS_URL}?role=client&token=${wsToken}&server_id=${serverId}&type=metrics`);

    ws.onmessage = (event) => {
        try {
            const payload = JSON.parse(event.data);
            if (payload.type === 'metrics') {
                const metrics = payload.data;
                
                // Update DOM text
                document.getElementById('cpu-metric-val').innerText = `${metrics.cpu}%`;
                document.getElementById('ram-metric-val').innerText = `${metrics.ram}%`;
                document.getElementById('disk-metric-val').innerText = `${metrics.disk}%`;
                document.getElementById('disk-progress-bar').style.width = `${metrics.disk}%`;
                
                // Format Uptime
                const uptimeHours = Math.round(metrics.uptime / 3600);
                document.getElementById('uptime-display').innerHTML = `<i class="fa-solid fa-clock mr-1"></i>Uptime: ${uptimeHours} hrs`;

                // Update CPU Chart
                cpuData.push(metrics.cpu);
                if (cpuData.length > maxDataPoints) cpuData.shift();
                cpuChart.update();

                // Update RAM Chart
                ramData.push(metrics.ram);
                if (ramData.length > maxDataPoints) ramData.shift();
                ramChart.update();
            }
        } catch (err) {
            console.error('Error updates metrics charts:', err);
        }
    };

    ws.onerror = (err) => {
        console.error('WebSocket metrics bridge error:', err);
    };
</script>
@endif
@endsection
