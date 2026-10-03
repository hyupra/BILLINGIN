<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password — BILLINGIN</title>
    {{-- ponytail: Tailwind via CDN, no build step yet, matches auth/login.blade.php.
         Visual mockup only — submits nowhere real. --}}
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
            <h1 class="text-4xl font-bold leading-tight mb-4">Lupa password? Tenang, kami bantu.</h1>
            <p class="text-gray-400">Kami kirim tautan reset ke email atau WhatsApp yang terdaftar.</p>
        </div>
        <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} BILLINGIN</p>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="flex items-center justify-between mb-8">
                <a href="{{ route('login') }}" class="flex items-center gap-1.5 text-sm font-semibold text-indigo-400 hover:text-indigo-300">
                    <span>&larr;</span> Kembali ke login
                </a>
                <button type="button" aria-label="Ganti mode tampilan" class="w-10 h-10 rounded-lg border border-white/10 bg-[#0F1428] flex items-center justify-center text-gray-300 hover:text-white">☀️</button>
            </div>

            <h2 class="text-2xl font-bold mb-1">Atur ulang password</h2>
            <p class="text-gray-400 mb-6">Masukkan email atau nomor WhatsApp akun Anda.</p>

            <form action="#" onsubmit="return false" class="space-y-5">
                <div>
                    <label for="identifier" class="block text-sm font-medium mb-1.5">Email atau nomor WhatsApp</label>
                    <input id="identifier" name="identifier" type="text" required autofocus
                        placeholder="nama@usaha.com"
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit"
                    class="w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
                    Kirim Tautan Reset
                </button>
            </form>
        </div>
    </div>
</body>
</html>
