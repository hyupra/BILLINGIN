@extends('mockup.dashboard.layout')

@section('title', 'Tambah Router — BILLINGIN')
@section('active', 'routers')
@section('back-link')
    <a href="{{ route('routers.index') }}" class="text-indigo-400 hover:text-indigo-300">&larr; Kembali ke Router</a>
@endsection
@section('page-title', 'Tambah Router')

@section('content')
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300 max-w-2xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('routers.store') }}" class="space-y-6 max-w-2xl">
        @csrf
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama router</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="MT-Mekar-01" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Driver</label>
                    <select name="network_driver" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="manual" @selected(old('network_driver', 'manual') === 'manual')>Manual (belum tersambung otomatis)</option>
                        <option value="mikrotik_pppoe" @selected(old('network_driver') === 'mikrotik_pppoe')>MikroTik PPPoE</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Host / IP</label>
                    <input type="text" name="host" value="{{ old('host') }}" required placeholder="192.168.88.1" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Port API</label>
                    <input type="number" name="api_port" value="{{ old('api_port', 8728) }}" required min="1" max="65535" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Username API (opsional)</label>
                    <input type="text" name="api_username" value="{{ old('api_username') }}" autocomplete="off" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Password API (opsional)</label>
                    <input type="password" name="api_password" autocomplete="new-password" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Router</button>
            <a href="{{ route('routers.index') }}" class="h-12 px-6 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Batal</a>
        </div>
    </form>
@endsection
