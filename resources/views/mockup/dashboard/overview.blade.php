@extends('mockup.dashboard.layout')

@section('title', 'Ringkasan — BILLINGIN')
@section('active', 'overview')
@section('page-title', 'Ringkasan')
@section('page-subtitle')
    Posisi tagihan Okt 2026.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection

@section('content')

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Tagihan bulan ini</p>
            <p class="text-2xl font-bold">Rp 18.450.000</p>
            <p class="text-gray-500 text-sm mt-1">150 tagihan</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Terbayar</p>
            <p class="text-2xl font-bold text-emerald-400">Rp 14.260.000</p>
            <p class="text-gray-500 text-sm mt-1">116 tagihan lunas</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Tunggakan</p>
            <p class="text-2xl font-bold text-amber-400">Rp 4.190.000</p>
            <p class="text-gray-500 text-sm mt-1">34 tagihan belum lunas</p>
        </div>
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
            <p class="text-gray-400 text-sm mb-2">Pelanggan isolir</p>
            <p class="text-2xl font-bold text-red-400">12</p>
            <p class="text-gray-500 text-sm mt-1">dari 150 pelanggan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Revenue bar chart (static bars, no chart lib) --}}
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold">Pendapatan 6 bulan terakhir</h2>
                <span class="text-gray-500 text-sm">dalam juta rupiah</span>
            </div>
            @php
                $bars = [
                    ['label' => 'Mei', 'value' => 15.8],
                    ['label' => 'Jun', 'value' => 16.4],
                    ['label' => 'Jul', 'value' => 16.9],
                    ['label' => 'Agu', 'value' => 17.5],
                    ['label' => 'Sep', 'value' => 17.9],
                    ['label' => 'Okt (berjalan)', 'value' => 14.3, 'current' => true],
                ];
                $max = 18;
            @endphp
            <div class="flex items-end justify-between gap-3 h-48">
                @foreach ($bars as $bar)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <span class="text-sm font-semibold">{{ number_format($bar['value'], 1, ',', '.') }}</span>
                        {{-- fixed-height track so the percentage-height bar below has a real
                             pixel height to size against (a % height needs a sized parent) --}}
                        <div class="w-full h-32 flex items-end">
                            <div class="w-full rounded-t-md {{ !empty($bar['current']) ? 'bg-indigo-400/70' : 'bg-indigo-600' }}"
                                 style="height: {{ ($bar['value'] / $max) * 100 }}%"></div>
                        </div>
                        <span class="text-gray-500 text-xs text-center">{{ $bar['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Billing status --}}
        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6 flex flex-col">
            <h2 class="font-bold mb-4">Status tagihan Okt 2026</h2>
            <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden mb-4">
                <div class="h-full bg-indigo-500" style="width: 77%"></div>
            </div>
            <div class="space-y-3 mb-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-gray-300"><span class="w-2 h-2 rounded-full bg-emerald-400"></span>Lunas</span>
                    <span class="font-semibold">116</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-gray-300"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Belum lunas</span>
                    <span class="font-semibold">22</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-2 text-gray-300"><span class="w-2 h-2 rounded-full bg-red-400"></span>Lewat jatuh tempo</span>
                    <span class="font-semibold">12</span>
                </div>
            </div>
            <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300 mb-4">
                ⚠ 9 pelanggan akan terisolir pada 15 Okt jika belum membayar.
            </div>
            <a href="/mockup/dashboard/invoices" class="mt-auto w-full text-center h-11 flex items-center justify-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">
                Lihat Tagihan
            </a>
        </div>
    </div>

    {{-- Recent transactions --}}
    <div class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
            <h2 class="font-bold">Transaksi terbaru</h2>
            <a href="/mockup/dashboard/payments" class="text-indigo-400 hover:text-indigo-300 text-sm">Lihat semua</a>
        </div>
        <div class="divide-y divide-white/10">
            @foreach ([
                ['name' => 'Budi S*****', 'via' => 'QRIS', 'time' => '10.42', 'amount' => 'Rp 162.600', 'status' => 'Berhasil'],
                ['name' => 'Siti R*****', 'via' => 'VA BCA', 'time' => '10.15', 'amount' => 'Rp 113.500', 'status' => 'Berhasil'],
                ['name' => 'Hendra T*****', 'via' => 'Alfamart', 'time' => '09.58', 'amount' => 'Rp 115.000', 'status' => 'Berhasil'],
                ['name' => 'Ani W*****', 'via' => 'QRIS', 'time' => '09.31', 'amount' => 'Rp 167.600', 'status' => 'Berhasil'],
                ['name' => 'Dewi L*****', 'via' => 'VA BRI', 'time' => '09.12', 'amount' => 'Rp 169.000', 'status' => 'Menunggu'],
            ] as $t)
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <p class="font-semibold">{{ $t['name'] }}</p>
                        <p class="text-gray-500 text-sm">{{ $t['via'] }} &middot; {{ $t['time'] }} WIB</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-semibold">{{ $t['amount'] }}</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $t['status'] === 'Berhasil' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' }}">
                            &bull; {{ $t['status'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
