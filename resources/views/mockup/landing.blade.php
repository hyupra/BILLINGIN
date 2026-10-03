<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BILLINGIN — Billing &amp; Pembayaran WiFi RTRW Net</title>
    {{-- ponytail: Tailwind via CDN, no build step yet. Swap for the real
         Vite+Tailwind pipeline (with the actual design tokens) in the
         Frontend sprint. This is a visual mockup only — no backend wiring. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        html { scroll-behavior: smooth; }

        /* ponytail: pragmatic html.light override layer, not a full semantic
           dark:/light rework — flips the handful of structural colors used
           across the page. Good enough for a functional toggle, not pixel-perfect. */
        html.light body { background: #FAFAFA; color: #0F172A; }
        html.light .bg-\[\#070A16\] { background-color: #FAFAFA !important; }
        html.light .bg-\[\#070A16\]\/90 { background-color: rgb(250 250 250 / 0.9) !important; }
        html.light .bg-\[\#0B0F24\] { background-color: #FFFFFF !important; }
        html.light .bg-\[\#0F1428\] { background-color: #F1F5F9 !important; }
        html.light .bg-gradient-to-br.from-\[\#0B0F24\].to-\[\#141A3A\] { background-image: linear-gradient(to bottom right, #FFFFFF, #EEF2FF) !important; }
        html.light .bg-white.text-\[\#0B0F24\] { background-color: #4F46E5 !important; color: #FFFFFF !important; }
        html.light .text-white { color: #0F172A !important; }
        html.light .text-gray-300 { color: #334155 !important; }
        html.light .text-gray-400 { color: #475569 !important; }
        html.light .text-gray-500 { color: #64748B !important; }
        html.light .text-indigo-400 { color: #4F46E5 !important; }
        html.light .border-white\/10 { border-color: #E2E8F0 !important; }
        html.light .border-white\/20 { border-color: #CBD5E1 !important; }
        html.light .divide-white\/10 > :not([hidden]) ~ :not([hidden]) { border-color: #E2E8F0 !important; }
        html.light .hover\:bg-white\/5:hover { background-color: #F1F5F9 !important; }
    </style>
</head>
<body class="bg-[#070A16] text-white antialiased">

    {{-- ========== NAVBAR ========== --}}
    <header class="sticky top-0 z-50 bg-[#070A16]/90 backdrop-blur border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/mockup/landing" class="flex items-center gap-2 shrink-0">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center"><i class="fa-solid fa-wifi"></i></span>
                <span class="font-bold text-lg">BILLING<span class="text-indigo-400">IN</span></span>
            </a>

            <nav class="hidden lg:flex items-center gap-8 text-sm text-gray-300">
                <a href="#beranda" class="hover:text-white">Beranda</a>
                <a href="#fitur" class="hover:text-white">Fitur</a>
                <a href="#web-pembayaran" class="hover:text-white">Web Pembayaran</a>
                <a href="#voucher" class="hover:text-white">Voucher</a>
                <a href="#harga" class="hover:text-white">Harga</a>
                <a href="#faq" class="hover:text-white">FAQ</a>
            </nav>

            <div class="hidden lg:flex items-center gap-3">
                <button type="button" id="theme-toggle" aria-label="Ganti mode tampilan" class="w-10 h-10 rounded-lg border border-white/10 bg-[#0F1428] flex items-center justify-center text-gray-300 hover:text-white"><i id="theme-icon" class="fa-solid fa-sun"></i></button>
                <a href="/login" class="h-11 px-4 inline-flex items-center rounded-lg border border-white/20 text-sm font-semibold hover:bg-white/5">Masuk</a>
                <a href="/mockup/register" class="h-11 px-5 inline-flex items-center rounded-lg bg-indigo-600 hover:bg-indigo-500 text-sm font-semibold transition">Coba Gratis</a>
            </div>

            <button type="button" id="mobile-menu-btn" aria-label="Buka menu" aria-expanded="false" class="lg:hidden w-11 h-11 rounded-lg border border-white/10 flex items-center justify-center">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>

        <nav id="mobile-menu" class="hidden lg:hidden border-t border-white/10 px-6 py-4 space-y-3 text-sm text-gray-300">
            <a href="#beranda" class="block py-1">Beranda</a>
            <a href="#fitur" class="block py-1">Fitur</a>
            <a href="#web-pembayaran" class="block py-1">Web Pembayaran</a>
            <a href="#voucher" class="block py-1">Voucher</a>
            <a href="#harga" class="block py-1">Harga</a>
            <a href="#faq" class="block py-1">FAQ</a>
            <div class="flex gap-3 pt-2">
                <a href="/login" class="flex-1 h-12 inline-flex items-center justify-center rounded-lg border border-white/20 font-semibold">Masuk</a>
                <a href="/mockup/register" class="flex-1 h-12 inline-flex items-center justify-center rounded-lg bg-indigo-600 font-semibold">Coba Gratis</a>
            </div>
        </nav>
    </header>

    {{-- ========== HERO ========== --}}
    <section id="beranda" class="relative overflow-hidden bg-gradient-to-br from-[#0B0F24] to-[#141A3A]">
        <div class="absolute -top-24 right-0 w-[500px] h-[500px] rounded-full bg-indigo-600/20 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-6xl mx-auto px-6 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-xs font-bold tracking-widest text-indigo-400 mb-4">BILLING &amp; PEMBAYARAN WIFI RTRW NET</p>
                <h1 class="text-4xl lg:text-5xl font-bold leading-tight mb-5">Tagihan WiFi Lunas Otomatis, Tanpa Kejar-kejar Pelanggan</h1>
                <p class="text-gray-400 text-lg mb-8 max-w-lg">Terima QRIS, Virtual Account, dan minimarket dalam satu halaman bayar. Pelanggan terisolir otomatis dibuka begitu pembayaran masuk.</p>
                <div class="flex flex-col sm:flex-row gap-3 mb-5">
                    <a href="/mockup/register" class="h-12 px-6 inline-flex items-center justify-center rounded-lg bg-white text-[#0B0F24] font-semibold hover:bg-gray-100">Coba Gratis 3 Hari</a>
                    <a href="#web-pembayaran" class="h-12 px-6 inline-flex items-center justify-center rounded-lg border border-white/20 font-semibold hover:bg-white/5">Lihat Demo Bayar</a>
                </div>
                <p class="text-sm text-gray-500">Tanpa biaya setup &middot; Tanpa kartu kredit &middot; Batalkan kapan saja</p>
            </div>

            <div class="w-full max-w-sm mx-auto lg:ml-auto lg:mr-0 rounded-2xl border border-white/10 bg-[#0B0F24] p-5">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-semibold">Cek Tagihan</span>
                    <span class="text-[10px] font-bold uppercase tracking-wide bg-amber-400/90 text-[#0B0F24] rounded-full px-2 py-1">contoh</span>
                </div>
                <div class="rounded-xl bg-[#0F1428] border border-white/10 p-4 mb-4">
                    <p class="font-semibold">Halo, Budi S*****</p>
                    <p class="text-sm text-gray-500 mb-2">Home 20 Mbps &middot; Okt 2026</p>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-400 bg-amber-400/10 rounded-full px-2.5 py-1 mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Belum dibayar
                    </span>
                    <div class="flex justify-between text-sm text-gray-400 mb-1">
                        <span>Tagihan</span><span>Rp 150.000</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-400 border-b border-white/10 pb-2 mb-2">
                        <span>Biaya layanan</span><span>Rp 2.500</span>
                    </div>
                    <div class="flex justify-between font-semibold">
                        <span>Total</span><span>Rp 152.500</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <label class="flex items-center gap-2 h-11 px-3 rounded-lg border border-indigo-500 bg-indigo-500/10 text-sm font-medium">
                        <input type="radio" name="hero-pay-method" checked class="accent-indigo-500"> QRIS
                    </label>
                    <label class="flex items-center gap-2 h-11 px-3 rounded-lg border border-white/10 bg-[#0F1428] text-sm font-medium">
                        <input type="radio" name="hero-pay-method" class="accent-indigo-500"> VA
                    </label>
                    <label class="flex items-center gap-2 h-11 px-3 rounded-lg border border-white/10 bg-[#0F1428] text-sm font-medium">
                        <input type="radio" name="hero-pay-method" class="accent-indigo-500"> E-Wallet
                    </label>
                    <label class="flex items-center gap-2 h-11 px-3 rounded-lg border border-white/10 bg-[#0F1428] text-sm font-medium">
                        <input type="radio" name="hero-pay-method" class="accent-indigo-500"> Minimarket
                    </label>
                </div>
                <a href="/mockup/pay/check-bill" class="w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition flex items-center justify-center">Bayar Sekarang</a>
            </div>
        </div>
    </section>

    {{-- ========== BUKTI SINGKAT ========== --}}
    <section class="bg-[#070A16] border-b border-white/5">
        <div class="max-w-6xl mx-auto px-6 py-10">
            <p class="mb-6">
                <span class="text-[10px] font-bold uppercase tracking-wide bg-amber-400/90 text-[#0B0F24] rounded-full px-2 py-1 mr-2">contoh</span>
                <span class="text-sm text-gray-500">Angka ilustrasi. Akan diganti data nyata setelah ada mitra.</span>
            </p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <p class="text-3xl font-bold">1.250</p>
                    <p class="text-sm text-gray-500">pelanggan dikelola</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">Rp 480 jt</p>
                    <p class="text-sm text-gray-500">total transaksi</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">99,5%</p>
                    <p class="text-sm text-gray-500">uptime halaman bayar</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">&lt; 5 detik</p>
                    <p class="text-sm text-gray-500">rata-rata konfirmasi bayar</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== KENAPA MEMILIH KAMI ========== --}}
    <section class="bg-[#0B0F24]">
        <div class="max-w-6xl mx-auto px-6 py-16">
            <p class="text-xs font-bold tracking-widest text-indigo-400 mb-3">KENAPA BILLINGIN</p>
            <h2 class="text-3xl lg:text-4xl font-bold mb-10 max-w-xl">Bayar, internet aktif lagi. Tanpa admin.</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ([
                    ['fa-solid fa-tag', 'Harga transparan per pelanggan', 'Bayar sesuai jumlah pelanggan aktif. Simulasikan sendiri di kalkulator.'],
                    ['fa-solid fa-circle-check', 'Pembayaran otomatis terkonfirmasi', 'Status lunas berubah sendiri begitu pembayaran masuk.'],
                    ['fa-solid fa-ban', 'Auto isolir dan buka isolir', 'Telat bayar terisolir, sudah bayar aktif lagi, tanpa sentuhan admin.'],
                    ['fa-brands fa-whatsapp', 'Support responsif lewat WhatsApp', 'Tim kami siap membantu onboarding dan kendala harian.'],
                ] as [$icon, $title, $desc])
                    <div class="rounded-xl border border-white/10 bg-[#0F1428] p-5">
                        <div class="w-11 h-11 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-lg mb-4"><i class="{{ $icon }}"></i></div>
                        <h3 class="font-semibold mb-1.5">{{ $title }}</h3>
                        <p class="text-sm text-gray-400">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== FITUR ========== --}}
    <section id="fitur" class="bg-[#070A16]">
        <div class="max-w-6xl mx-auto px-6 py-16">
            <p class="text-xs font-bold tracking-widest text-indigo-400 mb-3">FITUR</p>
            <h2 class="text-3xl lg:text-4xl font-bold mb-2 max-w-xl">Semua yang dibutuhkan untuk menagih WiFi</h2>
            <p class="text-gray-400 mb-10">Mulai dari enam fitur inti. Sisanya menyusul bertahap.</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ([
                    ['fa-solid fa-file-invoice-dollar', 'Billing & tagihan otomatis', 'Tagihan berulang terbit sendiri tiap periode.', false],
                    ['fa-solid fa-qrcode', 'Payment gateway', 'QRIS, Virtual Account, dan gerai dalam satu halaman.', false],
                    ['fa-solid fa-ban', 'Auto isolir', 'Isolir dan buka isolir langsung di router MikroTik.', false],
                    ['fa-brands fa-whatsapp', 'Notifikasi WhatsApp', 'Tagihan, pengingat, dan bukti bayar terkirim otomatis.', false],
                    ['fa-solid fa-ticket', 'Voucher hotspot', 'Jual voucher online, kredensial dikirim ke pembeli.', false],
                    ['fa-solid fa-headset', 'Tiket komplain', 'Pelanggan lapor gangguan, Anda pantau progresnya.', false],
                    ['fa-solid fa-network-wired', 'Monitoring router', 'Pantau status online dan offline pelanggan.', true],
                    ['fa-solid fa-chart-column', 'Laporan keuangan', 'Pendapatan dan tunggakan dalam satu laporan.', true],
                    ['fa-solid fa-wallet', 'Pengeluaran', 'Catat biaya operasional di luar tagihan.', true],
                    ['fa-solid fa-bullhorn', 'Broadcast pesan', 'Kirim info gangguan ke banyak pelanggan sekaligus.', true],
                    ['fa-solid fa-server', 'Multi router', 'Kelola banyak router dari satu akun.', true],
                    ['fa-solid fa-location-dot', 'Peta ODP', 'Lihat sebaran ODP dan sisa slot di peta.', true],
                ] as [$icon, $title, $desc, $soon])
                    <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-11 h-11 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-lg"><i class="{{ $icon }}"></i></div>
                            @if ($soon)
                                <span class="text-[11px] font-medium text-gray-400 inline-flex items-center gap-1 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Segera hadir
                                </span>
                            @endif
                        </div>
                        <h3 class="font-semibold mb-1.5">{{ $title }}</h3>
                        <p class="text-sm text-gray-400">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== CARA KERJA ========== --}}
    <section class="bg-[#0B0F24]">
        <div class="max-w-6xl mx-auto px-6 py-16">
            <p class="text-xs font-bold tracking-widest text-indigo-400 mb-3">CARA KERJA</p>
            <h2 class="text-3xl lg:text-4xl font-bold mb-10">Tiga langkah, semuanya otomatis</h2>
            <div class="grid sm:grid-cols-3 gap-5">
                @foreach ([
                    ['Pelanggan menerima tagihan', 'Tagihan dan tautan bayar terkirim lewat WhatsApp pada tanggal terjadwal.'],
                    ['Pelanggan bayar di halaman bayar', 'Cukup masukkan nomor internet, pilih QRIS, VA, atau minimarket. Tanpa login.'],
                    ['Sistem konfirmasi dan buka isolir', 'Pembayaran tervalidasi, isolir dibuka di router, bukti bayar dikirim.'],
                ] as $i => [$title, $desc])
                    <div class="rounded-xl border border-white/10 bg-[#0F1428] p-5">
                        <span class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-sm font-bold mb-4">{{ $i + 1 }}</span>
                        <h3 class="font-semibold mb-1.5">{{ $title }}</h3>
                        <p class="text-sm text-gray-400">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== SHOWCASE WEB PEMBAYARAN ========== --}}
    <section id="web-pembayaran" class="bg-[#070A16]">
        <div class="max-w-6xl mx-auto px-6 py-16 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-xs font-bold tracking-widest text-indigo-400 mb-3">WEB PEMBAYARAN</p>
                <h2 class="text-3xl lg:text-4xl font-bold mb-6">Halaman bayar yang selesai dalam hitungan detik</h2>
                <ul class="space-y-3 mb-8 text-gray-300">
                    @foreach ([
                        'Banyak metode bayar: QRIS, Virtual Account, e-wallet, minimarket',
                        'Tanpa login, cukup nomor internet pelanggan',
                        'Bukti bayar instan, bisa dibagikan ke WhatsApp',
                        'Data pelanggan selalu disamarkan di halaman publik',
                    ] as $point)
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 mt-0.5"><i class="fa-solid fa-circle-check"></i></span> <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="/mockup/pay/check-bill" class="h-12 px-6 inline-flex items-center justify-center rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Lihat Demo Pembayaran</a>
            </div>

            <div class="rounded-2xl border border-white/10 bg-[#0B0F24] overflow-hidden">
                <div class="flex items-center gap-1.5 px-4 py-3 border-b border-white/10 bg-[#0F1428]">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="ml-3 text-xs text-gray-500">bayar.nama-isp.id</span>
                </div>
                <div class="p-5 grid grid-cols-[1fr_auto] gap-4 items-start">
                    <div>
                        <p class="font-semibold mb-3">Cek atau Bayar Tagihan Internet</p>
                        <input type="text" value="12****67" readonly class="w-full h-11 px-3 rounded-lg bg-[#0F1428] border border-white/10 text-sm text-gray-300 mb-3">
                        <a href="/mockup/pay/check-bill" class="w-full h-11 rounded-lg bg-indigo-600 text-sm font-semibold mb-3 flex items-center justify-center">Cek Tagihan</a>
                        <p class="text-sm text-emerald-400 flex items-center gap-1.5"><span><i class="fa-solid fa-circle-check"></i></span> Pembayaran berhasil. Terima kasih!</p>
                    </div>
                    <div class="text-center">
                        <span class="text-[10px] font-bold uppercase tracking-wide bg-amber-400/90 text-[#0B0F24] rounded-full px-2 py-1 mb-2 inline-block">QRIS</span>
                        <div class="w-20 h-20 bg-[repeating-conic-gradient(#fff_0_25%,#0B0F24_0_50%)] bg-[length:10px_10px] rounded-lg border border-white/10"></div>
                        <p class="text-[11px] text-gray-500 mt-1">Sisa 14:52</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== VOUCHER ========== --}}
    <section id="voucher" class="bg-[#0B0F24]">
        <div class="max-w-6xl mx-auto px-6 py-16">
            <p class="text-xs font-bold tracking-widest text-indigo-400 mb-3">VOUCHER HOTSPOT</p>
            <h2 class="text-3xl lg:text-4xl font-bold mb-3 max-w-xl">Jual voucher dari HP, kredensial langsung terkirim</h2>
            <p class="text-gray-400 mb-10 max-w-xl">Fase 2. Pembeli pilih paket, sistem membuat akun di router, kredensial dikirim ke WhatsApp.</p>

            <div class="grid sm:grid-cols-3 gap-5 mb-10">
                @foreach ([
                    ['Pembeli pilih paket', 'Pilih durasi, isi nomor WhatsApp, lalu bayar.'],
                    ['Sistem membuat user', 'Username dan password dibuat otomatis di MikroTik.'],
                    ['Dikirim ke WhatsApp', 'Kredensial tiba dalam hitungan detik setelah bayar.'],
                ] as $i => [$title, $desc])
                    <div class="rounded-xl border border-white/10 bg-[#0F1428] p-5">
                        <span class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-sm font-bold mb-4">{{ $i + 1 }}</span>
                        <h3 class="font-semibold mb-1.5">{{ $title }}</h3>
                        <p class="text-sm text-gray-400">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            <p class="mb-5">
                <span class="text-[10px] font-bold uppercase tracking-wide bg-amber-400/90 text-[#0B0F24] rounded-full px-2 py-1 mr-2">contoh</span>
                <span class="text-sm text-gray-500">Paket dan harga ilustrasi</span>
            </p>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                @foreach ([
                    ['3 Jam', '3.000'],
                    ['1 Hari', '5.000'],
                    ['3 Hari', '12.000'],
                    ['7 Hari', '25.000'],
                ] as [$label, $price])
                    <div class="rounded-xl border border-white/10 bg-[#0F1428] p-5">
                        <p class="text-sm text-gray-400 mb-2">{{ $label }}</p>
                        <p class="text-2xl font-bold mb-4">Rp {{ $price }}</p>
                        <a href="/mockup/pay/payment-method" class="w-full h-11 rounded-lg border border-white/20 font-semibold hover:bg-white/5 flex items-center justify-center">Beli</a>
                    </div>
                @endforeach
            </div>
            <div class="inline-flex items-center gap-3">
                <a href="#" title="Demo toko segera hadir" class="h-12 px-6 inline-flex items-center justify-center rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition opacity-60 cursor-not-allowed">Lihat Demo Toko</a>
                <span class="text-[11px] font-medium text-gray-400 inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Segera hadir
                </span>
            </div>
        </div>
    </section>

    {{-- ========== APLIKASI PELANGGAN ========== --}}
    <section class="bg-[#070A16]">
        <div class="max-w-6xl mx-auto px-6 py-16 grid lg:grid-cols-2 gap-12 items-center">
            <div class="w-full max-w-xs mx-auto lg:mx-0 rounded-2xl border border-white/10 bg-[#0B0F24] p-4">
                <div class="rounded-xl bg-[#0F1428] border border-white/10 p-4 mb-3">
                    <p class="font-semibold text-sm">Halo, Budi S*****</p>
                    <p class="text-xs text-gray-500 mb-2">Tagihan Okt 2026</p>
                    <p class="text-xl font-bold mb-1">Rp 152.500</p>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-amber-400 bg-amber-400/10 rounded-full px-2 py-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Belum dibayar
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="h-11 px-3 rounded-lg bg-[#0F1428] border border-white/10 flex items-center text-sm font-medium">Riwayat pembayaran</div>
                    <div class="h-11 px-3 rounded-lg bg-[#0F1428] border border-white/10 flex items-center text-sm font-medium">Upgrade paket</div>
                    <div class="h-11 px-3 rounded-lg bg-[#0F1428] border border-white/10 flex items-center text-sm font-medium">Info gangguan</div>
                </div>
            </div>

            <div>
                <p class="text-xs font-bold tracking-widest text-indigo-400 mb-3">APLIKASI PELANGGAN <span class="text-gray-500">&middot; Fase 2</span></p>
                <h2 class="text-3xl lg:text-4xl font-bold mb-6 max-w-lg">Pelanggan bayar dan lapor gangguan dari satu aplikasi</h2>
                <ul class="space-y-3 text-gray-300">
                    @foreach ([
                        'Bayar tagihan dan lihat riwayat',
                        'Upgrade paket mandiri',
                        'Buat tiket komplain dan pantau progresnya',
                        'Info gangguan jaringan',
                    ] as $point)
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-400 mt-0.5"><i class="fa-solid fa-circle-check"></i></span> <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ========== HARGA + KALKULATOR ========== --}}
    <section id="harga" class="bg-[#0B0F24]">
        <div class="max-w-6xl mx-auto px-6 py-16">
            <p class="text-xs font-bold tracking-widest text-indigo-400 mb-3">HARGA</p>
            <h2 class="text-3xl lg:text-4xl font-bold mb-2">Bayar sesuai jumlah pelanggan aktif</h2>
            <p class="text-gray-400 mb-10">Geser untuk menghitung biaya platform per bulan.</p>

            <div class="grid lg:grid-cols-2 gap-5">
                <div class="rounded-xl border border-white/10 bg-[#0F1428] p-6">
                    <p class="mb-6">
                        <span class="text-[10px] font-bold uppercase tracking-wide bg-amber-400/90 text-[#0B0F24] rounded-full px-2 py-1 mr-2">contoh tarif</span>
                        <span class="text-sm text-gray-500">Tarif akhir masih divalidasi</span>
                    </p>

                    <div class="mb-6">
                        <div class="flex justify-between text-sm font-medium mb-2">
                            <span>Jumlah pelanggan aktif</span>
                            <span id="pelanggan-value">100</span>
                        </div>
                        <input type="range" id="pelanggan-slider" min="10" max="2000" step="10" value="100" class="w-full accent-indigo-500">
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>10</span><span>2.000</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex justify-between text-sm font-medium mb-2">
                            <span>Jumlah router</span>
                            <span id="router-value">1</span>
                        </div>
                        <input type="range" id="router-slider" min="1" max="20" step="1" value="1" class="w-full accent-indigo-500">
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>1</span><span>20</span>
                        </div>
                    </div>

                    <ul class="space-y-2 text-sm text-gray-300">
                        <li class="flex items-center gap-2"><span class="text-emerald-400"><i class="fa-solid fa-circle-check"></i></span> Tanpa biaya setup</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-400"><i class="fa-solid fa-circle-check"></i></span> Gratis trial 3 hari</li>
                        <li class="flex items-center gap-2"><span class="text-emerald-400"><i class="fa-solid fa-circle-check"></i></span> Bayar per 3 bulan</li>
                    </ul>
                </div>

                <div class="rounded-xl border border-white/10 bg-[#0F1428] p-6 flex flex-col">
                    <p class="font-semibold mb-4">Perkiraan biaya</p>
                    <div class="flex justify-between text-sm text-gray-400 mb-2">
                        <span id="pelanggan-line">100 pelanggan &times; Rp 350</span>
                        <span id="pelanggan-subtotal">Rp 35.000</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-400 border-b border-white/10 pb-4 mb-4">
                        <span id="router-line">1 router/VPN &times; Rp 5.000</span>
                        <span id="router-subtotal">Rp 5.000</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Total per bulan</p>
                    <p class="text-4xl font-bold mb-4" id="total-bulan">Rp 40.000</p>
                    <div class="flex justify-between text-sm text-gray-400 mb-6">
                        <span>Total per 3 bulan</span>
                        <span class="font-semibold text-white" id="total-3bulan">Rp 120.000</span>
                    </div>
                    <a href="/mockup/register" class="mt-auto w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition flex items-center justify-center">Coba Gratis 3 Hari</a>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== TESTIMONI ========== --}}
    <section class="bg-[#070A16]">
        <div class="max-w-6xl mx-auto px-6 py-16">
            <h2 class="text-3xl lg:text-4xl font-bold mb-10">
                Kata mitra
                <span class="align-middle ml-2 text-[10px] font-bold uppercase tracking-wide bg-amber-400/90 text-[#0B0F24] rounded-full px-2 py-1">contoh</span>
            </h2>
            <div class="grid sm:grid-cols-3 gap-5">
                @foreach ([
                    ['Tunggakan turun 40%', 'Dulu tiap tanggal muda saya chat satu-satu. Sekarang pelanggan bayar sendiri, saya tinggal cek laporan.', 'A', 'Pak Andi', 'RTRW Net, 150 pelanggan'],
                    ['Isolir otomatis', 'Pelanggan yang telat langsung terisolir, dan aktif lagi begitu bayar. Admin tidak perlu begadang.', 'R', 'Mas Rizal', 'ISP lokal, 2 router'],
                    ['Bayar di minimarket', 'Pelanggan yang tidak punya mobile banking sekarang bisa bayar di minimarket dengan kode sederhana.', 'S', 'Bu Sari', 'Pelanggan WiFi'],
                ] as [$badge, $quote, $initial, $name, $role])
                    <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-5">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-400 bg-emerald-400/10 rounded-full px-2.5 py-1 mb-4">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> {{ $badge }}
                        </span>
                        <p class="text-gray-300 mb-5">&ldquo;{{ $quote }}&rdquo;</p>
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-sm font-bold">{{ $initial }}</span>
                            <div>
                                <p class="text-sm font-semibold">{{ $name }}</p>
                                <p class="text-xs text-gray-500">{{ $role }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== FAQ ========== --}}
    <section id="faq" class="bg-[#070A16]">
        <div class="max-w-3xl mx-auto px-6 pb-16">
            <p class="text-xs font-bold tracking-widest text-indigo-400 mb-3">FAQ</p>
            <h2 class="text-3xl lg:text-4xl font-bold mb-8">Pertanyaan yang sering muncul</h2>

            <div class="rounded-xl border border-white/10 bg-[#0B0F24] divide-y divide-white/10">
                @foreach ([
                    ['Metode bayar apa saja yang tersedia?', 'QRIS, Virtual Account bank nasional, e-wallet, dan gerai minimarket. Semua tersedia dalam satu halaman bayar tanpa perlu login.'],
                    ['Berapa biaya per transaksi?', 'Biaya mengikuti tarif masing-masing penyedia payment gateway. BILLINGIN tidak menambah biaya tersembunyi di atas itu.'],
                    ['Kapan dana masuk ke rekening saya?', 'Dana diteruskan sesuai jadwal pencairan payment gateway yang dipakai, umumnya 1 hari kerja setelah transaksi berhasil.'],
                    ['Bagaimana jika pelanggan salah bayar?', 'Hubungi tim support lewat WhatsApp. Kami bantu telusuri status transaksi dan proses penyesuaiannya.'],
                    ['Apakah perlu server sendiri?', 'Tidak. BILLINGIN berjalan di cloud kami, Anda hanya perlu menghubungkan router MikroTik untuk auto isolir.'],
                    ['Apakah data pelanggan aman?', 'Data pelanggan dienkripsi dan selalu ditampilkan tersamar di halaman publik. Hanya Anda yang bisa melihat data lengkap di dashboard.'],
                ] as [$q, $a])
                    <details class="group p-5">
                        <summary class="flex items-center justify-between cursor-pointer list-none font-semibold">
                            {{ $q }}
                            <span class="text-gray-500 transition group-open:rotate-180"><i class="fa-solid fa-chevron-down"></i></span>
                        </summary>
                        <p class="text-sm text-gray-400 mt-3">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== CTA PENUTUP ========== --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-[#0B0F24] to-[#141A3A]">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] rounded-full bg-indigo-600/20 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-3xl mx-auto px-6 py-20 text-center">
            <h2 class="text-3xl lg:text-5xl font-bold mb-4">Siap Berhenti Menagih Manual?</h2>
            <p class="text-gray-400 mb-8">Daftar dalam beberapa menit. Tanpa biaya setup dan tanpa kartu kredit.</p>
            <a href="/mockup/register" class="h-12 px-6 inline-flex items-center justify-center rounded-lg bg-white text-[#0B0F24] font-semibold hover:bg-gray-100">Buat Akun Gratis</a>
        </div>
    </section>

    {{-- ========== FOOTER ========== --}}
    <footer class="bg-[#0B0F24] border-t border-white/10">
        <div class="max-w-6xl mx-auto px-6 py-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <a href="/mockup/landing" class="flex items-center gap-2 mb-3">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center"><i class="fa-solid fa-wifi"></i></span>
                    <span class="font-bold text-lg">BILLING<span class="text-indigo-400">IN</span></span>
                </a>
                <p class="text-sm text-gray-500">Billing dan pembayaran WiFi untuk RTRW Net dan ISP lokal.</p>
            </div>
            <div>
                <p class="font-semibold mb-3">Produk</p>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#fitur" class="hover:text-white">Fitur</a></li>
                    <li><a href="#harga" class="hover:text-white">Harga</a></li>
                    <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-3">Kontak</p>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="https://wa.me/6281234567890" class="hover:text-white">WhatsApp: 0812-3456-7890 (contoh)</a></li>
                    <li><a href="mailto:halo@billingin.id" class="hover:text-white">Email: halo@billingin.id (contoh)</a></li>
                </ul>
            </div>
            <div>
                <p class="font-semibold mb-3">Legal</p>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-white">Syarat &amp; Ketentuan</a></li>
                    <li><a href="#" class="hover:text-white">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <p class="max-w-6xl mx-auto px-6 py-5 text-sm text-gray-500">&copy; {{ date('Y') }} BILLINGIN. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script>
        // Mobile nav toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => {
            const isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            menuBtn.setAttribute('aria-expanded', String(!isOpen));
        });

        // Price calculator — contoh tarif: Rp 350/pelanggan + Rp 5.000/router, per bulan
        const RATE_PER_CUSTOMER = 350;
        const RATE_PER_ROUTER = 5000;
        const idr = (n) => 'Rp ' + n.toLocaleString('id-ID');

        const pelangganSlider = document.getElementById('pelanggan-slider');
        const routerSlider = document.getElementById('router-slider');

        function recalc() {
            const pelanggan = parseInt(pelangganSlider.value, 10);
            const router = parseInt(routerSlider.value, 10);
            const subPelanggan = pelanggan * RATE_PER_CUSTOMER;
            const subRouter = router * RATE_PER_ROUTER;
            const total = subPelanggan + subRouter;

            document.getElementById('pelanggan-value').textContent = pelanggan.toLocaleString('id-ID');
            document.getElementById('router-value').textContent = router;
            document.getElementById('pelanggan-line').textContent = `${pelanggan.toLocaleString('id-ID')} pelanggan × Rp 350`;
            document.getElementById('router-line').textContent = `${router} router/VPN × Rp 5.000`;
            document.getElementById('pelanggan-subtotal').textContent = idr(subPelanggan);
            document.getElementById('router-subtotal').textContent = idr(subRouter);
            document.getElementById('total-bulan').textContent = idr(total);
            document.getElementById('total-3bulan').textContent = idr(total * 3);
        }

        pelangganSlider.addEventListener('input', recalc);
        routerSlider.addEventListener('input', recalc);

        // Theme toggle — defaults to dark, persists choice in localStorage
        (function () {
            const root = document.documentElement;
            const btn = document.getElementById('theme-toggle');
            const icon = document.getElementById('theme-icon');

            function applyIcon() {
                const isLight = root.classList.contains('light');
                icon.classList.toggle('fa-sun', !isLight);
                icon.classList.toggle('fa-moon', isLight);
            }

            let stored = null;
            try { stored = localStorage.getItem('billingin-theme'); } catch (e) {}
            if (stored === 'light') root.classList.add('light');
            applyIcon();

            btn.addEventListener('click', () => {
                root.classList.toggle('light');
                applyIcon();
                try {
                    localStorage.setItem('billingin-theme', root.classList.contains('light') ? 'light' : 'dark');
                } catch (e) {}
            });
        })();
    </script>
</body>
</html>
