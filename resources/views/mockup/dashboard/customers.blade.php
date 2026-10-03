@extends('mockup.dashboard.layout')

@section('title', 'Pelanggan — BILLINGIN')
@section('active', 'customers')
@section('page-title', 'Pelanggan')
@section('page-subtitle')
    Kelola pelanggan, paket, dan status layanan.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <a href="/mockup/dashboard/pelanggan/impor" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Impor CSV
    </a>
    <a href="/mockup/dashboard/pelanggan/tambah" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Pelanggan
    </a>
@endsection

@section('content')

    {{-- Filter tabs + search --}}
    <div class="flex items-center justify-between gap-4 flex-wrap mb-5" data-state-switch="customers">
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" data-state-btn="data" onclick="setTableState('customers','data')" class="px-4 py-2 rounded-full text-sm font-semibold bg-indigo-600 text-white">Semua 150</button>
            <button type="button" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Aktif 128</button>
            <button type="button" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Terisolir 12</button>
            <button type="button" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Berhenti 10</button>
        </div>
        <input type="text" placeholder="Cari nama, ID, atau paket" class="h-11 w-full sm:w-72 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
                        <th class="px-3 py-3 font-medium">Area</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-3 py-3 font-medium">Jatuh tempo</th>
                        <th class="px-6 py-3 font-medium text-right">Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @php
                        $customers = [
                            ['id' => 'C-1042', 'name' => 'Budi S*****', 'phone' => '0857****695', 'package' => 'Home 20 Mbps', 'area' => 'Desa Mekar', 'status' => 'Aktif', 'due' => '10 Okt 2026', 'amount' => 'Rp 161.500'],
                            ['id' => 'C-1043', 'name' => 'Siti R*****', 'phone' => '0812****204', 'package' => 'Home 10 Mbps', 'area' => 'Desa Mekar', 'status' => 'Aktif', 'due' => '10 Okt 2026', 'amount' => 'Rp 111.000'],
                            ['id' => 'C-1051', 'name' => 'Agus P*****', 'phone' => '0821****887', 'package' => 'Home 50 Mbps', 'area' => 'Kp. Baru', 'status' => 'Terisolir', 'due' => '05 Okt 2026', 'amount' => 'Rp 277.500'],
                            ['id' => 'C-1060', 'name' => 'Dewi L*****', 'phone' => '0813****119', 'package' => 'Home 20 Mbps', 'area' => 'Perum Asri', 'status' => 'Aktif', 'due' => '12 Okt 2026', 'amount' => 'Rp 166.500'],
                            ['id' => 'C-1066', 'name' => 'Rudi H*****', 'phone' => '0856****430', 'package' => 'Home 10 Mbps', 'area' => 'Kp. Baru', 'status' => 'Berhenti', 'due' => '-', 'amount' => 'Rp 0'],
                            ['id' => 'C-1072', 'name' => 'Ani W*****', 'phone' => '0878****562', 'package' => 'Home 20 Mbps', 'area' => 'Desa Mekar', 'status' => 'Aktif', 'due' => '15 Okt 2026', 'amount' => 'Rp 166.500'],
                            ['id' => 'C-1080', 'name' => 'Joko S*****', 'phone' => '0819****771', 'package' => 'Home 50 Mbps', 'area' => 'Perum Asri', 'status' => 'Terisolir', 'due' => '03 Okt 2026', 'amount' => 'Rp 277.500'],
                            ['id' => 'C-1085', 'name' => 'Maya K*****', 'phone' => '0838****026', 'package' => 'Home 10 Mbps', 'area' => 'Desa Mekar', 'status' => 'Aktif', 'due' => '20 Okt 2026', 'amount' => 'Rp 111.000'],
                        ];
                        $statusClass = [
                            'Aktif' => 'bg-emerald-500/15 text-emerald-400',
                            'Terisolir' => 'bg-red-500/15 text-red-400',
                            'Berhenti' => 'bg-white/10 text-gray-400',
                        ];
                    @endphp
                    @foreach ($customers as $c)
                        <tr class="hover:bg-white/5">
                            <td class="px-6 py-4 text-gray-400">{{ $c['id'] }}</td>
                            <td class="px-3 py-4">
                                <p class="font-semibold">{{ $c['name'] }}</p>
                                <p class="text-gray-500 text-xs">{{ $c['phone'] }}</p>
                            </td>
                            <td class="px-3 py-4 text-gray-300">{{ $c['package'] }}</td>
                            <td class="px-3 py-4 text-gray-300">{{ $c['area'] }}</td>
                            <td class="px-3 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass[$c['status']] }}">&bull; {{ $c['status'] }}</span>
                            </td>
                            <td class="px-3 py-4 text-gray-300">{{ $c['due'] }}</td>
                            <td class="px-6 py-4 text-right font-semibold">{{ $c['amount'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="flex items-center justify-between px-6 py-4 border-t border-white/10">
                <span class="text-gray-400 text-sm">Menampilkan 8 dari 150 pelanggan</span>
                <div class="flex items-center gap-2">
                    <button type="button" disabled class="h-10 px-4 rounded-lg border border-white/10 text-gray-500 text-sm font-semibold cursor-not-allowed">Sebelumnya</button>
                    <button type="button" class="h-10 px-4 rounded-lg border border-white/15 text-sm font-semibold hover:bg-white/5">Berikutnya</button>
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
            <a href="/mockup/dashboard/pelanggan/tambah" class="h-11 px-5 inline-flex items-center rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold">Tambah Pelanggan</a>
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
