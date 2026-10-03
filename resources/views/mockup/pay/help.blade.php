<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bantuan — BILLINGIN</title>
    {{-- ponytail: static visual mockup (Bagian B8). No backend, nothing submits anywhere real. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white">

    <header class="max-w-sm mx-auto flex items-center justify-between py-4 px-4">
        <a href="#" class="flex items-center gap-1.5 text-indigo-400 font-semibold text-sm hover:text-indigo-300">← Kembali</a>
        <button type="button" class="w-10 h-10 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center" aria-label="Ganti tema">☀️</button>
    </header>

    <main class="max-w-sm mx-auto px-4 pb-10">
        <h1 class="text-2xl font-bold mb-5">Bantuan</h1>

        <div class="rounded-2xl bg-[#0B0F24] border border-white/10 divide-y divide-white/10 mb-5">
            <details class="p-5">
                <summary class="font-semibold cursor-pointer list-none flex items-center justify-between">Bagaimana cara bayar? <span>⌄</span></summary>
                <p class="mt-2 text-sm text-gray-400">Masukkan nomor internet Anda di halaman Cek Tagihan, lalu pilih metode pembayaran (QRIS, Virtual Account, e-wallet, minimarket, atau transfer manual).</p>
            </details>
            <details class="p-5">
                <summary class="font-semibold cursor-pointer list-none flex items-center justify-between">Kenapa internet saya masih terisolir? <span>⌄</span></summary>
                <p class="mt-2 text-sm text-gray-400">Internet aktif kembali otomatis dalam ±1 menit setelah pembayaran diterima. Jika lebih dari 10 menit belum aktif, hubungi admin.</p>
            </details>
            <details class="p-5">
                <summary class="font-semibold cursor-pointer list-none flex items-center justify-between">Bagaimana cara ganti paket? <span>⌄</span></summary>
                <p class="mt-2 text-sm text-gray-400">Hubungi admin via WhatsApp untuk mengganti paket. Perubahan berlaku mulai periode tagihan berikutnya.</p>
            </details>
            <details class="p-5">
                <summary class="font-semibold cursor-pointer list-none flex items-center justify-between">Saya salah transfer, bagaimana? <span>⌄</span></summary>
                <p class="mt-2 text-sm text-gray-400">Kirim bukti transfer ke admin via WhatsApp beserta nomor internet Anda. Admin akan membantu verifikasi dan penyesuaian.</p>
            </details>
        </div>

        <a href="#" class="flex items-center justify-center gap-2 w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">💬 Chat Admin via WhatsApp</a>

        <div class="mt-5 rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
            <h2 class="font-bold mb-1">Lapor gangguan</h2>
            <p class="text-sm text-gray-400 mb-4">Admin akan membuat tiket dan menghubungi Anda.</p>
            <form action="#" onsubmit="return false" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nomor internet</label>
                    <input type="text" placeholder="Masukkan nomor internet Anda" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Masalah</label>
                    <input type="text" placeholder="Contoh: internet putus sejak pagi" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="w-full h-12 rounded-lg border border-white/15 hover:border-white/30 font-semibold">Kirim Tiket</button>
            </form>
        </div>

        <p class="mt-5 text-center text-xs text-gray-500">Kontak admin: WhatsApp 0812****345</p>
        <p class="text-center text-xs text-gray-500">Senin–Sabtu 08.00–20.00 WIB</p>
    </main>
</body>
</html>
