@extends('mockup.dashboard.layout')

@section('title', 'Paket — BILLINGIN')
@section('active', 'packages')
@section('page-title', 'Paket')
@section('page-subtitle')
    Atur paket internet, harga, dan PPN.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <button type="button" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Paket
    </button>
@endsection

@section('content')
    <div class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b border-white/10">
                    <th class="px-6 py-3 font-medium">Paket</th>
                    <th class="px-3 py-3 font-medium">Kecepatan</th>
                    <th class="px-3 py-3 font-medium">Harga dasar</th>
                    <th class="px-3 py-3 font-medium">PPN 11%</th>
                    <th class="px-3 py-3 font-medium">Total / bulan</th>
                    <th class="px-3 py-3 font-medium">Pelanggan</th>
                    <th class="px-3 py-3 font-medium">Daftar online</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @foreach ([
                    ['name' => 'Home 10 Mbps', 'speed' => '10 Mbps', 'base' => 'Rp 100.000', 'ppn' => 'Rp 11.000', 'total' => 'Rp 111.000', 'subs' => 48, 'online' => true],
                    ['name' => 'Home 20 Mbps', 'speed' => '20 Mbps', 'base' => 'Rp 150.000', 'ppn' => 'Rp 16.500', 'total' => 'Rp 166.500', 'subs' => 62, 'online' => true],
                    ['name' => 'Home 50 Mbps', 'speed' => '50 Mbps', 'base' => 'Rp 250.000', 'ppn' => 'Rp 27.500', 'total' => 'Rp 277.500', 'subs' => 40, 'online' => true],
                ] as $pkg)
                    <tr class="hover:bg-white/5">
                        <td class="px-6 py-4 font-semibold">{{ $pkg['name'] }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $pkg['speed'] }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $pkg['base'] }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $pkg['ppn'] }}</td>
                        <td class="px-3 py-4 font-semibold">{{ $pkg['total'] }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $pkg['subs'] }}</td>
                        <td class="px-3 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400">&bull; Ya</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button type="button" class="h-9 px-4 rounded-lg border border-white/15 text-xs font-semibold hover:bg-white/5">Ubah</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
