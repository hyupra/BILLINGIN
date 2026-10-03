@extends('mockup.dashboard.layout')

@section('title', 'Langganan — BILLINGIN')
@section('active', 'subscription')
@section('page-title', 'Langganan BILLINGIN')
@section('page-subtitle')
    Tagihan platform untuk usaha Anda.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection

@section('content')

    <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold">Paket saat ini</h2>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400">&bull; Aktif</span>
        </div>
        <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-gray-300">150 pelanggan &times; Rp 350</span>
                <span>Rp 52.500</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-300">2 router &times; Rp 5.000</span>
                <span>Rp 10.000</span>
            </div>
        </div>
        <div class="border-t border-white/10 mt-4 pt-4 flex items-center justify-between">
            <span class="font-bold">Total per bulan</span>
            <span class="text-xl font-bold">Rp 62.500</span>
        </div>
        <p class="text-gray-500 text-xs mt-3">Tarif contoh. Tagihan mengikuti jumlah pelanggan aktif dan router terdaftar.</p>
    </div>

    <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6 mb-6 max-w-2xl">
        <h2 class="font-bold mb-4">Periode bayar</h2>
        <div class="space-y-3 mb-5">
            <label class="flex items-center justify-between rounded-lg border border-indigo-500/40 bg-indigo-500/5 px-4 py-3 cursor-pointer">
                <span class="flex items-center gap-3">
                    <input type="radio" name="period" checked class="w-4 h-4 text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                    <span>
                        <span class="block font-semibold">1 bulan</span>
                        <span class="block text-gray-400 text-sm">Rp 62.500</span>
                    </span>
                </span>
            </label>
            <label class="flex items-center justify-between rounded-lg border border-white/10 px-4 py-3 cursor-pointer">
                <span class="flex items-center gap-3">
                    <input type="radio" name="period" class="w-4 h-4 text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                    <span>
                        <span class="block font-semibold">3 bulan</span>
                        <span class="block text-gray-400 text-sm">Rp 187.500 &middot; tanpa diskon pada contoh ini</span>
                    </span>
                </span>
            </label>
        </div>
        <button type="button" class="w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Bayar Rp 62.500</button>
        <p class="text-gray-500 text-xs text-center mt-3">Pembayaran memakai QRIS, transfer VA, atau e-wallet.</p>
    </div>

    <div class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10">
            <h2 class="font-bold">Riwayat tagihan langganan</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400">
                    <th class="px-6 py-3 font-medium">Periode</th>
                    <th class="px-3 py-3 font-medium">No. tagihan</th>
                    <th class="px-3 py-3 font-medium">Jumlah</th>
                    <th class="px-3 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach ([
                    ['period' => 'Okt 2026', 'no' => 'SUB-2610-01', 'amount' => 'Rp 62.500'],
                    ['period' => 'Sep 2026', 'no' => 'SUB-2609-01', 'amount' => 'Rp 60.000'],
                    ['period' => 'Agu 2026', 'no' => 'SUB-2608-01', 'amount' => 'Rp 57.500'],
                ] as $row)
                    <tr>
                        <td class="px-6 py-4">{{ $row['period'] }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $row['no'] }}</td>
                        <td class="px-3 py-4 font-semibold">{{ $row['amount'] }}</td>
                        <td class="px-3 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400">&bull; Lunas</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" class="h-9 px-4 rounded-lg border border-white/15 text-xs font-semibold hover:bg-white/5">Unduh</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
