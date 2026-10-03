<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Mockup — BILLINGIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#070A16] text-white p-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-1">Mockup BILLINGIN</h1>
        <p class="text-gray-400 mb-8">Halaman statis (belum ada backend/data asli) hasil dari desain PNG. Dibuat {{ now()->format('d M Y') }}.</p>

        @foreach ($groups as $group => $pages)
            <h2 class="text-sm font-semibold text-indigo-400 uppercase tracking-wide mt-6 mb-2">{{ $group }}</h2>
            <ul class="grid sm:grid-cols-2 gap-2 mb-4">
                @foreach ($pages as $label => $path)
                    <li>
                        <a href="{{ url($path) }}" class="block rounded-lg bg-[#0F1428] border border-white/10 px-4 py-3 hover:border-indigo-500 transition">
                            {{ $label }}
                            <span class="block text-xs text-gray-500">{{ $path }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </div>
</body>
</html>
