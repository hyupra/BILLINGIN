<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BILLINGIN')</title>
    {{-- ponytail: Tailwind via CDN, no build step yet, matches resources/views/auth/login.blade.php.
         Swap for the real Vite+Tailwind pipeline in the Frontend sprint. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
</head>
<body class="min-h-screen bg-[#070A16] text-white flex">

    {{-- Mobile sidebar backdrop --}}
    <div id="sidebar-backdrop" onclick="toggleSidebar(false)"
        class="fixed inset-0 bg-black/60 z-30 hidden lg:hidden"></div>

    {{-- Sidebar (shared app shell — do not copy into individual pages) --}}
    <aside id="sidebar"
        class="w-64 shrink-0 bg-[#0B0F24] border-r border-white/10 flex flex-col justify-between
               fixed inset-y-0 left-0 z-40 -translate-x-full transition-transform duration-200
               lg:static lg:translate-x-0">
        <div>
            <div class="flex items-center gap-2 px-6 py-6">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">📶</span>
                <span class="font-bold text-lg">BILLING<span class="text-indigo-400">IN</span></span>
            </div>

            @php
                $navItems = [
                    ['key' => 'overview',     'label' => 'Ringkasan',  'href' => '/mockup/dashboard/ringkasan',
                        'icon' => 'M3 3v18h18 M7 15l3-4 3 3 5-7'],
                    ['key' => 'customers',    'label' => 'Pelanggan',  'href' => '/mockup/dashboard/pelanggan',
                        'icon' => 'M17 20h5v-1a4 4 0 00-5-4M9 20H4v-1a4 4 0 015-4m5-6a3 3 0 11-6 0 3 3 0 016 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['key' => 'invoices',     'label' => 'Tagihan',    'href' => '/mockup/dashboard/tagihan',
                        'icon' => 'M9 12h6m-6 4h6M8 3h8l3 3v15H5V6l3-3z'],
                    ['key' => 'payments',     'label' => 'Pembayaran', 'href' => '/mockup/dashboard/pembayaran',
                        'icon' => 'M3 7h18M3 7a2 2 0 012-2h14a2 2 0 012 2m0 0v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7m4 7h4'],
                    ['key' => 'packages',     'label' => 'Paket',      'href' => '/mockup/dashboard/paket',
                        'icon' => 'M21 8l-9-5-9 5 9 5 9-5zM3 8v8l9 5 9-5V8M12 13v8'],
                    ['key' => 'routers',      'label' => 'Router',     'href' => '/mockup/dashboard/router',
                        'icon' => 'M4 10h16v6H4zM8 16v2M16 16v2M8 13h.01M12 13h.01'],
                    ['key' => 'reports',      'label' => 'Laporan',    'href' => '/mockup/dashboard/laporan',
                        'icon' => 'M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['key' => 'settings',     'label' => 'Pengaturan', 'href' => '/mockup/dashboard/pengaturan',
                        'icon' => 'M12 15a3 3 0 100-6 3 3 0 000 6z M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 008.6 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.65 1.65 0 004.6 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 8.6a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06A1.65 1.65 0 008.6 4.6a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V8.6a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z'],
                    ['key' => 'subscription', 'label' => 'Langganan',  'href' => '/mockup/dashboard/langganan',
                        'icon' => 'M3 10h18M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2zM7 15h4'],
                ];
                $active = trim($__env->yieldContent('active'));
            @endphp

            <nav class="px-3 mt-2 space-y-1">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                               {{ $active === $item['key']
                                    ? 'bg-indigo-600/20 text-indigo-300 ring-1 ring-inset ring-indigo-500/30'
                                    : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $item['icon'] }}"></path>
                        </svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="px-6 py-6 border-t border-white/10">
            <p class="font-semibold text-sm">RTRW Net Mekar Jaya</p>
            <p class="text-gray-500 text-sm mb-3">Pak Andi &middot; Pemilik</p>
            <a href="/mockup/dashboard/ringkasan" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Keluar</a>
        </div>
    </aside>

    <div class="flex-1 min-w-0 flex flex-col">
        {{-- Mobile topbar (hamburger only below lg:) --}}
        <div class="lg:hidden flex items-center justify-between px-4 py-3 border-b border-white/10 bg-[#0B0F24]">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-sm">📶</span>
                <span class="font-bold">BILLING<span class="text-indigo-400">IN</span></span>
            </div>
            <button type="button" onclick="toggleSidebar(true)" class="w-9 h-9 rounded-lg border border-white/10 flex items-center justify-center" aria-label="Buka menu">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>

        <main class="flex-1 p-6 lg:p-10">
            <div class="flex items-start justify-between gap-4 flex-wrap mb-8">
                <div>
                    @hasSection('back-link')
                        <div class="mb-2 text-sm">@yield('back-link')</div>
                    @endif
                    <h1 class="text-2xl lg:text-3xl font-bold">@yield('page-title')</h1>
                    @hasSection('page-subtitle')
                        <p class="text-gray-400 mt-1">@yield('page-subtitle')</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @yield('page-actions')
                    <button type="button" class="w-11 h-11 rounded-lg bg-[#0F1428] border border-white/10 flex items-center justify-center text-lg hover:bg-white/5" aria-label="Ganti tema" title="Ganti tema (mockup, non-fungsional)">
                        🔆
                    </button>
                </div>
            </div>

            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar(open) {
            const sb = document.getElementById('sidebar');
            const bd = document.getElementById('sidebar-backdrop');
            sb.classList.toggle('-translate-x-full', !open);
            bd.classList.toggle('hidden', !open);
        }

        // Shared helper for the C-state (memuat/kosong/error) demo switchers
        // used on list pages (Pelanggan, Tagihan, Pembayaran). Not reused
        // elsewhere, kept here only so it's defined once.
        function setTableState(group, state) {
            document.querySelectorAll('[data-state-group="' + group + '"] [data-state]').forEach(function (el) {
                el.classList.toggle('hidden', el.dataset.state !== state);
            });
            document.querySelectorAll('[data-state-switch="' + group + '"] [data-state-btn]').forEach(function (btn) {
                btn.classList.toggle('bg-indigo-600', btn.dataset.stateBtn === state);
                btn.classList.toggle('text-white', btn.dataset.stateBtn === state);
                btn.classList.toggle('bg-[#0F1428]', btn.dataset.stateBtn !== state);
                btn.classList.toggle('text-gray-400', btn.dataset.stateBtn !== state);
            });
        }
    </script>

    @yield('scripts')
</body>
</html>
