<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Tagihan — BILLINGIN</title>
    {{-- ponytail: static visual mockup (Bagian B2). No backend, nothing submits anywhere real. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white">

    <header class="max-w-sm mx-auto lg:max-w-2xl flex items-center justify-between py-4 px-4">
        <a href="#" class="flex items-center gap-1.5 text-indigo-400 font-semibold text-sm hover:text-indigo-300">← Kembali</a>
        <button type="button" class="w-10 h-10 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center" aria-label="Ganti tema">☀️</button>
    </header>

    {{-- Mockup-only aid: preview B2 states (lunas / terisolir) without a backend. --}}
    <div class="max-w-sm mx-auto lg:max-w-2xl px-4 mt-1 mb-4">
        <p class="text-[11px] uppercase tracking-wide text-gray-500 mb-1.5">Pratinjau status (alat bantu mockup)</p>
        <div class="flex flex-wrap gap-1.5">
            <button type="button" data-pill="normal" onclick="setState('normal')" class="text-xs px-2.5 py-1 rounded-md bg-indigo-600 text-white">Belum lunas</button>
            <button type="button" data-pill="lunas" onclick="setState('lunas')" class="text-xs px-2.5 py-1 rounded-md bg-[#0F1428] border border-white/10 text-gray-300">Lunas</button>
            <button type="button" data-pill="isolir" onclick="setState('isolir')" class="text-xs px-2.5 py-1 rounded-md bg-[#0F1428] border border-white/10 text-gray-300">Terisolir</button>
        </div>
    </div>

    <main class="max-w-sm mx-auto lg:max-w-2xl px-4 pb-10">
        <h1 class="text-2xl font-bold">Halo, BUDI S*****</h1>
        <p class="text-gray-400 text-sm mb-5">ID 12****67 • Paket Home 20 Mbps</p>

        {{-- BELUM LUNAS (default) --}}
        <div data-state="normal">
            <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Status</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-500/15 text-amber-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Belum lunas
                    </span>
                </div>
                <div class="flex items-center justify-between"><span class="text-sm text-gray-400">Periode</span><span class="font-semibold text-sm">Okt 2026</span></div>
                <div class="flex items-center justify-between"><span class="text-sm text-gray-400">Jatuh tempo</span><span class="font-semibold text-sm">10 Okt 2026</span></div>
                <div class="flex items-center justify-between"><span class="text-sm text-gray-400">Akan terisolir</span><span class="font-semibold text-sm">15 Okt 2026</span></div>
                <div class="rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-sm px-3 py-2.5 flex items-center gap-2">
                    <span>⏱</span> Tagihan Anda belum dibayar
                </div>
            </div>

            <div class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-2.5">
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Tagihan internet</span><span>Rp 150.000</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">PPN 11%</span><span>Rp 16.500</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Biaya tambahan</span><span>Rp 5.000</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Diskon</span><span class="text-emerald-400">−Rp 10.000</span></div>
                <div class="h-px bg-white/10 my-1"></div>
                <div class="flex items-center justify-between"><span class="text-xs font-bold tracking-wide text-gray-400">TOTAL</span><span class="text-2xl font-bold">Rp 161.500</span></div>
            </div>

            <div class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-sm">Bayar beberapa bulan?</p>
                    <p class="text-xs text-gray-400">Bayar di muka, tanpa repot tiap bulan</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="bump(-1)" class="w-9 h-9 rounded-lg border border-white/15 flex items-center justify-center hover:border-white/30">−</button>
                    <span id="bulan-qty" class="w-5 text-center font-semibold">1</span>
                    <button type="button" onclick="bump(1)" class="w-9 h-9 rounded-lg border border-white/15 flex items-center justify-center hover:border-white/30">+</button>
                </div>
            </div>

            <details class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
                <summary class="font-bold cursor-pointer list-none flex items-center justify-between">Riwayat pembayaran <span>⌄</span></summary>
                <div class="mt-3 space-y-2 text-sm text-gray-400">
                    <div class="flex justify-between"><span>Sep 2026 • QRIS</span><span class="text-gray-300">Rp 161.500</span></div>
                    <div class="flex justify-between"><span>Agu 2026 • Virtual Account</span><span class="text-gray-300">Rp 161.500</span></div>
                </div>
            </details>

            <a href="#" class="mt-5 block text-center w-full h-12 leading-[3rem] rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Bayar Sekarang</a>
        </div>

        {{-- LUNAS / KOSONG --}}
        <div data-state="lunas" hidden>
            <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Status</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Lunas
                    </span>
                </div>
                <div class="rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm px-3 py-2.5 flex items-center gap-2">
                    <span>✓</span> Tidak ada tagihan. Terima kasih!
                </div>
                <div class="flex items-center justify-between"><span class="text-sm text-gray-400">Aktif sampai</span><span class="font-semibold text-sm">10 Nov 2026</span></div>
            </div>

            <details class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5">
                <summary class="font-bold cursor-pointer list-none flex items-center justify-between">Riwayat pembayaran <span>⌄</span></summary>
                <div class="mt-3 space-y-2 text-sm text-gray-400">
                    <div class="flex justify-between"><span>Okt 2026 • QRIS</span><span class="text-gray-300">Rp 161.500</span></div>
                </div>
            </details>

            <a href="#" class="mt-5 flex items-center justify-center gap-2 w-full h-12 rounded-lg border border-white/15 hover:border-white/30 font-semibold">⬇ Lihat Struk Terakhir</a>
        </div>

        {{-- TERISOLIR --}}
        <div data-state="isolir" hidden>
            <div class="rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Status</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-500/15 text-red-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Terisolir
                    </span>
                </div>
                <div class="flex items-center justify-between"><span class="text-sm text-gray-400">Periode tertunggak</span><span class="font-semibold text-sm">Okt 2026</span></div>
                <div class="flex items-center justify-between"><span class="text-sm text-gray-400">Jatuh tempo</span><span class="font-semibold text-sm">10 Okt 2026</span></div>
                <div class="rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 text-sm px-3 py-2.5 flex items-start gap-2">
                    <span>🚫</span> Layanan internet Anda sementara dinonaktifkan karena tagihan belum dibayar.
                </div>
            </div>

            <div class="mt-4 rounded-2xl bg-[#0B0F24] border border-white/10 p-5 space-y-2.5">
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Tagihan internet</span><span>Rp 150.000</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">PPN 11%</span><span>Rp 16.500</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Biaya tambahan</span><span>Rp 5.000</span></div>
                <div class="flex items-center justify-between text-sm"><span class="text-gray-400">Diskon</span><span class="text-emerald-400">−Rp 10.000</span></div>
                <div class="h-px bg-white/10 my-1"></div>
                <div class="flex items-center justify-between"><span class="text-xs font-bold tracking-wide text-gray-400">TOTAL</span><span class="text-2xl font-bold">Rp 161.500</span></div>
            </div>

            <a href="#" class="mt-5 block text-center w-full h-12 leading-[3rem] rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Bayar Sekarang</a>
        </div>
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
        function bump(delta) {
            var el = document.getElementById('bulan-qty');
            var val = Math.max(1, parseInt(el.textContent, 10) + delta);
            el.textContent = val;
        }
    </script>
</body>
</html>
