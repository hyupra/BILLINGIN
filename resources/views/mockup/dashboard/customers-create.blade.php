@extends('mockup.dashboard.layout')

@section('title', 'Tambah Pelanggan — BILLINGIN')
@section('active', 'customers')
@section('back-link')
    <a href="/mockup/dashboard/pelanggan" class="text-indigo-400 hover:text-indigo-300">&larr; Kembali ke Pelanggan</a>
@endsection
@section('page-title', 'Tambah Pelanggan')

@section('content')
    <form action="#" onsubmit="return false" class="space-y-6 max-w-4xl">

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-5">Data pelanggan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama lengkap</label>
                    <input type="text" placeholder="Contoh: Budi Santoso" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nomor WhatsApp</label>
                    <input type="text" placeholder="0857xxxxxxx" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">NIK (opsional)</label>
                    <input type="text" placeholder="16 digit, tidak wajib" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium mb-1.5">Alamat pemasangan</label>
                    <input type="text" placeholder="Jalan, RT/RW, dusun" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-5">Layanan dan jaringan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Paket</label>
                    <select class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>Home 10 Mbps &middot; Rp 111.000</option>
                        <option>Home 20 Mbps &middot; Rp 166.500</option>
                        <option>Home 50 Mbps &middot; Rp 277.500</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Router</label>
                    <select class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>MT-Mekar-01</option>
                        <option>MT-Kampung-02</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Username PPPoE</label>
                    <input type="text" placeholder="budi-s-01" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Password PPPoE</label>
                    <input type="text" placeholder="Dibuat otomatis jika kosong" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-5">Penagihan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tanggal mulai</label>
                    <input type="date" value="2026-10-03" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tanggal isolir otomatis</label>
                    <select class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>Tanggal 10 setiap bulan</option>
                        <option>Tanggal 15 setiap bulan</option>
                        <option>Tanggal 20 setiap bulan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Biaya tambahan (Rp)</label>
                    <input type="number" value="0" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Diskon (Rp)</label>
                    <input type="number" value="0" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex items-center gap-6 flex-wrap">
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="billing_type" checked class="w-4 h-4 text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                    Pascabayar (tagih tiap bulan)
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="billing_type" class="w-4 h-4 text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                    Prabayar
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" checked class="w-4 h-4 rounded text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                    Kirim tagihan lewat WhatsApp
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Pelanggan</button>
            <a href="/mockup/dashboard/pelanggan" class="h-12 px-6 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Batal</a>
        </div>
    </form>
@endsection
