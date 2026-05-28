@extends('layouts.app')

@section('title', 'Deploy Project')

@section('content')
<div class="max-w-2xl mx-auto py-6 animate-fade-in">
    <!-- Back to Server -->
    <a href="{{ route('server.show', $server->id) }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-200 transition text-sm font-semibold mb-6">
        <i class="fa-solid fa-arrow-left"></i> Back to Server Node
    </a>

    <div class="glass glass-glow rounded-3xl p-8 border border-slate-800/80">
        <div class="border-b border-slate-900 pb-6 mb-6">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Configure New Project</h1>
            <p class="text-slate-400 text-sm mt-1">Deploy an automated git service on: <span class="text-indigo-400 font-bold">{{ $server->name }}</span></p>
        </div>

        <form action="{{ route('project.create', $server->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Project Name -->
            <div>
                <label for="name" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">Project Name</label>
                <input type="text" id="name" name="name" required value="{{ old('name', 'My Web App') }}"
                       class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                @error('name')
                    <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Repository URL -->
                <div class="md:col-span-2">
                    <label for="repository_url" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">Git Repository URL</label>
                    <input type="text" id="repository_url" name="repository_url" required value="{{ old('repository_url', 'https://github.com/username/repo.git') }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('repository_url')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch -->
                <div>
                    <label for="branch" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400 mb-2">Default Branch</label>
                    <input type="text" id="branch" name="branch" required value="{{ old('branch', 'main') }}"
                           class="w-full bg-slate-900/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-white placeholder-slate-500 transition duration-200 outline-none">
                    @error('branch')
                        <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Deploy Script -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="deploy_script" class="block text-xs uppercase font-extrabold tracking-widest text-slate-400">Deployment Automation Script</label>
                    <span class="text-[10px] text-slate-500 font-mono">Runs inside VPS agent sandbox</span>
                </div>
                <textarea id="deploy_script" name="deploy_script" rows="5"
                          class="w-full bg-slate-950/60 border border-slate-800 focus:border-indigo-500/80 focus:ring-1 focus:ring-indigo-500/30 rounded-xl py-3 px-4 text-sm text-slate-300 font-mono placeholder-slate-600 transition duration-200 outline-none"
                          placeholder="git pull origin main&#10;npm install&#10;npm run build"></textarea>
                @error('deploy_script')
                    <p class="text-rose-400 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-900">
                <a href="{{ route('server.show', $server->id) }}" class="px-5 py-3 rounded-xl border border-slate-800 hover:border-slate-700 hover:bg-slate-900 text-sm font-bold text-slate-400 hover:text-white transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-indigo-600/20 text-sm transition">
                    Configure Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
