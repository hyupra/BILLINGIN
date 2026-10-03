<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bayar dengan QRIS — BILLINGIN</title>
    {{-- ponytail: static visual mockup (Bagian B4). No backend, nothing submits anywhere real. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white">

    <header class="max-w-sm mx-auto flex items-center justify-between py-4 px-4">
        <a href="#" class="flex items-center gap-1.5 text-indigo-400 font-semibold text-sm hover:text-indigo-300">← Ganti metode</a>
        <button type="button" class="w-10 h-10 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center" aria-label="Ganti tema">☀️</button>
    </header>

    <main class="max-w-sm mx-auto px-4 pb-10">
        <h1 class="text-2xl font-bold mb-1">Bayar dengan QRIS</h1>
        <p class="text-gray-400 text-sm mb-5">Buka aplikasi bank atau e-wallet, lalu pindai kode di bawah.</p>

        <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-6 text-center">
            <p class="text-sm text-gray-400 mb-1">Total pembayaran</p>
            <p class="text-3xl font-bold mb-5">Rp 162.600</p>

            <div id="qr-box" class="mx-auto w-56 h-56 rounded-xl bg-white/90 p-3 grid grid-cols-8 grid-rows-8 gap-0.5 transition-opacity">
                @for ($i = 0; $i < 64; $i++)
                    <span class="{{ (($i + intdiv($i, 8)) % 2 === 0) ? 'bg-[#0B0F24]' : 'bg-white/90' }} rounded-[1px]"></span>
                @endfor
            </div>
            <span class="inline-block mt-3 text-xs font-semibold px-2 py-0.5 rounded bg-amber-500/20 text-amber-400">QR contoh</span>

            {{-- NORMAL: live countdown --}}
            <p data-state="normal" class="mt-3 text-sm text-gray-400">Selesaikan dalam <span id="countdown" class="font-semibold text-white">09:58</span></p>

            {{-- EXPIRED --}}
            <div data-state="expired" hidden class="mt-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 text-sm px-3 py-2.5 flex items-start gap-2 text-left">
                <span>⚠️</span> Waktu pembayaran habis. Buat pembayaran baru.
            </div>
        </div>

        <div class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
            <h2 class="font-bold mb-3">Cara bayar</h2>
            <ol class="space-y-2 text-sm text-gray-300 list-decimal list-inside">
                <li>Buka aplikasi bank atau e-wallet Anda</li>
                <li>Pilih menu Scan atau QRIS</li>
                <li>Pindai kode, cek nominal, lalu konfirmasi</li>
            </ol>
        </div>

        <button type="button" onclick="forceExpire()" class="mt-5 w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Buat Pembayaran Baru</button>
    </main>

    <script>
        // Demo-length countdown (2 minutes) so the kedaluwarsa (expired) state
        // in the B-state reference sheet is reachable without waiting long.
        var secondsLeft = 120;
        var countdownEl = document.getElementById('countdown');
        var qrBox = document.getElementById('qr-box');
        var timer = setInterval(function () {
            secondsLeft--;
            if (secondsLeft <= 0) {
                forceExpire();
                return;
            }
            var m = Math.floor(secondsLeft / 60);
            var s = secondsLeft % 60;
            countdownEl.textContent = (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
        }, 1000);

        function forceExpire() {
            clearInterval(timer);
            qrBox.classList.add('opacity-30');
            document.querySelectorAll('[data-state]').forEach(function (el) {
                el.hidden = el.dataset.state !== 'expired';
            });
        }
    </script>
</body>
</html>
