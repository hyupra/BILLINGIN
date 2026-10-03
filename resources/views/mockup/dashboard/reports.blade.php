@extends('mockup.dashboard.layout')

@section('title', 'Laporan — BILLINGIN')
@section('active', 'reports')
@section('page-title', 'Laporan')
@section('page-subtitle')
    Pendapatan dan tunggakan per periode.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <button type="button" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Ekspor CSV
    </button>
    <button type="button" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        Ekspor Excel
    </button>
@endsection

@section('content')

    <div class="flex items-center gap-2 mb-6">
        <button type="button" onclick="setActiveFilterPill(this)" class="px-4 py-2 rounded-full text-sm font-semibold bg-indigo-600 text-white">Okt 2026</button>
        <button type="button" onclick="setActiveFilterPill(this)" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Sep 2026</button>
        <button type="button" onclick="setActiveFilterPill(this)" class="px-4 py-2 rounded-full text-sm font-semibold border border-white/15 text-gray-300 hover:bg-white/5">Agu 2026</button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Pendapatan</p>
            <p class="text-2xl font-bold">Rp 14.260.000</p>
            <p class="text-gray-500 text-sm mt-1">dari 116 pembayaran</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Tunggakan</p>
            <p class="text-2xl font-bold text-amber-400">Rp 4.190.000</p>
            <p class="text-gray-500 text-sm mt-1">34 tagihan</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Tingkat tertagih</p>
            <p class="text-2xl font-bold text-emerald-400">77%</p>
            <p class="text-gray-500 text-sm mt-1">terbayar dari total tagihan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-5">Pendapatan per metode bayar</h2>
            <div class="space-y-4">
                @foreach ([
                    ['label' => 'QRIS', 'pct' => 48],
                    ['label' => 'Virtual Account', 'pct' => 27],
                    ['label' => 'Minimarket', 'pct' => 14],
                    ['label' => 'E-wallet', 'pct' => 8],
                    ['label' => 'Transfer manual', 'pct' => 3],
                ] as $row)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span>{{ $row['label'] }}</span>
                            <span class="font-semibold">{{ $row['pct'] }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden">
                            <div class="h-full bg-indigo-500" style="width: {{ $row['pct'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="font-bold">Umur tunggakan</h2>
                <p class="text-gray-500 text-xs mt-1">Dari 12 tagihan yang lewat jatuh tempo.</p>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400">
                        <th class="px-6 py-3 font-medium">Keterlambatan</th>
                        <th class="px-3 py-3 font-medium">Tagihan</th>
                        <th class="px-6 py-3 font-medium text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ([
                        ['label' => '1-7 hari', 'count' => 7, 'amount' => 'Rp 980.000'],
                        ['label' => '8-14 hari', 'count' => 3, 'amount' => 'Rp 700.000'],
                        ['label' => 'Lebih dari 14 hari', 'count' => 2, 'amount' => 'Rp 480.000'],
                    ] as $row)
                        <tr>
                            <td class="px-6 py-4">{{ $row['label'] }}</td>
                            <td class="px-3 py-4">{{ $row['count'] }}</td>
                            <td class="px-6 py-4 text-right font-semibold">{{ $row['amount'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
