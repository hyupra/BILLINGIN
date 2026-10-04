@extends('mockup.dashboard.layout')

@section('title', 'Ubah Paket — BILLINGIN')
@section('active', 'packages')
@section('back-link')
    <a href="{{ route('packages.index') }}" class="text-indigo-400 hover:text-indigo-300">&larr; Kembali ke Paket</a>
@endsection
@section('page-title', 'Ubah Paket')

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

    <form method="POST" action="{{ route('packages.update', $package) }}" class="space-y-6 max-w-2xl">
        @csrf
        @method('PUT')
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama paket</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" required placeholder="Home 20 Mbps" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Label kecepatan</label>
                    <input type="text" name="speed_label" value="{{ old('speed_label', $package->speed_label) }}" required placeholder="20 Mbps" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Harga dasar (Rp)</label>
                    <input type="number" name="base_price" value="{{ old('base_price', $package->base_price) }}" required min="0" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">PPN (%)</label>
                    <input type="number" step="0.01" name="ppn_percent" value="{{ old('ppn_percent', $package->ppn_percent) }}" required min="0" max="100" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Router (opsional)</label>
                    <select name="router_id" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Tidak terikat router —</option>
                        @foreach ($routers as $router)
                            <option value="{{ $router->id }}" @selected(old('router_id', $package->router_id) == $router->id)>{{ $router->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Profile PPPoE default (opsional)</label>
                    <input type="text" name="default_profile" value="{{ old('default_profile', $package->default_profile) }}" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm mt-5">
                <input type="checkbox" name="allow_online_registration" value="1" @checked(old('allow_online_registration', $package->allow_online_registration)) class="w-4 h-4 rounded text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                Izinkan untuk pendaftaran online
            </label>
            <label class="flex items-center gap-2 text-sm mt-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package->is_active)) class="w-4 h-4 rounded text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                Aktif (nonaktifkan untuk menyembunyikan dari pendaftaran baru)
            </label>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Perubahan</button>
            <a href="{{ route('packages.index') }}" class="h-12 px-6 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Batal</a>
        </div>
    </form>
@endsection
