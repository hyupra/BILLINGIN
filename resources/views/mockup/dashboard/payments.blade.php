@extends('mockup.dashboard.layout')

@section('title', 'Pembayaran — BILLINGIN')
@section('active', 'payments')
@section('page-title', 'Pembayaran')
@section('page-subtitle')
    Log transaksi dan rekonsiliasi dengan gateway.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <button type="button" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Ekspor CSV
    </button>
@endsection

@section('content')

    <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6 mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="font-bold mb-1">Rekonsiliasi dengan gateway</h2>
            <p class="text-gray-400 text-sm">Transaksi 3 Okt 2026 dibandingkan dengan data payment gateway.</p>
        </div>
        <div class="flex items-center gap-8">
            <div>
                <p class="text-gray-400 text-sm mb-1">Cocok</p>
                <p class="text-xl font-bold text-emerald-400">115</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Selisih</p>
                <p class="text-xl font-bold text-red-400">1</p>
            </div>
            <div>
                <p class="text-gray-400 text-sm mb-1">Menunggu</p>
                <p class="text-xl font-bold text-amber-400">3</p>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap mb-5" data-state-switch="payments">
        <button type="button" data-state-btn="data" onclick="setTableState('payments','data')" class="px-4 py-2 rounded-full text-sm font-semibold bg-indigo-600 text-white">Semua</button>
        <button type="button" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Berhasil</button>
        <button type="button" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Menunggu</button>
        <button type="button" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Gagal</button>
        <button type="button" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Kedaluwarsa</button>
    </div>

    <div class="flex items-center gap-2 mb-4 text-xs" data-state-switch="payments">
        <span class="text-gray-500">Lihat state tabel:</span>
        <button type="button" data-state-btn="data" onclick="setTableState('payments','data')" class="px-3 py-1.5 rounded-md font-semibold bg-indigo-600 text-white">Data</button>
        <button type="button" data-state-btn="loading" onclick="setTableState('payments','loading')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Memuat</button>
        <button type="button" data-state-btn="empty" onclick="setTableState('payments','empty')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Kosong</button>
        <button type="button" data-state-btn="error" onclick="setTableState('payments','error')" class="px-3 py-1.5 rounded-md font-semibold bg-[#0F1428] text-gray-400 border border-white/10">Error</button>
    </div>

    <div data-state-group="payments">

        <div data-state="data" class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400 border-b border-white/10">
                        <th class="px-6 py-3 font-medium">Waktu</th>
                        <th class="px-3 py-3 font-medium">No. referensi</th>
                        <th class="px-3 py-3 font-medium">Pelanggan</th>
                        <th class="px-3 py-3 font-medium">Metode</th>
                        <th class="px-3 py-3 font-medium text-right">Jumlah</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Rekonsiliasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @php
                        $payments = [
                            ['time' => '10.42', 'ref' => 'BLN-261003-0042', 'name' => 'Budi S*****', 'phone' => '0857****695', 'method' => 'QRIS', 'amount' => 'Rp 162.600', 'status' => 'Berhasil', 'recon' => 'Cocok'],
                            ['time' => '10.15', 'ref' => 'BLN-261003-0041', 'name' => 'Siti R*****', 'phone' => '0812****204', 'method' => 'VA BCA', 'amount' => 'Rp 113.500', 'status' => 'Berhasil', 'recon' => 'Cocok'],
                            ['time' => '09.58', 'ref' => 'BLN-261003-0040', 'name' => 'Hendra T*****', 'phone' => '0852****318', 'method' => 'Alfamart', 'amount' => 'Rp 115.000', 'status' => 'Berhasil', 'recon' => 'Selisih'],
                            ['time' => '09.31', 'ref' => 'BLN-261003-0039', 'name' => 'Ani W*****', 'phone' => '0878****562', 'method' => 'QRIS', 'amount' => 'Rp 167.600', 'status' => 'Berhasil', 'recon' => 'Cocok'],
                            ['time' => '09.12', 'ref' => 'BLN-261003-0038', 'name' => 'Dewi L*****', 'phone' => '0813****119', 'method' => 'VA BRI', 'amount' => 'Rp 169.000', 'status' => 'Menunggu', 'recon' => 'Belum'],
                            ['time' => '08.47', 'ref' => 'BLN-261003-0037', 'name' => 'Maya K*****', 'phone' => '0838****026', 'method' => 'GoPay', 'amount' => 'Rp 113.000', 'status' => 'Gagal', 'recon' => 'Belum'],
                            ['time' => '08.20', 'ref' => 'BLN-261003-0036', 'name' => 'Agus P*****', 'phone' => '0821****887', 'method' => 'QRIS', 'amount' => 'Rp 278.600', 'status' => 'Kedaluwarsa', 'recon' => 'Belum'],
                            ['time' => '07.55', 'ref' => 'BLN-261003-0035', 'name' => 'Joko S*****', 'phone' => '0819****771', 'method' => 'Transfer manual', 'amount' => 'Rp 277.500', 'status' => 'Menunggu', 'recon' => 'Belum'],
                        ];
                        $statusClass = [
                            'Berhasil' => 'bg-emerald-500/15 text-emerald-400',
                            'Menunggu' => 'bg-amber-500/15 text-amber-400',
                            'Gagal' => 'bg-red-500/15 text-red-400',
                            'Kedaluwarsa' => 'bg-white/10 text-gray-400',
                        ];
                        $reconClass = [
                            'Cocok' => 'bg-emerald-500/15 text-emerald-400',
                            'Selisih' => 'bg-red-500/15 text-red-400',
                            'Belum' => 'bg-white/10 text-gray-400',
                        ];
                    @endphp
                    @foreach ($payments as $p)
                        <tr class="hover:bg-white/5">
                            <td class="px-6 py-4 text-gray-300">{{ $p['time'] }}</td>
                            <td class="px-3 py-4 text-gray-300">{{ $p['ref'] }}</td>
                            <td class="px-3 py-4">
                                <p class="font-semibold">{{ $p['name'] }}</p>
                                <p class="text-gray-500 text-xs">{{ $p['phone'] }}</p>
                            </td>
                            <td class="px-3 py-4 text-gray-300">{{ $p['method'] }}</td>
                            <td class="px-3 py-4 text-right font-semibold">{{ $p['amount'] }}</td>
                            <td class="px-3 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass[$p['status']] }}">&bull; {{ $p['status'] }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $reconClass[$p['recon']] }}">&bull; {{ $p['recon'] }}</span>
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
            <div class="w-14 h-14 rounded-full bg-white/5 flex items-center justify-center mb-4 text-2xl">💳</div>
            <p class="font-bold mb-1">Belum ada transaksi</p>
            <p class="text-gray-400 text-sm">Transaksi pembayaran pelanggan akan muncul di sini.</p>
        </div>

        <div data-state="error" class="hidden rounded-xl border border-white/10 bg-[#0B0F24] p-16 flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-full bg-red-500/15 flex items-center justify-center mb-4 text-2xl">⚠️</div>
            <p class="font-bold mb-1">Data pembayaran gagal dimuat</p>
            <p class="text-gray-400 text-sm mb-5">Terjadi gangguan pada server. Coba lagi beberapa saat.</p>
            <button type="button" onclick="setTableState('payments','data')" class="h-11 px-5 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">⟳ Coba Lagi</button>
        </div>

    </div>
@endsection
