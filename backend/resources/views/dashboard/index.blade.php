@extends('layouts.app')

@section('title', 'Server Infrastructure')

@section('content')
<div class="space-y-8 animate-fade-in" x-data="{ viewMode: 'grid' }">
    
    <!-- Dashboard Heading and Global Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Infrastructure Overview</h1>
            <p class="text-slate-400 text-sm mt-1">Orchestrate and deploy your high-performance containerized VPS instances</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-slate-900 border border-slate-800 p-1 rounded-xl flex">
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition duration-150">
                    <i class="fa-solid fa-grip mr-1"></i> Grid
                </button>
                <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition duration-150">
                    <i class="fa-solid fa-list mr-1"></i> List
                </button>
            </div>
            <a href="{{ route('server.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-indigo-600/20 text-sm transition transform active:scale-95">
                <i class="fa-solid fa-plus-circle text-base"></i> Add New VPS
            </a>
        </div>
    </div>

    <!-- Aggregate Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Stat Card 1 -->
        <div class="glass rounded-2xl p-5 border border-slate-800/80 hover:border-slate-700/50 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs uppercase font-extrabold tracking-widest text-slate-500">Total VPS Servers</p>
                    <p class="text-3xl font-extrabold text-white mt-1">{{ $totalServers }}</p>
                </div>
                <div class="p-2.5 bg-slate-900/80 rounded-xl text-indigo-400 border border-slate-800">
                    <i class="fa-solid fa-server text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-indigo-400">
                <i class="fa-solid fa-circle-dot text-[8px] animate-pulse"></i> Multi-instance orchestrator
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="glass rounded-2xl p-5 border border-slate-800/80 hover:border-slate-700/50 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs uppercase font-extrabold tracking-widest text-slate-500">Active Agents</p>
                    <p class="text-3xl font-extrabold text-emerald-400 mt-1">{{ $onlineServers }}</p>
                </div>
                <div class="p-2.5 bg-slate-900/80 rounded-xl text-emerald-400 border border-slate-800">
                    <i class="fa-solid fa-bolt text-lg animate-pulse"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-slate-400 font-semibold">
                @if($totalServers > 0)
                    {{ round(($onlineServers / $totalServers) * 100) }}% infrastructure coverage
                @else
                    No servers registered
                @endif
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="glass rounded-2xl p-5 border border-slate-800/80 hover:border-slate-700/50 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs uppercase font-extrabold tracking-widest text-slate-500">Micro-Projects</p>
                    <p class="text-3xl font-extrabold text-white mt-1">{{ $totalProjects }}</p>
                </div>
                <div class="p-2.5 bg-slate-900/80 rounded-xl text-purple-400 border border-slate-800">
                    <i class="fa-solid fa-folder-open text-lg"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs text-purple-400 font-semibold">
                <i class="fa-brands fa-git-alt"></i> Automated Git Deployments
            </div>
        </div>

        <!-- Stat Card 4 -->
        <div class="glass rounded-2xl p-5 border border-slate-800/80 hover:border-slate-700/50 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs uppercase font-extrabold tracking-widest text-slate-500">Avg CPU Usage</p>
                    <p class="text-3xl font-extrabold text-white mt-1">{{ round($avgCpu) }}%</p>
                </div>
                <div class="p-2.5 bg-slate-900/80 rounded-xl text-cyan-400 border border-slate-800">
                    <i class="fa-solid fa-microchip text-lg"></i>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="mt-4 w-full bg-slate-900 rounded-full h-1.5 overflow-hidden border border-slate-800">
                <div class="h-full bg-gradient-to-r from-cyan-500 to-indigo-500 transition duration-500" style="width: {{ $avgCpu }}%"></div>
            </div>
        </div>
    </div>

    <!-- Active VPS Server List -->
    <div>
        <h2 class="text-xl font-extrabold text-white tracking-tight mb-5 flex items-center gap-2">
            <i class="fa-solid fa-network-wired text-indigo-400"></i> Active Server Nodes
        </h2>

        @if($servers->isEmpty())
            <!-- Empty State -->
            <div class="glass rounded-3xl p-12 text-center border border-slate-800/80 flex flex-col items-center justify-center min-h-[350px]">
                <div class="h-16 w-16 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-800 flex items-center justify-center border border-slate-800 text-slate-400 mb-6 shadow-inner">
                    <i class="fa-solid fa-rocket text-2xl"></i>
                </div>
                <h3 class="text-xl font-extrabold text-white">No VPS instances registered yet</h3>
                <p class="text-slate-400 text-sm max-w-md mx-auto mt-2">Add your first Linux VPS to deploy docker instances, monitor core metrics, and enjoy real-time interactive terminals.</p>
                <a href="{{ route('server.create') }}" class="mt-6 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2.5 px-5 rounded-xl shadow-lg text-sm transition">
                    <i class="fa-solid fa-circle-plus"></i> Register Server Node
                </a>
            </div>
        @else
            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($servers as $server)
                    <div class="glass rounded-3xl p-6 border transition hover:scale-[1.01] duration-300 relative group flex flex-col justify-between min-h-[290px] {{ $server->status === 'online' ? 'border-slate-800/80 hover:border-indigo-500/40' : 'border-slate-900 hover:border-slate-800' }}">
                        
                        <!-- Top Header inside Card -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-3.5 w-3.5 relative">
                                        @if($server->status === 'online')
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-slate-950"></span>
                                        @else
                                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-slate-600 border-2 border-slate-950"></span>
                                        @endif
                                    </span>
                                    <span class="text-xs uppercase font-extrabold tracking-widest {{ $server->status === 'online' ? 'text-emerald-400' : 'text-slate-500' }}">
                                        {{ $server->status === 'online' ? 'active node' : 'offline' }}
                                    </span>
                                </div>
                                <div class="text-[10px] font-mono text-slate-500 bg-slate-900/60 px-2 py-0.5 rounded border border-slate-800">
                                    PORT {{ $server->agent_port }}
                                </div>
                            </div>

                            <h3 class="text-xl font-extrabold text-white group-hover:text-indigo-400 transition">{{ $server->name }}</h3>
                            <p class="text-xs font-mono text-slate-400 mt-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-globe text-slate-500"></i> {{ $server->ip_address }}
                            </p>
                        </div>

                        <!-- Central Metrics Stats -->
                        <div class="my-5 space-y-3.5">
                            @if($server->status === 'online')
                                <!-- CPU Usage -->
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs font-semibold">
                                        <span class="text-slate-400"><i class="fa-solid fa-microchip mr-1.5 text-cyan-400"></i>CPU</span>
                                        <span class="text-slate-200">{{ $server->cpu_usage }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-900/80 rounded-full h-1 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-cyan-400 to-indigo-500 transition duration-500" style="width: {{ $server->cpu_usage }}%"></div>
                                    </div>
                                </div>

                                <!-- Memory Usage -->
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs font-semibold">
                                        <span class="text-slate-400"><i class="fa-solid fa-memory mr-1.5 text-purple-400"></i>RAM</span>
                                        <span class="text-slate-200">{{ $server->ram_usage }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-900/80 rounded-full h-1 overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-purple-400 to-pink-500 transition duration-500" style="width: {{ $server->ram_usage }}%"></div>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-4 bg-slate-950/40 rounded-2xl border border-slate-900/60">
                                    <i class="fa-solid fa-ban text-slate-600 mb-1.5 text-sm"></i>
                                    <p class="text-[11px] text-slate-500 uppercase font-bold tracking-wider">No real-time metrics available</p>
                                </div>
                            @endif
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-4 border-t border-slate-900/60 flex items-center justify-between gap-2">
                            <a href="{{ route('server.show', $server->id) }}" class="flex-1 text-center bg-slate-900 hover:bg-indigo-600 border border-slate-800 hover:border-indigo-500 text-xs font-bold py-2 rounded-lg text-slate-200 hover:text-white transition duration-200">
                                <i class="fa-solid fa-sliders mr-1.5"></i> Manage
                            </a>
                            
                            @if($server->status === 'online')
                                <a href="{{ route('server.terminal', $server->id) }}" class="p-2 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-indigo-400 transition" title="Web Terminal">
                                    <i class="fa-solid fa-terminal text-sm"></i>
                                </a>
                                <a href="{{ route('server.docker', $server->id) }}" class="p-2 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-cyan-400 transition" title="Docker Container Controller">
                                    <i class="fa-brands fa-docker text-sm"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'" class="glass rounded-3xl border border-slate-800/80 overflow-hidden" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-900/80 text-xs uppercase font-extrabold tracking-widest text-slate-500 bg-slate-950/20">
                                <th class="py-4 px-6">Server Node</th>
                                <th class="py-4 px-6">IP Address</th>
                                <th class="py-4 px-6">Agent Port</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6">Performance</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900/40 text-sm font-semibold">
                            @foreach($servers as $server)
                                <tr class="hover:bg-slate-900/10 transition">
                                    <td class="py-4 px-6 font-bold text-white">{{ $server->name }}</td>
                                    <td class="py-4 px-6 font-mono text-slate-400">{{ $server->ip_address }}</td>
                                    <td class="py-4 px-6 font-mono text-slate-400">{{ $server->agent_port }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $server->status === 'online' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800/30 text-slate-500 border border-slate-800/50' }}">
                                            <i class="fa-solid fa-circle text-[6px]"></i>
                                            {{ $server->status === 'online' ? 'ONLINE' : 'OFFLINE' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($server->status === 'online')
                                            <div class="flex items-center gap-4 text-xs font-mono">
                                                <span>CPU: {{ $server->cpu_usage }}%</span>
                                                <span>RAM: {{ $server->ram_usage }}%</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-600 font-bold uppercase">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right flex items-center justify-end gap-2.5">
                                        <a href="{{ route('server.show', $server->id) }}" class="text-xs font-bold bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 px-3 py-1.5 rounded-lg text-slate-200 transition">
                                            Manage
                                        </a>
                                        @if($server->status === 'online')
                                            <a href="{{ route('server.terminal', $server->id) }}" class="text-slate-400 hover:text-indigo-400 p-1.5" title="Terminal"><i class="fa-solid fa-terminal"></i></a>
                                            <a href="{{ route('server.docker', $server->id) }}" class="text-slate-400 hover:text-cyan-400 p-1.5" title="Docker"><i class="fa-brands fa-docker text-lg"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
