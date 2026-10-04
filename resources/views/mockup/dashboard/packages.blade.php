@extends('mockup.dashboard.layout')

@section('title', 'Paket — BILLINGIN')
@section('active', 'packages')
@section('page-title', 'Paket')
@section('page-subtitle')
    Atur paket internet, harga, dan PPN.
    <span class="ml-1 inline-block px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 text-xs font-semibold align-middle">data contoh</span>
@endsection
@section('page-actions')
    <a href="{{ route('packages.create') }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tambah Paket
    </a>
@endsection

@section('content')
    @if (session('status'))
        <div class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('status') }}</div>
    @endif

    <div class="rounded-xl border border-white/10 bg-[#0B0F24] overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 border-b border-white/10">
                    <th class="px-6 py-3 font-medium">Paket</th>
                    <th class="px-3 py-3 font-medium">Kecepatan</th>
                    <th class="px-3 py-3 font-medium">Harga dasar</th>
                    <th class="px-3 py-3 font-medium">Total / bulan</th>
                    <th class="px-3 py-3 font-medium">Pelanggan</th>
                    <th class="px-3 py-3 font-medium">Daftar online</th>
                    <th class="px-3 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($packages as $pkg)
                    <tr class="hover:bg-white/5">
                        <td class="px-6 py-4 font-semibold">{{ $pkg->name }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $pkg->speed_label }}</td>
                        <td class="px-3 py-4 text-gray-300">Rp {{ number_format($pkg->base_price, 0, ',', '.') }}</td>
                        <td class="px-3 py-4 font-semibold">Rp {{ number_format($pkg->totalPrice(), 0, ',', '.') }}</td>
                        <td class="px-3 py-4 text-gray-300">{{ $pkg->customers_count }}</td>
                        <td class="px-3 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $pkg->allow_online_registration ? 'bg-emerald-500/15 text-emerald-400' : 'bg-white/10 text-gray-400' }}">&bull; {{ $pkg->allow_online_registration ? 'Ya' : 'Tidak' }}</span>
                        </td>
                        <td class="px-3 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $pkg->is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-white/10 text-gray-400' }}">&bull; {{ $pkg->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('packages.edit', $pkg) }}" class="h-9 px-4 inline-flex items-center rounded-lg border border-white/15 text-xs font-semibold hover:bg-white/5">Ubah</a>
                            @if ($pkg->is_active)
                                <form method="POST" action="{{ route('packages.destroy', $pkg) }}" class="inline" onsubmit="return confirm('Nonaktifkan paket {{ $pkg->name }}? Pelanggan yang masih memakainya tidak terpengaruh.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-9 px-4 rounded-lg border border-red-500/30 text-red-300 text-xs font-semibold hover:bg-red-500/10">Nonaktifkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400">Belum ada paket. <a href="{{ route('packages.create') }}" class="text-indigo-400 hover:text-indigo-300">Tambah paket pertama</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
