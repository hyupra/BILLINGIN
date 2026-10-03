<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pilih Metode Pembayaran — BILLINGIN</title>
    {{-- ponytail: static visual mockup (Bagian B3). No backend, nothing submits anywhere real. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white">

    <header class="max-w-sm mx-auto flex items-center justify-between py-4 px-4">
        <a href="#" class="flex items-center gap-1.5 text-indigo-400 font-semibold text-sm hover:text-indigo-300">← Kembali</a>
        <button type="button" class="w-10 h-10 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center" aria-label="Ganti tema">☀️</button>
    </header>

    <main class="max-w-sm mx-auto px-4 pb-10">
        <h1 class="text-2xl font-bold mb-1">Pilih Metode Pembayaran</h1>
        <p class="text-gray-400 text-sm mb-2">Biaya admin ditampilkan sebelum Anda konfirmasi.</p>
        <span class="inline-block mb-5 text-xs font-semibold px-2 py-0.5 rounded bg-amber-500/20 text-amber-400">contoh</span>

        <form action="#" onsubmit="return false" id="metode-form" class="space-y-5">

            <div>
                <p class="text-xs font-bold tracking-wide text-gray-500 mb-2">REKOMENDASI</p>
                <label class="flex items-center gap-3 p-4 rounded-xl bg-[#0B0F24] border-2 border-indigo-500 cursor-pointer">
                    <input type="radio" name="metode" value="qris" data-fee="1100" checked onchange="updateTotal()" class="w-4 h-4 accent-indigo-500">
                    <span class="w-10 h-10 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center">▦</span>
                    <span class="flex-1">
                        <span class="block font-semibold text-sm">QRIS</span>
                        <span class="block text-xs text-gray-400">Instan, biaya terendah</span>
                    </span>
                    <span class="text-sm font-semibold">+Rp 1.100</span>
                </label>
            </div>

            <div>
                <p class="text-xs font-bold tracking-wide text-gray-500 mb-2">VIRTUAL ACCOUNT</p>
                <label class="flex items-center gap-3 p-4 rounded-xl bg-[#0B0F24] border border-white/10 cursor-pointer hover:border-white/20">
                    <input type="radio" name="metode" value="va" data-fee="2500" onchange="updateTotal()" class="w-4 h-4 accent-indigo-500">
                    <span class="w-10 h-10 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center">🏦</span>
                    <span class="flex-1">
                        <span class="block font-semibold text-sm">Virtual Account</span>
                        <span class="block text-xs text-gray-400">BCA, BRI, BNI, Mandiri, Permata</span>
                    </span>
                    <span class="text-sm font-semibold">+Rp 2.500</span>
                </label>
            </div>

            <div>
                <p class="text-xs font-bold tracking-wide text-gray-500 mb-2">E-WALLET</p>
                <label class="flex items-center gap-3 p-4 rounded-xl bg-[#0B0F24] border border-white/10 cursor-pointer hover:border-white/20">
                    <input type="radio" name="metode" value="ewallet" data-fee="2000" onchange="updateTotal()" class="w-4 h-4 accent-indigo-500">
                    <span class="w-10 h-10 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center">👛</span>
                    <span class="flex-1">
                        <span class="block font-semibold text-sm">E-wallet</span>
                        <span class="block text-xs text-gray-400">GoPay, OVO, DANA, ShopeePay, LinkAja</span>
                    </span>
                    <span class="text-sm font-semibold">+Rp 2.000</span>
                </label>
            </div>

            <div>
                <p class="text-xs font-bold tracking-wide text-gray-500 mb-2">MINIMARKET</p>
                <label class="flex items-center gap-3 p-4 rounded-xl bg-[#0B0F24] border border-white/10 cursor-pointer hover:border-white/20">
                    <input type="radio" name="metode" value="minimarket" data-fee="4000" onchange="updateTotal()" class="w-4 h-4 accent-indigo-500">
                    <span class="w-10 h-10 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center">🏪</span>
                    <span class="flex-1">
                        <span class="block font-semibold text-sm">Alfamart / Indomaret</span>
                        <span class="block text-xs text-gray-400">Bayar dengan kode di kasir</span>
                    </span>
                    <span class="text-sm font-semibold">+Rp 4.000</span>
                </label>
            </div>

            <div>
                <p class="text-xs font-bold tracking-wide text-gray-500 mb-2">TRANSFER MANUAL</p>
                <label class="flex items-center gap-3 p-4 rounded-xl bg-[#0B0F24] border border-white/10 cursor-pointer hover:border-white/20">
                    <input type="radio" name="metode" value="manual" data-fee="0" onchange="updateTotal()" class="w-4 h-4 accent-indigo-500">
                    <span class="w-10 h-10 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center">⬆</span>
                    <span class="flex-1">
                        <span class="block font-semibold text-sm">Transfer Bank manual</span>
                        <span class="block text-xs text-gray-400">Unggah bukti, diverifikasi admin</span>
                    </span>
                    <span class="text-sm font-semibold">Gratis</span>
                </label>
            </div>

            <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-2">
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Tagihan Okt 2026</span><span>Rp 161.500</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Biaya admin</span><span id="biaya-admin">Rp 1.100</span></div>
                <div class="h-px bg-white/10 my-1"></div>
                <div class="flex items-center justify-between"><span class="text-xs font-bold tracking-wide text-gray-400">TOTAL</span><span id="total-bayar" class="text-2xl font-bold">Rp 162.600</span></div>
            </div>

            <button type="submit" class="w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Lanjutkan</button>
        </form>
    </main>

    <script>
        var BASE_TAGIHAN = 161500;
        function formatRupiah(n) {
            return 'Rp ' + n.toLocaleString('id-ID');
        }
        function updateTotal() {
            var selected = document.querySelector('input[name="metode"]:checked');
            var fee = selected ? parseInt(selected.dataset.fee, 10) : 0;
            document.getElementById('biaya-admin').textContent = fee === 0 ? 'Gratis' : formatRupiah(fee);
            document.getElementById('total-bayar').textContent = formatRupiah(BASE_TAGIHAN + fee);
            document.querySelectorAll('input[name="metode"]').forEach(function (input) {
                var card = input.closest('label');
                card.classList.toggle('border-indigo-500', input.checked);
                card.classList.toggle('border-2', input.checked);
                card.classList.toggle('border', !input.checked);
                card.classList.toggle('border-white/10', !input.checked);
            });
        }
    </script>
</body>
</html>
