<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bayar dengan Virtual Account — BILLINGIN</title>
    {{-- ponytail: static visual mockup (Bagian B4b). No backend, nothing submits anywhere real. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white">

    <header class="max-w-sm mx-auto flex items-center justify-between py-4 px-4">
        <a href="#" class="flex items-center gap-1.5 text-indigo-400 font-semibold text-sm hover:text-indigo-300">← Ganti metode</a>
        <button type="button" class="w-10 h-10 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center" aria-label="Ganti tema">☀️</button>
    </header>

    <main class="max-w-sm mx-auto px-4 pb-10">
        <h1 class="text-2xl font-bold mb-1">Bayar dengan Virtual Account</h1>
        <p class="text-gray-400 text-sm mb-5">Transfer ke nomor di bawah lewat ATM, m-banking, atau internet banking.</p>

        <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
            <div class="flex items-center justify-between mb-4">
                <span class="font-bold">Bank BCA</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-500/15 text-amber-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Menunggu pembayaran
                </span>
            </div>

            <p class="text-xs text-gray-400 mb-1">Nomor Virtual Account</p>
            <div class="flex items-center justify-between gap-3 mb-4">
                <span id="va-number" class="text-xl font-bold tracking-wide">8077 0012 3456 7890</span>
                <button type="button" onclick="copyValue('va-number', this)" class="shrink-0 flex items-center gap-1.5 h-10 px-3 rounded-lg border border-white/15 hover:border-white/30 text-sm font-semibold">📋 <span>Salin</span></button>
            </div>

            <div class="h-px bg-white/10 mb-4"></div>

            <p class="text-xs text-gray-400 mb-1">Jumlah yang harus dibayar</p>
            <div class="flex items-center justify-between gap-3 mb-4">
                <span id="va-amount" class="text-2xl font-bold">Rp 164.000</span>
                <button type="button" onclick="copyValue('va-amount', this)" class="shrink-0 flex items-center gap-1.5 h-10 px-3 rounded-lg border border-white/15 hover:border-white/30 text-sm font-semibold">📋 <span>Salin</span></button>
            </div>

            <div class="rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-sm px-3 py-2.5 flex items-start gap-2">
                <span>⏱</span> Bayar sebelum 4 Okt 2026, 14.30 WIB. Setelah itu nomor tidak berlaku.
            </div>
        </div>

        <div class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 divide-y divide-white/10">
            <details class="p-5" open>
                <summary class="font-bold cursor-pointer list-none flex items-center justify-between">Lewat ATM <span>⌃</span></summary>
                <ol class="mt-3 space-y-2 text-sm text-gray-300 list-decimal list-inside">
                    <li>Pilih Transaksi Lainnya, lalu Transfer</li>
                    <li>Masukkan nomor Virtual Account</li>
                    <li>Cek nominal, lalu konfirmasi</li>
                </ol>
            </details>
            <details class="p-5">
                <summary class="font-bold cursor-pointer list-none flex items-center justify-between">Lewat m-banking <span>⌄</span></summary>
                <ol class="mt-3 space-y-2 text-sm text-gray-300 list-decimal list-inside">
                    <li>Buka aplikasi m-banking BCA</li>
                    <li>Pilih menu Transfer ke Virtual Account</li>
                    <li>Masukkan nomor VA, cek nominal, lalu konfirmasi</li>
                </ol>
            </details>
            <details class="p-5">
                <summary class="font-bold cursor-pointer list-none flex items-center justify-between">Lewat Alfamart / Indomaret <span>⌄</span></summary>
                <ol class="mt-3 space-y-2 text-sm text-gray-300 list-decimal list-inside">
                    <li>Datangi kasir dan sebutkan "pembayaran Virtual Account"</li>
                    <li>Berikan nomor VA di atas</li>
                    <li>Bayar sesuai nominal, simpan struk</li>
                </ol>
            </details>
        </div>

        <a href="#" class="mt-5 block text-center w-full h-12 leading-[3rem] rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Saya Sudah Bayar</a>
    </main>

    <script>
        function copyValue(id, btn) {
            var text = document.getElementById(id).textContent.trim();
            var label = btn.querySelector('span');
            function flash() {
                var original = label.textContent;
                label.textContent = 'Tersalin';
                setTimeout(function () { label.textContent = original; }, 1500);
            }
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(flash).catch(function () {});
            }
            // ponytail: no execCommand fallback for legacy browsers — mockup only, add if needed.
        }
    </script>
</body>
</html>
