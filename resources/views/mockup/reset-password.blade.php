<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Baru — BILLINGIN</title>
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
            <h1 class="text-4xl font-bold leading-tight mb-4">Buat password baru yang kuat.</h1>
            <p class="text-gray-400">Gunakan minimal 8 karakter dengan huruf besar dan angka.</p>
        </div>
        <p class="text-gray-500 text-sm">&copy; {{ date('Y') }} BILLINGIN</p>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-2">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">📶</span>
                    <span class="font-bold text-lg">BILLING<span class="text-indigo-400">IN</span></span>
                </div>
                <button type="button" aria-label="Ganti mode tampilan" class="w-10 h-10 rounded-lg border border-white/10 bg-[#0F1428] flex items-center justify-center text-gray-300 hover:text-white">☀️</button>
            </div>

            <h2 class="text-2xl font-bold mb-1">Password baru</h2>
            <p class="text-gray-400 mb-6">Untuk akun w*****@gmail.com</p>

            <form action="#" onsubmit="return false" class="space-y-5">
                <div>
                    <label for="password" class="block text-sm font-medium mb-1.5">Password baru</label>
                    <input id="password" name="password" type="password" required autofocus
                        placeholder="Minimal 8 karakter" oninput="updateStrength(this.value)"
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <div class="flex gap-1.5 mt-2">
                        <span id="bar-1" class="h-1 flex-1 rounded-full bg-white/10"></span>
                        <span id="bar-2" class="h-1 flex-1 rounded-full bg-white/10"></span>
                        <span id="bar-3" class="h-1 flex-1 rounded-full bg-white/10"></span>
                    </div>
                    <p id="strength-label" class="text-xs text-gray-500 mt-1.5">Kekuatan password: belum diisi</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1.5">Ulangi password baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        placeholder="Ketik ulang password"
                        class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <button type="submit"
                    class="w-full h-12 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">
                    Simpan Password
                </button>
            </form>
        </div>
    </div>

    <script>
        function updateStrength(value) {
            const bars = [document.getElementById('bar-1'), document.getElementById('bar-2'), document.getElementById('bar-3')];
            const label = document.getElementById('strength-label');
            const colors = ['bg-red-500', 'bg-amber-500', 'bg-emerald-500'];
            bars.forEach(b => b.className = 'h-1 flex-1 rounded-full bg-white/10');

            if (!value) {
                label.textContent = 'Kekuatan password: belum diisi';
                return;
            }

            let score = 0;
            if (value.length >= 8) score++;
            if (/[A-Z]/.test(value) && /[0-9]/.test(value)) score++;
            if (value.length >= 12 && /[^A-Za-z0-9]/.test(value)) score++;
            score = Math.max(score, 1);

            const labels = ['Lemah', 'Sedang', 'Kuat'];
            for (let i = 0; i < score; i++) bars[i].className = `h-1 flex-1 rounded-full ${colors[score - 1]}`;
            label.textContent = `Kekuatan password: ${labels[score - 1]}`;
        }
    </script>
</body>
</html>
