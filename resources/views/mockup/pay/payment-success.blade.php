<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Berhasil — BILLINGIN</title>
    {{-- ponytail: static visual mockup (Bagian B5). No backend, nothing submits anywhere real. --}}
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

    <main class="max-w-sm mx-auto px-4 pb-10 text-center">
        <div class="w-16 h-16 rounded-full bg-emerald-500/15 flex items-center justify-center mx-auto mt-4 mb-5">
            <span class="text-3xl text-emerald-400">✓</span>
        </div>
        <h1 class="text-2xl font-bold mb-1">Pembayaran berhasil. Terima kasih!</h1>
        <p class="text-3xl font-bold mb-6">Rp 162.600</p>

        <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 text-left space-y-3">
            <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Waktu</span><span class="font-semibold">3 Okt 2026, 10.42 WIB</span></div>
            <div class="flex items-center justify-between text-sm"><span class="text-gray-400">No. referensi</span><span class="font-semibold">BLN-261003-0042</span></div>
            <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Metode</span><span class="font-semibold">QRIS</span></div>
            <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Pelanggan</span><span class="font-semibold">BUDI S***** • 12****67</span></div>
            <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Paket / periode</span><span class="font-semibold">Home 20 Mbps • Okt 2026</span></div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-400">Status</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Lunas
                </span>
            </div>
        </div>

        <div class="mt-4 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-sm px-4 py-3 flex items-center justify-center gap-2 text-left">
            <span>⏱</span> Internet akan aktif kembali dalam ±1 menit.
        </div>

        <a href="#" class="mt-5 flex items-center justify-center gap-2 w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">⬇ Unduh Struk (PDF)</a>
        <a href="#" class="mt-3 flex items-center justify-center gap-2 w-full h-12 rounded-lg border border-white/15 hover:border-white/30 font-semibold">💬 Bagikan WhatsApp</a>
        <a href="#" class="mt-4 inline-block text-indigo-400 hover:text-indigo-300 font-semibold text-sm">Kembali</a>
    </main>
</body>
</html>
