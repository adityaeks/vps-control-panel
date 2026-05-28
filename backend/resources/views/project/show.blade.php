@extends('layouts.app')

@section('title', 'Project - ' . $project->name)

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Back / Navigation -->
    <div class="flex items-center justify-between border-b border-slate-900 pb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('server.show', $project->server_id) }}" class="h-9 w-9 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-slate-200 flex items-center justify-center transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-indigo-400 text-sm"></i> Project Details
                </h1>
                <p class="text-slate-400 text-xs mt-0.5">Automated Git pipeline on: <span class="font-bold text-slate-300">{{ $project->server->name }}</span></p>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('project.edit', $project->id) }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs font-bold text-slate-300 hover:text-white transition">
                <i class="fa-solid fa-gear mr-1"></i> Edit Project
            </a>
            
            <form action="{{ route('project.delete', $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project? All deployment logs will be permanently deleted.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-950/20 hover:bg-rose-900/25 border border-rose-500/20 hover:border-rose-500/35 text-xs font-bold text-rose-400 hover:text-rose-300 transition">
                    <i class="fa-solid fa-trash-can mr-1"></i> Delete Project
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Project Configuration Summary Card -->
        <div class="glass rounded-3xl p-6 border border-slate-800/80 space-y-6">
            <div>
                <h3 class="text-xs uppercase font-extrabold tracking-widest text-slate-500">PROJECT NAME</h3>
                <p class="text-lg font-bold text-white mt-1">{{ $project->name }}</p>
            </div>

            <div>
                <h3 class="text-xs uppercase font-extrabold tracking-widest text-slate-500">GIT CONFIGURATION</h3>
                <p class="text-sm font-bold text-indigo-400 mt-1 flex items-center gap-1.5"><i class="fa-solid fa-code-branch text-xs"></i> Branch: {{ $project->branch }}</p>
                <p class="text-xs font-mono text-slate-400 mt-1 flex items-center gap-1.5 truncate" title="{{ $project->repository_url }}">
                    <i class="fa-solid fa-link text-slate-500"></i> {{ Str::limit($project->repository_url, 30) }}
                </p>
            </div>

            <div>
                <h3 class="text-xs uppercase font-extrabold tracking-widest text-slate-500">PIPELINE STATUS</h3>
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase
                        @if($project->status === 'deploying') bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 animate-pulse
                        @elseif($project->status === 'success') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                        @elseif($project->status === 'failed') bg-rose-500/10 text-rose-400 border border-rose-500/20
                        @else bg-slate-800/40 text-slate-400 border border-slate-800/60
                        @endif">
                        <i class="fa-solid fa-circle text-[6px]"></i>
                        {{ $project->status }}
                    </span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-900">
                @if($project->server->status === 'online')
                    <form action="{{ route('project.deploy', $project->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-600/20 text-sm transition transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-rocket"></i> Trigger Deployment Now
                        </button>
                    </form>
                @else
                    <button disabled class="w-full bg-slate-900 border border-slate-800 text-slate-500 font-bold py-3 px-4 rounded-xl text-sm cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plug-circle-xmark"></i> VPS Agent Offline
                    </button>
                @endif
            </div>
        </div>

        <!-- Deploy Script Viewer -->
        <div class="md:col-span-2 glass rounded-3xl p-6 border border-slate-800/80 flex flex-col justify-between">
            <div class="space-y-3">
                <h3 class="text-xs uppercase font-extrabold tracking-widest text-slate-500">AUTOMATION SCRIPT EXECUTION</h3>
                <div class="bg-slate-950 p-4 rounded-2xl border border-slate-900 font-mono text-xs text-indigo-300 leading-relaxed overflow-x-auto whitespace-pre">
                    {{ $project->deploy_script ?? 'No script configured.' }}
                </div>
            </div>
            <p class="text-[10px] text-slate-500 font-medium mt-4">
                <i class="fa-solid fa-circle-exclamation mr-1 text-slate-600"></i> The script runs automatically inside the sandbox on the agent VPS during trigger events.
            </p>
        </div>
    </div>

    <!-- Deployment Logs History -->
    <div class="glass rounded-3xl border border-slate-800/80 p-6 md:p-8">
        <h2 class="text-xl font-extrabold text-white tracking-tight mb-5 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-indigo-400 text-lg"></i> Deployment History
        </h2>

        @if($deployments->isEmpty())
            <div class="text-center py-8">
                <i class="fa-solid fa-timeline text-slate-700 text-3xl mb-3 block"></i>
                <p class="text-xs text-slate-500 font-semibold uppercase">No deployment triggers recorded yet</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-900/60 text-xs uppercase font-extrabold tracking-widest text-slate-500">
                            <th class="py-3 px-4">Run ID</th>
                            <th class="py-3 px-4">Triggered At</th>
                            <th class="py-3 px-4">Duration</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Run Logs</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900/30 text-sm font-semibold">
                        @foreach($deployments as $deploy)
                            <tr class="hover:bg-slate-900/10 transition">
                                <td class="py-3.5 px-4 font-mono text-xs text-slate-400">#{{ $deploy->id }}</td>
                                <td class="py-3.5 px-4 text-slate-300">{{ $deploy->created_at->diffForHumans() }}</td>
                                <td class="py-3.5 px-4 font-mono text-xs text-slate-400">
                                    @if($deploy->finished_at)
                                        {{ $deploy->finished_at->diffInSeconds($deploy->started_at) }}s
                                    @else
                                        Running...
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                                        @if($deploy->status === 'running') bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 animate-pulse
                                        @elseif($deploy->status === 'success') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                        @elseif($deploy->status === 'failed') bg-rose-500/10 text-rose-400 border border-rose-500/20
                                        @else bg-slate-800/40 text-slate-400 border border-slate-800/60
                                        @endif">
                                        <i class="fa-solid fa-circle text-[5px]"></i>
                                        {{ $deploy->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('deployment.show', $deploy->id) }}" class="text-xs font-bold bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white transition">
                                        Inspect Logs
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
