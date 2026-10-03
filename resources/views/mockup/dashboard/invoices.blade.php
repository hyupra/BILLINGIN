@extends('mockup.dashboard.layout')

@section('title', 'Tagihan — BILLINGIN')
@section('active', 'invoices')
@section('page-title', 'Tagihan')
@section('page-subtitle')
    Periode Okt 2026.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <button type="button" onclick="mockupToast('Pengingat massal belum aktif di mockup ini')" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12a9 9 0 11-9-9 9 9 0 019 9z"/></svg>
        Kirim Pengingat Massal
    </button>
@endsection

@section('content')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Total tagihan</p>
            <p class="text-2xl font-bold">Rp 18.450.000</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Lunas</p>
            <p class="text-2xl font-bold text-emerald-400">116</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Belum lunas</p>
            <p class="text-2xl font-bold text-amber-400">22</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Lewat jatuh tempo</p>
            <p class="text-2xl font-bold text-red-400">12</p>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap mb-5" data-state-switch="invoices">
        <button type="button" data-state-btn="data" onclick="setActiveFilterPill(this); setTableState('invoices','data')" class="px-4 py-2 rounded-full text-sm font-semibold bg-indigo-600 text-white">Semua 150</button>
        <button type="button" onclick="setActiveFilterPill(this); mockupToast('Daftar di bawah belum benar-benar terfilter di mockup ini')" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Lunas 116</button>
        <button type="button" onclick="setActiveFilterPill(this); mockupToast('Daftar di bawah belum benar-benar terfilter di mockup ini')" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Belum lunas 22</button>
        <button type="button" onclick="setActiveFilterPill(this); mockupToast('Daftar di bawah belum benar-benar terfilter di mockup ini')" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Lewat jatuh tempo 12</button>
    </div>

    <div class="flex items-center gap-2 mb-4 text-xs" data-state-switch="invoices">
        <span class="text-gray-500">Lihat state tabel:</span>
        <button type="button" data-state-btn="data" onclick="setTableState('invoices','data')" class="px-3 py-1.5 rounded-md font-semibold bg-indigo-600 text-white">Data</button>
        <button type="button" data-state-btn="loading" onclick="setTableState('invoices','loading')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Memuat</button>
        <button type="button" data-state-btn="empty" onclick="setTableState('invoices','empty')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Kosong</button>
        <button type="button" data-state-btn="error" onclick="setTableState('invoices','error')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Error</button>
    </div>

    <div data-state-group="invoices">

        <div data-state="data" class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-white/10">
                        <th class="px-6 py-3 font-medium">No. invoice</th>
                        <th class="px-3 py-3 font-medium">Pelanggan</th>
                        <th class="px-3 py-3 font-medium">Periode</th>
                        <th class="px-3 py-3 font-medium">Jatuh tempo</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-3 py-3 font-medium text-right">Jumlah</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @php
                        $invoices = [
                            ['no' => 'INV-2610-0042', 'name' => 'Budi S*****', 'phone' => '0857****695', 'due' => '10 Okt 2026', 'status' => 'Lunas', 'amount' => 'Rp 161.500'],
                            ['no' => 'INV-2610-0043', 'name' => 'Siti R*****', 'phone' => '0812****204', 'due' => '10 Okt 2026', 'status' => 'Lunas', 'amount' => 'Rp 111.000'],
                            ['no' => 'INV-2610-0051', 'name' => 'Agus P*****', 'phone' => '0821****887', 'due' => '15 Sep 2026', 'status' => 'Lewat jatuh tempo', 'amount' => 'Rp 277.500'],
                            ['no' => 'INV-2610-0060', 'name' => 'Dewi L*****', 'phone' => '0813****119', 'due' => '12 Okt 2026', 'status' => 'Belum lunas', 'amount' => 'Rp 166.500'],
                            ['no' => 'INV-2610-0072', 'name' => 'Ani W*****', 'phone' => '0878****562', 'due' => '15 Okt 2026', 'status' => 'Lunas', 'amount' => 'Rp 166.500'],
                            ['no' => 'INV-2610-0080', 'name' => 'Joko S*****', 'phone' => '0819****771', 'due' => '22 Sep 2026', 'status' => 'Lewat jatuh tempo', 'amount' => 'Rp 277.500'],
                            ['no' => 'INV-2610-0085', 'name' => 'Maya K*****', 'phone' => '0838****026', 'due' => '20 Okt 2026', 'status' => 'Belum lunas', 'amount' => 'Rp 111.000'],
                            ['no' => 'INV-2610-0091', 'name' => 'Hendra T*****', 'phone' => '0852****318', 'due' => '10 Okt 2026', 'status' => 'Lunas', 'amount' => 'Rp 111.000'],
                        ];
                        $statusClass = [
                            'Lunas' => 'bg-emerald-500/15 text-emerald-400',
                            'Belum lunas' => 'bg-amber-500/15 text-amber-400',
                            'Lewat jatuh tempo' => 'bg-red-500/15 text-red-400',
                        ];
                    @endphp
                    @foreach ($invoices as $i)
                        <tr class="hover:bg-white/5">
                            <td class="px-6 py-4 text-gray-300">{{ $i['no'] }}</td>
                            <td class="px-3 py-4">
                                <p class="font-semibold">{{ $i['name'] }}</p>
                                <p class="text-gray-500 text-xs">{{ $i['phone'] }}</p>
                            </td>
                            <td class="px-3 py-4 text-gray-300">Okt 2026</td>
                            <td class="px-3 py-4 text-gray-300">{{ $i['due'] }}</td>
                            <td class="px-3 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass[$i['status']] }}">&bull; {{ $i['status'] }}</span>
                            </td>
                            <td class="px-3 py-4 text-right font-semibold">{{ $i['amount'] }}</td>
                            <td class="px-6 py-4 text-right">
                                @if ($i['status'] === 'Lunas')
                                    <button type="button" onclick="mockupToast('Struk belum tersedia di mockup ini')" class="text-indigo-400 hover:text-indigo-300 font-semibold">Lihat struk</button>
                                @else
                                    <button type="button" onclick="mockupToast('Pengiriman pengingat belum aktif di mockup ini')" class="h-9 px-3 rounded-lg border border-white/15 text-xs font-semibold hover:bg-white/5">Kirim pengingat</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div data-state="loading" class="hidden rounded-xl border border-white/10 bg-[#0B0F24] p-6 space-y-3">
            @for ($i = 0; $i < 6; $i++)
                <div class="h-10 rounded-lg bg-white/5 animate-pulse"></div>
            @endfor
        </div>

        <div data-state="empty" class="hidden rounded-xl border border-white/10 bg-[#0B0F24] p-16 flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-white/5 flex items-center justify-center mb-4 text-2xl">🧾</div>
            <p class="font-bold mb-1">Belum ada tagihan periode ini</p>
            <p class="text-gray-400 text-sm">Tagihan akan muncul otomatis setelah periode berjalan dimulai.</p>
        </div>

        <div data-state="error" class="hidden rounded-xl border border-white/10 bg-[#0B0F24] p-16 flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-red-500/15 flex items-center justify-center mb-4 text-2xl">⚠️</div>
            <p class="font-bold mb-1">Data tagihan gagal dimuat</p>
            <p class="text-gray-400 text-sm mb-5">Terjadi gangguan pada server. Coba lagi beberapa saat.</p>
            <button type="button" onclick="setTableState('invoices','data')" class="h-11 px-5 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">⟳ Coba Lagi</button>
        </div>

    </div>
@endsection
