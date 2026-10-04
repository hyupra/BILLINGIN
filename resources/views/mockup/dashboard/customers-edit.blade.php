@extends('mockup.dashboard.layout')

@section('title', 'Ubah Pelanggan — BILLINGIN')
@section('active', 'customers')
@section('back-link')
    <a href="/mockup/dashboard/customers" class="text-indigo-400 hover:text-indigo-300">&larr; Kembali ke Pelanggan</a>
@endsection
@section('page-title', 'Ubah Pelanggan')

@section('content')
    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300 max-w-4xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="customer-form" method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6 max-w-4xl">
        @csrf
        @method('PUT')

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-5">Data pelanggan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nama lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" placeholder="Contoh: Budi Santoso" required class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="0857xxxxxxx" required class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">NIK (opsional)</label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}" placeholder="Kosongkan jika tidak diubah" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium mb-1.5">Alamat pemasangan</label>
                    <input type="text" name="address" value="{{ old('address', $customer->address) }}" placeholder="Jalan, RT/RW, dusun" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-5">Layanan dan jaringan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Paket</label>
                    <select name="package_id" required class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Pilih paket —</option>
                        @foreach ($packages as $package)
                            <option value="{{ $package->id }}" @selected(old('package_id', $customer->package_id) == $package->id)>{{ $package->name }} &middot; Rp {{ number_format($package->totalPrice(), 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Router</label>
                    <select name="router_id" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">— Tidak dipasang router —</option>
                        @foreach ($routers as $router)
                            <option value="{{ $router->id }}" @selected(old('router_id', $customer->router_id) == $router->id)>{{ $router->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Username PPPoE</label>
                    <input type="text" name="ppp_username" value="{{ old('ppp_username', $customer->ppp_username) }}" placeholder="budi-s-01" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-white/10 bg-[#0B0F24] p-6">
            <h2 class="font-bold mb-5">Penagihan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tanggal mulai</label>
                    <input type="date" value="2026-10-03" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Tanggal isolir otomatis</label>
                    <input type="number" name="due_day" min="1" max="28" placeholder="10" value="{{ old('due_day', $customer->due_day) }}" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Biaya tambahan (Rp)</label>
                    <input type="number" name="extra_amount" value="{{ old('extra_amount', $customer->extra_amount) }}" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Diskon (Rp)</label>
                    <input type="number" name="discount" value="{{ old('discount', $customer->discount) }}" class="w-full h-12 px-4 rounded-lg bg-[#0F1428] border border-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex items-center gap-6 flex-wrap">
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="billing_type" value="postpaid" @checked(old('billing_type', $customer->billing_type) === 'postpaid') class="w-4 h-4 text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                    Pascabayar (tagih tiap bulan)
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="radio" name="billing_type" value="prepaid" @checked(old('billing_type', $customer->billing_type) === 'prepaid') class="w-4 h-4 text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                    Prabayar
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" checked class="w-4 h-4 rounded text-indigo-500 bg-[#0F1428] border-white/20 focus:ring-indigo-500">
                    Kirim tagihan lewat WhatsApp
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="h-12 px-6 rounded-lg bg-indigo-600 hover:bg-indigo-500 font-semibold transition">Simpan Perubahan</button>
            <a href="/mockup/dashboard/customers" class="h-12 px-6 inline-flex items-center rounded-lg border border-white/15 font-semibold hover:bg-white/5">Batal</a>
        </div>
    </form>
@endsection
