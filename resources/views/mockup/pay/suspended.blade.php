<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layanan Terisolir — BILLINGIN</title>
    {{-- ponytail: static visual mockup (Bagian B6). No backend, nothing submits anywhere real. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white">

    <header class="max-w-sm mx-auto flex items-center justify-between py-4 px-4">
        <a href="#" class="flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">📶</span>
            <span class="font-bold text-lg">BILLING<span class="text-indigo-400">IN</span></span>
        </a>
        <button type="button" class="w-10 h-10 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center" aria-label="Ganti tema">☀️</button>
    </header>

    <main class="max-w-sm mx-auto px-4 pb-10">
        <div class="rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm px-4 py-3 flex items-start gap-2 mb-4 font-semibold">
            <span>🚫</span> Layanan internet Anda sementara dinonaktifkan karena tagihan belum dibayar.
        </div>

        <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
            <div class="flex items-center justify-between mb-1">
                <span class="font-bold">BUDI S*****</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-500/15 text-red-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Terisolir
                </span>
            </div>
            <p class="text-xs text-gray-400 mb-4">ID 12****67 • Home 20 Mbps</p>
            <div class="h-px bg-white/10 mb-4"></div>
            <div class="flex items-center justify-between mb-2"><span class="text-sm text-gray-400">Periode tertunggak</span><span class="font-semibold text-sm">Okt 2026</span></div>
            <div class="flex items-center justify-between mb-5"><span class="text-sm text-gray-400">Jatuh tempo</span><span class="font-semibold text-sm">10 Okt 2026</span></div>
            <p class="text-sm text-gray-400 mb-1">Total tunggakan</p>
            <p class="text-3xl font-bold mb-5">Rp 161.500</p>
            <a href="#" class="block text-center w-full h-12 leading-[3rem] rounded-lg bg-indigo-600 hover:bg-indigo-500 font-bold uppercase tracking-wide transition">Bayar Sekarang</a>
        </div>

        <div class="mt-4 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-sm px-4 py-3 flex items-start gap-2">
            <span>⚡</span> Setelah pembayaran masuk, internet aktif kembali dalam ±1 menit.
        </div>

        <div class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
            <h2 class="font-bold mb-2">Butuh bantuan?</h2>
            <p class="text-sm text-gray-400 mb-1">WhatsApp admin 0812****345</p>
            <p class="text-sm text-gray-400 mb-4">Senin–Sabtu, 08.00–20.00 WIB</p>
            <a href="#" class="flex items-center justify-center gap-2 w-full h-12 rounded-lg border border-white/15 hover:border-white/30 font-semibold">💬 Hubungi Admin</a>
        </div>
    </main>
</body>
</html>
