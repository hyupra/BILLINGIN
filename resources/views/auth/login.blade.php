<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — BILLINGIN</title>
    {{-- ponytail: Tailwind via CDN, no build step yet. Swap for the real
         Vite+Tailwind pipeline (with the actual design tokens) in the
         Frontend sprint. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white flex">
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#0B0F24] to-[#141A3A] flex-col justify-between p-12">
        <div class="flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">📶</span>
            <span class="font-bold text-lg">BILLING<span class="text-indigo-400">IN</span></span>
        </div>
        <div>
            <h1 class="text-4xl font-bold leading-tight mb-4">Tagihan WiFi lunas otomatis, tanpa kejar-kejar pelanggan.</h1>
            <p class="text-gray-400">Pantau tagihan, pembayaran, dan pelanggan terisolir dari satu dashboard.</p>
        </div>
        <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} BILLINGIN</p>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-2 lg:hidden">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">📶</span>
                    <span class="font-bold text-lg">BILLING<span class="text-indigo-400">IN</span></span>
                </div>
            </div>

            <h2 class="text-2xl font-bold mb-1">Masuk ke dashboard</h2>
            <p class="text-gray-400 mb-6">Gunakan akun mitra BILLINGIN Anda.</p>

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium mb-1.5">Email atau nomor WhatsApp</label>
                    <input id="email" name="email" type="text" value="{{ old('email') }}" required autofocus
                        placeholder="nama@usaha.com"
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium">Password</label>
                        <a href="/mockup/forgot-password" class="text-sm text-indigo-400 hover:text-indigo-300">Lupa password?</a>
                    </div>
                    <input id="password" name="password" type="password" required
                        placeholder="Masukkan password"
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-300">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-[#0F1428] text-indigo-500 focus:ring-indigo-500">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit"
                    class="w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
                    Masuk
                </button>

                <p class="text-center text-sm text-gray-400">
                    Belum punya akun? <a href="/mockup/register" class="text-indigo-400 hover:text-indigo-300 underline">Daftar gratis 3 hari</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
