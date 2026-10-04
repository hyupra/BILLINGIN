@extends('mockup.dashboard.layout')

@section('title', 'Impor Pelanggan — BILLINGIN')
@section('active', 'customers')
@section('back-link')
    <a href="/mockup/dashboard/customers" class="text-indigo-400 hover:text-indigo-300">&larr; Kembali ke Pelanggan</a>
@endsection
@section('page-title', 'Impor Pelanggan dari CSV')

@section('content')
    <div class="max-w-4xl space-y-6">

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-2">1. Siapkan file</h2>
            <p class="text-gray-400 text-sm mb-4">Gunakan templat agar kolom sesuai: nama, nomor WhatsApp, alamat, paket, username PPPoE.</p>
            <a href="#" role="button" onclick="mockupToast('Templat CSV belum tersedia di mockup ini'); return false;" class="h-11 px-4 inline-flex items-center gap-2 rounded-lg border border-white/15 font-semibold hover:bg-white/5">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                Unduh Templat CSV
            </a>
        </div>

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-4">2. Unggah file</h2>

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('import_summary'))
                @php $summary = session('import_summary'); @endphp
                <div class="mb-5 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                    {{ $summary['created'] }} pelanggan berhasil diimpor.
                </div>
                @if (count($summary['errors']))
                    <div class="mb-5 rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
                        <p class="font-semibold mb-2">{{ count($summary['errors']) }} baris dilewati:</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($summary['errors'] as $rowError)
                                <li>{{ $rowError }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif

            <form method="POST" action="{{ route('customers.import.store') }}" enctype="multipart/form-data">
                @csrf
                <label for="csv-file" class="block border-2 border-dashed border-white/15 rounded-xl py-14 text-center cursor-pointer hover:border-indigo-500/50 hover:bg-white/5 transition">
                    <span class="block text-3xl mb-3">⬆</span>
                    <span class="block font-semibold">Seret file ke sini atau klik untuk memilih</span>
                    <span class="block text-gray-500 text-sm mt-1">Format .csv, maksimal 5 MB</span>
                    <input id="csv-file" name="csv_file" type="file" accept=".csv" required class="hidden">
                </label>
                <p id="csv-filename" class="hidden text-sm text-gray-300 mt-3"></p>
                <button id="csv-import-btn" type="submit" class="mt-4 h-11 px-5 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Impor</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.getElementById('csv-file').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const nameEl = document.getElementById('csv-filename');
        const btn = document.getElementById('csv-import-btn');
        if (file) {
            nameEl.textContent = 'File dipilih: ' + file.name;
            nameEl.classList.remove('hidden');
            btn.classList.remove('hidden');
        } else {
            nameEl.classList.add('hidden');
            btn.classList.add('hidden');
        }
    });
</script>
@endsection
