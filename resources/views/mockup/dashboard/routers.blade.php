@extends('mockup.dashboard.layout')

@section('title', 'Router — BILLINGIN')
@section('active', 'routers')
@section('page-title', 'Router')
@section('page-subtitle')
    Hubungkan MikroTik untuk isolir dan buka isolir otomatis.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <a href="{{ route('routers.create') }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Router
    </a>
@endsection

@section('content')
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse ($routers as $router)
            <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6" data-router-id="{{ $router->id }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-lg"><i class="fa-solid fa-tower-broadcast"></i></span>
                        <div>
                            <p class="font-bold">{{ $router->name }}</p>
                            <p class="text-gray-500 text-sm">{{ $router->host }}:{{ $router->api_port }} &middot; {{ $router->network_driver }}</p>
                        </div>
                    </div>
                    <span data-status-badge
                        class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $router->status === 'online' ? 'bg-emerald-500/15 text-emerald-400' : ($router->status === 'offline' ? 'bg-red-500/15 text-red-400' : 'bg-white/10 text-gray-400') }}">
                        &bull; {{ ['online' => 'Online', 'offline' => 'Offline', 'unknown' => 'Belum diuji'][$router->status] }}
                    </span>
                </div>
                <div class="border-t border-white/10 pt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Pelanggan terhubung</span>
                        <span class="font-semibold">{{ $router->customers_count }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Terakhir dicek</span>
                        <span class="font-semibold">{{ $router->last_seen_at?->format('d M Y, H.i') ?? '—' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-5 flex-wrap">
                    <button type="button" data-test-connection="{{ route('routers.test-connection', $router) }}" class="h-11 px-4 rounded-lg border border-white/15 font-semibold hover:bg-white/5">Uji Koneksi</button>
                    <button type="button" onclick="mockupToast('Sinkronisasi secret {{ $router->name }} belum tersedia — bagian S3')" class="h-11 px-4 rounded-lg border border-white/15 font-semibold hover:bg-white/5">Sinkronkan Secret</button>
                    <a href="{{ route('routers.edit', $router) }}" class="h-11 px-4 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Ubah</a>
                    <form method="POST" action="{{ route('routers.destroy', $router) }}" onsubmit="return confirm('Hapus router {{ $router->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="h-11 px-4 rounded-lg border border-red-500/30 text-red-300 font-semibold hover:bg-red-500/10">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-white/10 bg-[#0B0F24] p-16 flex flex-col items-center text-center">
                <p class="font-bold mb-1">Belum ada router</p>
                <p class="text-gray-400 text-sm mb-5">Tambahkan router pertama untuk menghubungkan pelanggan ke jaringan.</p>
                <a href="{{ route('routers.create') }}" class="h-11 px-5 inline-flex items-center rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold">Tambah Router</a>
            </div>
        @endforelse
    </div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('[data-test-connection]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card = btn.closest('[data-router-id]');
            const badge = card.querySelector('[data-status-badge]');
            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = 'Menguji...';

            fetch(btn.dataset.testConnection, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
                .then(function (res) { return res.json().then(function (body) { return { status: res.status, body: body }; }); })
                .then(function (result) {
                    if (!result.body.ok) {
                        mockupToast(result.body.message);
                        return;
                    }
                    mockupToast(result.body.reachable
                        ? 'Terhubung (' + result.body.latency_ms + ' ms)'
                        : 'Tidak bisa terhubung: ' + result.body.message);
                    if (badge) {
                        badge.textContent = '• ' + (result.body.reachable ? 'Online' : 'Offline');
                        badge.className = 'px-2.5 py-1 rounded-full text-xs font-semibold ' +
                            (result.body.reachable ? 'bg-emerald-500/15 text-emerald-400' : 'bg-red-500/15 text-red-400');
                    }
                })
                .catch(function () { mockupToast('Gagal menguji koneksi.'); })
                .finally(function () { btn.disabled = false; btn.textContent = originalText; });
        });
    });
</script>
@endsection
