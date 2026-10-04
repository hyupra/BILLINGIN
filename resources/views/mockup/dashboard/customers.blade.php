@extends('mockup.dashboard.layout')

@section('title', 'Pelanggan — BILLINGIN')
@section('active', 'customers')
@section('page-title', 'Pelanggan')
@section('page-subtitle')
    Kelola pelanggan, paket, dan status layanan.
@endsection
@section('page-actions')
    <a href="/mockup/dashboard/customers-import" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Impor CSV
    </a>
    <a href="/mockup/dashboard/customers-create" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Pelanggan
    </a>
@endsection

@section('content')
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('status') }}</div>
    @endif

    {{-- Filter tabs + search --}}
    <div class="flex items-center justify-between gap-4 flex-wrap mb-5" data-state-switch="customers">
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" data-state-btn="data" onclick="setActiveFilterPill(this); setTableState('customers','data')" class="px-4 py-2 rounded-full text-sm font-semibold bg-indigo-600 text-white">Semua {{ $customers->count() }}</button>
            <button type="button" onclick="setActiveFilterPill(this); mockupToast('Daftar di bawah belum benar-benar terfilter di mockup ini')" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Aktif</button>
            <button type="button" onclick="setActiveFilterPill(this); mockupToast('Daftar di bawah belum benar-benar terfilter di mockup ini')" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Terisolir</button>
            <button type="button" onclick="setActiveFilterPill(this); mockupToast('Daftar di bawah belum benar-benar terfilter di mockup ini')" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Berhenti</button>
        </div>
        <div class="w-full sm:w-72">
            <input type="text" placeholder="Cari nama, ID, atau paket" class="h-11 w-full px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <p class="text-gray-500 text-xs mt-1">Pencarian belum aktif di mockup ini.</p>
        </div>
    </div>

    {{-- ponytail: state demo buttons — a real backend sprint wires these to actual
         loading/empty/error responses. Here they just toggle which static block shows. --}}
    <div class="flex items-center gap-2 mb-4 text-xs" data-state-switch="customers">
        <span class="text-gray-500">Lihat state tabel:</span>
        <button type="button" data-state-btn="data" onclick="setTableState('customers','data')" class="px-3 py-1.5 rounded-md font-semibold bg-indigo-600 text-white">Data</button>
        <button type="button" data-state-btn="loading" onclick="setTableState('customers','loading')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Memuat</button>
        <button type="button" data-state-btn="empty" onclick="setTableState('customers','empty')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Kosong</button>
        <button type="button" data-state-btn="error" onclick="setTableState('customers','error')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Error</button>
    </div>

    <div data-state-group="customers">

        {{-- Data state (default) --}}
        <div data-state="data" class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-white/10">
                        <th class="px-6 py-3 font-medium">ID</th>
                        <th class="px-3 py-3 font-medium">Pelanggan</th>
                        <th class="px-3 py-3 font-medium">Paket</th>
                        <th class="px-3 py-3 font-medium">Router</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @php
                        $statusClass = [
                            'active' => 'bg-emerald-500/15 text-emerald-400',
                            'suspended' => 'bg-red-500/15 text-red-400',
                            'stopped' => 'bg-white/10 text-gray-400',
                        ];
                        $statusLabel = ['active' => 'Aktif', 'suspended' => 'Terisolir', 'stopped' => 'Berhenti'];
                    @endphp
                    @forelse ($customers as $c)
                        <tr class="hover:bg-white/5">
                            <td class="px-6 py-4 text-gray-400">{{ $c->customer_code }}</td>
                            <td class="px-3 py-4">
                                <p class="font-semibold">{{ $c->name }}</p>
                                <p class="text-gray-500 text-xs">{{ $c->phone }}</p>
                            </td>
                            <td class="px-3 py-4 text-gray-300">{{ $c->package->name }}</td>
                            <td class="px-3 py-4 text-gray-300">{{ $c->router->name ?? '—' }}</td>
                            <td class="px-3 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass[$c->status] }}">&bull; {{ $statusLabel[$c->status] }}</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('customers.edit', $c) }}" class="h-9 px-4 inline-flex items-center rounded-lg border border-white/15 text-xs font-semibold hover:bg-white/5">Ubah</a>
                                <form method="POST" action="{{ route('customers.destroy', $c) }}" class="inline" data-confirm-delete="{{ $c->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-9 px-4 rounded-lg border border-red-500/30 text-red-300 text-xs font-semibold hover:bg-red-500/10">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada pelanggan. <a href="/mockup/dashboard/customers-create" class="text-indigo-400 hover:text-indigo-300">Tambah pelanggan pertama</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="flex items-center justify-between px-6 py-4 border-t border-white/10">
                <span class="text-gray-400 text-sm">Menampilkan 8 dari 150 pelanggan</span>
                <div class="flex items-center gap-2">
                    <button type="button" disabled class="h-10 px-4 rounded-lg border border-white/10 text-gray-500 text-sm font-semibold cursor-not-allowed">Sebelumnya</button>
                    <button type="button" onclick="mockupToast('Data halaman berikutnya belum tersedia di mockup ini')" class="h-10 px-4 rounded-lg border border-white/15 text-sm font-semibold hover:bg-white/5">Berikutnya</button>
                </div>
            </div>
        </div>

        {{-- Loading state (C State tabel memuat) --}}
        <div data-state="loading" class="hidden rounded-xl border border-white/10 bg-[#0B0F24] p-6 space-y-3">
            @for ($i = 0; $i < 6; $i++)
                <div class="h-10 rounded-lg bg-white/5 animate-pulse"></div>
            @endfor
        </div>

        {{-- Empty state (C State tabel kosong) --}}
        <div data-state="empty" class="hidden rounded-xl border border-white/10 bg-[#0B0F24] p-16 flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-white/5 flex items-center justify-center mb-4 text-2xl">👥</div>
            <p class="font-bold mb-1">Belum ada pelanggan</p>
            <p class="text-gray-400 text-sm mb-5">Tambahkan pelanggan pertama atau impor dari file CSV.</p>
            <a href="/mockup/dashboard/customers-create" class="h-11 px-5 inline-flex items-center rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold">Tambah Pelanggan</a>
        </div>

        {{-- Error state (C State tabel error) --}}
        <div data-state="error" class="hidden rounded-xl border border-white/10 bg-[#0B0F24] p-16 flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-red-500/15 flex items-center justify-center mb-4 text-2xl">⚠️</div>
            <p class="font-bold mb-1">Data pelanggan gagal dimuat</p>
            <p class="text-gray-400 text-sm mb-5">Terjadi gangguan pada server. Coba lagi beberapa saat.</p>
            <button type="button" onclick="setTableState('customers','data')" class="h-11 px-5 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
                ⟳ Coba Lagi
            </button>
        </div>

    </div>
@endsection
