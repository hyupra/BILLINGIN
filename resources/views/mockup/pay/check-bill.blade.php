<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Tagihan — BILLINGIN</title>
    {{-- ponytail: Tailwind via CDN, no build step yet. Swap for the real
         Vite+Tailwind pipeline (with the actual design tokens) in the
         Frontend sprint. This is a static visual mockup (Bagian B1) —
         no backend, nothing here submits anywhere real. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white">

    <header class="max-w-sm mx-auto lg:max-w-6xl flex items-center justify-between py-4 px-4">
        <a href="#" class="flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">📶</span>
            <span class="font-bold text-lg">BILLING<span class="text-indigo-400">IN</span></span>
        </a>
        <div class="flex items-center gap-4">
            <a href="#" class="text-indigo-400 text-sm font-semibold hover:text-indigo-300">Bantuan</a>
            <button type="button" class="w-10 h-10 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center" aria-label="Ganti tema">☀️</button>
        </div>
    </header>

    {{-- Mockup-only aid: lets a reviewer preview the required B1 states
         (loading / not-found / error) without wiring a backend. Not part
         of the design itself. --}}
    <div class="max-w-sm mx-auto lg:max-w-6xl px-4 mt-1 mb-4">
        <p class="text-[11px] uppercase tracking-wide text-gray-500 mb-1.5">Pratinjau status (alat bantu mockup)</p>
        <div class="flex flex-wrap gap-1.5">
            <button type="button" data-pill="normal" onclick="setState('normal')" class="text-xs px-2.5 py-1 rounded-md bg-indigo-600 text-white">Normal</button>
            <button type="button" data-pill="loading" onclick="setState('loading')" class="text-xs px-2.5 py-1 rounded-md bg-[#0F1428] border border-white/10 text-gray-300">Memuat</button>
            <button type="button" data-pill="notfound" onclick="setState('notfound')" class="text-xs px-2.5 py-1 rounded-md bg-[#0F1428] border border-white/10 text-gray-300">Tidak ditemukan</button>
            <button type="button" data-pill="error" onclick="setState('error')" class="text-xs px-2.5 py-1 rounded-md bg-[#0F1428] border border-white/10 text-gray-300">Error server</button>
        </div>
    </div>

    <main class="max-w-sm mx-auto lg:max-w-6xl px-4 pb-10 lg:grid lg:grid-cols-[240px_1fr_240px] lg:gap-6 lg:items-start">

        <aside class="hidden lg:block">
            <h2 class="font-bold mb-4">Kenapa bayar di sini</h2>
            <ul class="space-y-3 text-sm text-gray-300">
                <li class="flex gap-2"><span class="text-emerald-400">✓</span> Tanpa login, cukup nomor internet</li>
                <li class="flex gap-2"><span class="text-emerald-400">✓</span> QRIS, Virtual Account, e-wallet, minimarket</li>
                <li class="flex gap-2"><span class="text-emerald-400">✓</span> Internet aktif lagi setelah bayar</li>
            </ul>
        </aside>

        <div>
            <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 lg:p-8">
                <div class="flex flex-col items-center text-center mb-6">
                    <span class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-2xl mb-3">📶</span>
                    <p class="text-xs font-semibold text-gray-400 mb-1">RTRW Net Mekar Jaya</p>
                    <h1 class="text-2xl font-bold">Cek atau Bayar Tagihan Internet</h1>
                </div>

                {{-- NORMAL --}}
                <div data-state="normal">
                    <label for="nomor1" class="block text-sm font-medium mb-1.5">Nomor Internet</label>
                    <input id="nomor1" type="text" placeholder="Masukkan nomor internet Anda"
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="button" onclick="setState('loading')" class="mt-3 w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Cek Tagihan</button>
                    <div class="flex items-center gap-3 my-4 text-xs text-gray-500">
                        <span class="flex-1 h-px bg-white/10"></span>atau<span class="flex-1 h-px bg-white/10"></span>
                    </div>
                    <p class="text-center text-sm text-gray-400 mb-2">Belum berlangganan?</p>
                    <a href="#" class="block text-center w-full h-12 leading-[3rem] rounded-lg border border-white/15 hover:border-white/30 font-semibold">Daftar Pemasangan</a>
                </div>

                {{-- LOADING (skeleton) --}}
                <div data-state="loading" hidden>
                    <label class="block text-sm font-medium mb-1.5">Nomor Internet</label>
                    <input value="0812xxxxxxx" disabled
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 text-gray-400">
                    <button type="button" disabled class="mt-3 w-full h-12 rounded-lg bg-indigo-600/70 font-semibold flex items-center justify-center gap-2">
                        <span class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span> Memeriksa...
                    </button>
                    <div class="flex items-center gap-3 my-4 text-xs text-gray-500">
                        <span class="flex-1 h-px bg-white/10"></span>atau<span class="flex-1 h-px bg-white/10"></span>
                    </div>
                    <p class="text-center text-sm text-gray-400 mb-2">Belum berlangganan?</p>
                    <a href="#" class="block text-center w-full h-12 leading-[3rem] rounded-lg border border-white/15 font-semibold">Daftar Pemasangan</a>
                </div>

                {{-- NOT FOUND --}}
                <div data-state="notfound" hidden>
                    <label class="block text-sm font-medium mb-1.5">Nomor Internet</label>
                    <input value="0899999999"
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-red-500/60 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <p class="text-red-400 text-xs mt-1.5">Nomor tidak ditemukan. Periksa kembali atau hubungi admin.</p>
                    <a href="#" class="mt-3 flex items-center justify-center gap-2 w-full h-12 rounded-lg border border-white/15 hover:border-white/30 font-semibold">💬 Hubungi Admin via WhatsApp</a>
                    <button type="button" onclick="setState('normal')" class="mt-2 w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Cek Tagihan</button>
                    <div class="flex items-center gap-3 my-4 text-xs text-gray-500">
                        <span class="flex-1 h-px bg-white/10"></span>atau<span class="flex-1 h-px bg-white/10"></span>
                    </div>
                    <p class="text-center text-sm text-gray-400 mb-2">Belum berlangganan?</p>
                    <a href="#" class="block text-center w-full h-12 leading-[3rem] rounded-lg border border-white/15 hover:border-white/30 font-semibold">Daftar Pemasangan</a>
                </div>

                {{-- SERVER ERROR --}}
                <div data-state="error" hidden>
                    <label class="block text-sm font-medium mb-1.5">Nomor Internet</label>
                    <input value="081234567890"
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="mt-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 text-sm px-3 py-2.5 flex items-start gap-2">
                        <span>⚠️</span> Terjadi gangguan pada server. Coba lagi beberapa saat.
                    </div>
                    <button type="button" onclick="setState('normal')" class="mt-3 w-full h-12 rounded-lg border border-white/15 hover:border-white/30 font-semibold">Coba Lagi</button>
                    <div class="flex items-center gap-3 my-4 text-xs text-gray-500">
                        <span class="flex-1 h-px bg-white/10"></span>atau<span class="flex-1 h-px bg-white/10"></span>
                    </div>
                    <p class="text-center text-sm text-gray-400 mb-2">Belum berlangganan?</p>
                    <a href="#" class="block text-center w-full h-12 leading-[3rem] rounded-lg border border-white/15 hover:border-white/30 font-semibold">Daftar Pemasangan</a>
                </div>
            </div>

            <div data-state="normal" class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
                <h2 class="font-bold mb-1">Pemasangan terdaftar</h2>
                <p class="text-sm text-gray-400 mb-3">Satu nomor terhubung ke beberapa pemasangan. Pilih salah satu.</p>
                <div class="space-y-2">
                    <a href="#" class="flex items-center gap-3 p-3 rounded-xl bg-[#0F1428] border border-white/10 hover:border-white/20">
                        <span class="w-10 h-10 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center">🏠</span>
                        <span class="flex-1">
                            <span class="block font-semibold text-sm">Home 20 Mbps</span>
                            <span class="block text-xs text-gray-400">Budi S***** • Jl. Melati No. **</span>
                        </span>
                        <span class="text-gray-500">›</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 p-3 rounded-xl bg-[#0F1428] border border-white/10 hover:border-white/20">
                        <span class="w-10 h-10 rounded-lg bg-indigo-500/15 text-indigo-400 flex items-center justify-center">🏠</span>
                        <span class="flex-1">
                            <span class="block font-semibold text-sm">Home 10 Mbps</span>
                            <span class="block text-xs text-gray-400">Budi S***** • Ruko Mekar Blok **</span>
                        </span>
                        <span class="text-gray-500">›</span>
                    </a>
                </div>
            </div>

            <div data-state="loading" hidden class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5 animate-pulse">
                <div class="h-4 w-40 bg-white/10 rounded mb-3"></div>
                <div class="h-16 bg-white/5 rounded-xl mb-2"></div>
                <div class="h-16 bg-white/5 rounded-xl"></div>
            </div>

            <div class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
                <h2 class="font-bold mb-3">Download Aplikasi</h2>
                <div class="grid grid-cols-2 gap-2">
                    <a href="#" class="flex items-center justify-center gap-2 h-12 rounded-lg border border-white/15 text-sm font-semibold">📱 Play Store</a>
                    <a href="#" class="flex items-center justify-center gap-2 h-12 rounded-lg border border-white/15 text-sm font-semibold">📱 App Store</a>
                </div>
                <span class="inline-block mt-2 text-xs font-semibold px-2 py-0.5 rounded bg-amber-500/20 text-amber-400">segera hadir</span>
            </div>

            <div class="mt-4 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-sm px-4 py-3 flex items-start gap-2">
                <span>⚡</span> Promo bulan ini: bayar 3 bulan sekaligus, gratis biaya layanan. Layanan akan terisolir pada tanggal 15 jika belum dibayar.
            </div>

            <p class="mt-4 text-center text-xs text-gray-500">Kontak admin: WhatsApp 0812****345 • Senin–Sabtu 08.00–20.00</p>
            <p class="text-center text-xs text-gray-500">Jl. Mekar Raya No. **, Desa Mekar</p>
        </div>

        <aside class="hidden lg:block">
            <h2 class="font-bold mb-3">Info gangguan</h2>
            <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm px-4 py-3 flex items-center gap-2 mb-3">
                <span>✓</span> Semua layanan berjalan normal.
            </div>
            <a href="#" class="block text-center h-12 leading-[3rem] rounded-lg border border-white/15 hover:border-white/30 font-semibold text-sm">Pusat Bantuan</a>
        </aside>
    </main>

    <script>
        function setState(state) {
            document.querySelectorAll('[data-state]').forEach(function (el) {
                el.hidden = el.dataset.state !== state;
            });
            document.querySelectorAll('[data-pill]').forEach(function (btn) {
                var active = btn.dataset.pill === state;
                btn.classList.toggle('bg-indigo-600', active);
                btn.classList.toggle('text-white', active);
                btn.classList.toggle('bg-[#0F1428]', !active);
                btn.classList.toggle('border', !active);
                btn.classList.toggle('border-white/10', !active);
                btn.classList.toggle('text-gray-300', !active);
            });
        }
    </script>
</body>
</html>
