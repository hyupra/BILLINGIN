@extends('mockup.dashboard.layout')

@section('title', 'Pengaturan — BILLINGIN')
@section('active', 'settings')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Profil usaha, metode bayar, template WhatsApp, dan alamat halaman bayar.')

@section('content')

    <div class="border-b border-white/10 mb-6 flex items-center gap-6 text-sm overflow-x-auto">
        <button type="button" class="pb-3 border-b-2 border-indigo-500 text-white font-semibold whitespace-nowrap">Profil usaha</button>
        <button type="button" class="pb-3 border-b-2 border-transparent text-gray-400 hover:text-white whitespace-nowrap">Metode bayar</button>
        <button type="button" class="pb-3 border-b-2 border-transparent text-gray-400 hover:text-white whitespace-nowrap">Template WhatsApp</button>
        <button type="button" class="pb-3 border-b-2 border-transparent text-gray-400 hover:text-white whitespace-nowrap">Alamat halaman bayar</button>
    </div>

    <form action="#" onsubmit="return false" class="rounded-xl border border-white/10 bg-[#0B0F24] p-6 max-w-4xl space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">Nama usaha</label>
                <input type="text" value="RTRW Net Mekar Jaya" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Nama pemilik</label>
                <input type="text" value="Pak Andi" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">WhatsApp admin</label>
                <input type="text" value="0812****345" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">Jam layanan</label>
            <input type="text" value="Senin-Sabtu 08.00-20.00 WIB" class="w-full max-w-sm h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">Alamat</label>
            <input type="text" value="Jl. Mekar Raya No. **, Desa Mekar" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">Catatan di bawah halaman bayar</label>
            <textarea rows="4" class="w-full px-4 py-3 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">Promo bulan ini: bayar 3 bulan sekaligus. Layanan akan terisolir pada [ISOLIR] jika belum dibayar.</textarea>
            <p class="text-gray-500 text-xs mt-1.5">Token tersedia: [ISOLIR], [NAMA], [PAKET].</p>
        </div>

        <div class="flex items-center justify-between py-2">
            <div>
                <p class="font-semibold text-sm">Tampilkan periode pemakaian</p>
                <p class="text-gray-500 text-sm">Periode ditampilkan di detail tagihan pelanggan</p>
            </div>
            <button type="button" class="w-12 h-7 rounded-full bg-indigo-600 relative shrink-0">
                <span class="absolute top-1 right-1 w-5 h-5 rounded-full bg-white"></span>
            </button>
        </div>

        <div class="flex items-center justify-between py-2 border-t border-white/10 pt-5">
            <div>
                <p class="font-semibold text-sm">Izinkan pendaftaran online</p>
                <p class="text-gray-500 text-sm">Calon pelanggan bisa mendaftar dari halaman bayar</p>
            </div>
            <button type="button" class="w-12 h-7 rounded-full bg-indigo-600 relative shrink-0">
                <span class="absolute top-1 right-1 w-5 h-5 rounded-full bg-white"></span>
            </button>
        </div>

        <button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Perubahan</button>
    </form>
@endsection
