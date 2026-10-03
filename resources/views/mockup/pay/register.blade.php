<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pemasangan — BILLINGIN</title>
    {{-- ponytail: static visual mockup (Bagian B7). No backend, nothing submits anywhere real.
         Stepper has real show/hide behaviour; no field validation. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white">

    <header class="max-w-sm mx-auto flex items-center justify-between py-4 px-4">
        <a href="#" class="flex items-center gap-1.5 text-indigo-400 font-semibold text-sm hover:text-indigo-300">← Kembali</a>
        <button type="button" class="w-10 h-10 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center" aria-label="Ganti tema">☀️</button>
    </header>

    <main class="max-w-sm mx-auto px-4 pb-10">
        <h1 class="text-2xl font-bold mb-5">Daftar Pemasangan</h1>

        <div class="flex items-center mb-6" id="stepper">
            <div class="flex flex-col items-center" data-step-pill="1">
                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white text-sm font-bold flex items-center justify-center">1</span>
                <span class="text-[11px] font-semibold mt-1 text-center">Data<br>diri</span>
            </div>
            <span class="flex-1 h-px bg-white/15 mx-1 mb-5"></span>
            <div class="flex flex-col items-center" data-step-pill="2">
                <span class="w-7 h-7 rounded-full bg-[#0F1428] border border-white/15 text-gray-400 text-sm font-bold flex items-center justify-center">2</span>
                <span class="text-[11px] text-gray-400 mt-1 text-center">Lokasi &amp;<br>paket</span>
            </div>
            <span class="flex-1 h-px bg-white/15 mx-1 mb-5"></span>
            <div class="flex flex-col items-center" data-step-pill="3">
                <span class="w-7 h-7 rounded-full bg-[#0F1428] border border-white/15 text-gray-400 text-sm font-bold flex items-center justify-center">3</span>
                <span class="text-[11px] text-gray-400 mt-1 text-center">Konfirmasi</span>
            </div>
        </div>

        <form action="#" onsubmit="return false">
            {{-- STEP 1 --}}
            <div data-step="1" class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama lengkap</label>
                    <input type="text" placeholder="Sesuai identitas" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nomor WhatsApp</label>
                    <div class="flex gap-2">
                        <input type="text" placeholder="08xx xxxx xxxx" class="flex-1 h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" class="h-12 px-4 rounded-lg border border-white/15 hover:border-white/30 font-semibold text-sm whitespace-nowrap">Kirim OTP</button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5">Kode OTP dikirim ke WhatsApp untuk verifikasi.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">NIK (opsional)</label>
                    <input type="text" placeholder="16 digit" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1.5">NIK dienkripsi dan tidak pernah ditampilkan kembali.</p>
                </div>
            </div>

            {{-- STEP 2 --}}
            <div data-step="2" hidden class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Alamat pemasangan</label>
                    <textarea rows="3" placeholder="Jl., No. rumah, RT/RW, Desa" class="w-full px-4 py-3 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Pilih paket</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl bg-[#0F1428] border border-white/10 cursor-pointer">
                            <input type="radio" name="paket" class="w-4 h-4 accent-indigo-500">
                            <span class="flex-1 text-sm font-semibold">Home 10 Mbps</span>
                            <span class="text-sm text-gray-400">Rp 100.000/bln</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl bg-[#0F1428] border-2 border-indigo-500 cursor-pointer">
                            <input type="radio" name="paket" checked class="w-4 h-4 accent-indigo-500">
                            <span class="flex-1 text-sm font-semibold">Home 20 Mbps</span>
                            <span class="text-sm text-gray-400">Rp 150.000/bln</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl bg-[#0F1428] border border-white/10 cursor-pointer">
                            <input type="radio" name="paket" class="w-4 h-4 accent-indigo-500">
                            <span class="flex-1 text-sm font-semibold">Home 50 Mbps</span>
                            <span class="text-sm text-gray-400">Rp 300.000/bln</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- STEP 3 --}}
            <div data-step="3" hidden class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-3">
                <h2 class="font-bold mb-1">Konfirmasi data</h2>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Nama</span><span class="font-semibold">Budi S*****</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">WhatsApp</span><span class="font-semibold">0812****345</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Alamat</span><span class="font-semibold text-right">Jl. Melati No. **</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Paket</span><span class="font-semibold">Home 20 Mbps</span></div>
                <div class="h-px bg-white/10 my-1"></div>
                <p class="text-xs text-gray-500">Dengan mendaftar, Anda setuju dihubungi admin untuk jadwal pemasangan.</p>
            </div>

            <div class="flex gap-3 mt-5">
                <button type="button" data-prev hidden onclick="goStep(-1)" class="flex-1 h-12 rounded-lg border border-white/15 hover:border-white/30 font-semibold">Kembali</button>
                <button type="button" data-next onclick="goStep(1)" class="flex-1 h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Lanjut</button>
            </div>
        </form>
    </main>

    <script>
        var current = 1;
        var total = 3;

        function render() {
            document.querySelectorAll('[data-step]').forEach(function (el) {
                el.hidden = parseInt(el.dataset.step, 10) !== current;
            });
            document.querySelectorAll('[data-step-pill]').forEach(function (pill) {
                var n = parseInt(pill.dataset.stepPill, 10);
                var circle = pill.querySelector('span:first-child');
                var active = n === current;
                var done = n < current;
                circle.classList.toggle('bg-indigo-600', active);
                circle.classList.toggle('text-white', active);
                circle.classList.toggle('border', !active);
                circle.classList.toggle('border-white/15', !active);
                circle.classList.toggle('bg-[#0F1428]', !active && !done);
                circle.classList.toggle('bg-emerald-600', done);
                circle.classList.toggle('border-0', done);
                circle.textContent = done ? '✓' : n;
            });
            document.querySelector('[data-prev]').hidden = current === 1;
            document.querySelector('[data-next]').textContent = current === total ? 'Daftar Sekarang' : 'Lanjut';
        }

        function goStep(delta) {
            current = Math.min(total, Math.max(1, current + delta));
            render();
        }

        render();
    </script>
</body>
</html>
