@extends('mockup.dashboard.layout')

@section('title', 'Router — BILLINGIN')
@section('active', 'routers')
@section('page-title', 'Router')
@section('page-subtitle')
    Hubungkan MikroTik untuk isolir dan buka isolir otomatis.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <button type="button" onclick="mockupToast('Fitur tambah router belum tersedia di mockup ini')" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Router
    </button>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach ([
            [
                'name' => 'MT-Mekar-01', 'meta' => 'RouterOS 7.14 · VPN L2TP', 'online' => true,
                'secrets' => '90 / 90 sinkron', 'action' => 'Disable secret', 'synced' => '3 Okt 2026, 10.30',
                'warning' => null,
            ],
            [
                'name' => 'MT-Kampung-02', 'meta' => 'RouterOS 6.49 · VPN OpenVPN', 'online' => false,
                'secrets' => '60 / 60 sinkron', 'action' => 'Ubah profile', 'synced' => '3 Okt 2026, 06.12',
                'warning' => 'Router offline. 3 perintah isolir diantrikan dan dijalankan setelah router online.',
            ],
        ] as $r)
            <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-lg">📡</span>
                        <div>
                            <p class="font-bold">{{ $r['name'] }}</p>
                            <p class="text-gray-500 text-sm">{{ $r['meta'] }}</p>
                        </div>
                    </div>
                    @if ($r['online'])
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400">&bull; Online</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/15 text-red-400">&bull; Offline</span>
                    @endif
                </div>
                <div class="border-t border-white/10 pt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Secret PPPoE</span>
                        <span class="font-semibold">{{ $r['secrets'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Aksi isolir</span>
                        <span class="font-semibold">{{ $r['action'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Sinkron terakhir</span>
                        <span class="font-semibold">{{ $r['synced'] }}</span>
                    </div>
                </div>
                @if ($r['warning'])
                    <div class="mt-4 rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
                        ⚠ {{ $r['warning'] }}
                    </div>
                @endif
                <div class="flex items-center gap-3 mt-5">
                    <button type="button" onclick="mockupToast('Uji koneksi {{ $r['name'] }} belum tersedia di mockup ini')" class="h-11 px-4 rounded-lg border border-white/15 font-semibold hover:bg-white/5">Uji Koneksi</button>
                    <button type="button" onclick="mockupToast('Sinkronisasi secret {{ $r['name'] }} belum tersedia di mockup ini')" class="h-11 px-4 rounded-lg border border-white/15 font-semibold hover:bg-white/5">Sinkronkan Secret</button>
                </div>
            </div>
        @endforeach
    </div>
@endsection
