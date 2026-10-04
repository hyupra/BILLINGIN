# S1 — Master Data (Paket, Router) & Pelanggan

> Status: Draft disetujui user secara konversasional, menunggu review spec tertulis.
> Sumber rujukan: `docs/spec/BACKEND-BILLINGIN-laravel-sqlserver.md` (dokumen rancangan backend lengkap).
> Scope sprint ini: **S1** dari roadmap §18 dokumen rujukan, dipangkas ke entitas yang sudah punya mockup UI.

## Context

BILLINGIN saat ini adalah mockup UI murni (Blade + data statis, 0 model/migration/controller/route API).
Laporan QA v2 (3 Okt 2026) mengonfirmasi ini. Dokumen rancangan backend (`docs/spec/...`) sudah
berisi desain lengkap 13 sprint (S0–S13) tapi belum ada implementasi sama sekali — "rancangan,
bukan implementasi" (§19.3 dokumen itu).

S0 (fondasi: Docker, auth Fortify, tenant, RBAC Spatie, RLS SQL Server, audit log) **sudah selesai**
di kode saat ini. Sprint ini adalah **S1**: data master yang jadi fondasi segala fitur billing
berikutnya (Pelanggan tidak bisa ada tanpa Paket dan Router).

**Keputusan desain yang sudah dikonfirmasi user:**
1. Frontend tetap Blade + Tailwind CDN yang sudah ada (bukan migrasi ke Inertia+Vue yang
   direkomendasikan dokumen rancangan) — backend disambungkan bertahap ke view mockup yang ada,
   bukan rebuild SPA.
2. Scope S1 dipangkas ke entitas yang sudah punya halaman mockup: **Pelanggan, Paket, Router**.
   Area/ODP/Bank (bagian "Master" di dokumen rancangan) ditunda sampai ada kebutuhan/UI nyata.
3. Integrasi eksternal (Midtrans/MikroTik/WhatsApp) di-stub — S1 tidak butuh kredensial/akses
   provider nyata. Tombol "Test Koneksi" router cukup cek TCP reachability, bukan login RouterOS API.
4. URL tetap di `/mockup/dashboard/*` yang sudah ada (bukan pindah ke prefix `/admin/*` seperti di
   dokumen rancangan) — supaya QA yang sudah test manual di URL ini tidak perlu pindah referensi,
   dan progres terlihat langsung di halaman yang sudah dikenal.

## Yang dibangun

### Struktur modul

Mengikuti pola yang sudah ada di `app/Modules/Identity/` dan `app/Modules/Platform/`:

```
app/Modules/
├── Master/
│   ├── Models/Package.php
│   ├── Models/Router.php
│   └── Http/Controllers/PackageController.php, RouterController.php
└── Customer/
    ├── Models/Customer.php
    └── Http/Controllers/CustomerController.php, CustomerImportController.php
```

Semua model pakai trait `BelongsToTenant` (sudah ada di `app/Support/Tenancy/`) untuk scoping
otomatis per tenant — pola yang sama persis dipakai `Tenant`/`User` sekarang.

### Skema database (migration baru)

Dipangkas dari DDL penuh di dokumen rancangan (§5.6) — hanya kolom yang dipakai fitur/UI S1.
Kolom lain (area_id, odp_id, lat/lng, network_state, isolir_*, collector_id, agent_id, dst)
ditambahkan di migration terpisah pada sprint yang benar-benar membutuhkannya.

```
packages
  id, tenant_id, router_id (nullable FK), name, speed_label,
  base_price (bigint), ppn_percent (decimal), total_price (bigint),
  default_profile (nullable), is_active (bool), timestamps

routers
  id, tenant_id, name, network_driver (default 'manual'),
  host, api_port, api_username_enc, api_password_enc,
  status (default 'unknown'), last_seen_at (nullable), timestamps

customers
  id, tenant_id, customer_code (unique per tenant), name, phone,
  id_number_enc (nullable, NIK), address, package_id (FK), router_id (nullable FK),
  ppp_username (nullable), ppp_password_enc (nullable),
  billing_type (default 'postpaid'), due_day (nullable),
  extra_amount (bigint, default 0), discount (bigint, default 0),
  status (default 'active'), timestamps, deleted_at (soft delete)
```

Index: `uq_customers_code (tenant_id, customer_code)`, `uq_customers_ppp (tenant_id, router_id,
ppp_username) WHERE deleted_at IS NULL AND ppp_username IS NOT NULL` — mencegah PPPoE username
dobel di router yang sama, sesuai aturan dokumen §5.6.

NIK dan password PPPoE dienkripsi pakai `Crypt` (sama seperti disebut dokumen §15), tidak pernah
dikembalikan ke response/view setelah disimpan.

`customer_code` digenerate otomatis saat create, format `C-{4 digit sequential per tenant}` (meniru
pola data contoh di mockup: `C-1042`, dst), bukan diisi manual di form. `due_day` kalau dikosongkan
pakai default hardcode 10 untuk S1 (`tenants.due_day_default` dari dokumen rancangan belum
dipakai di sini — kolom itu sudah ada di tabel `tenants`, tapi menyambungkannya ke pengaturan
beneran adalah bagian halaman Pengaturan yang belum di-scope sprint ini).

### Routes (menggantikan closure generik di `routes/web.php`)

Semua di bawah middleware `auth` yang sudah ada (route group `/mockup/dashboard/*`), tenant
otomatis dari sesi — tidak ada parameter tenant di URL.

| Method | Path | Controller@action |
|---|---|---|
| GET | `/mockup/dashboard/customers` | `CustomerController@index` |
| GET | `/mockup/dashboard/customers-create` | `CustomerController@create` |
| POST | `/mockup/dashboard/customers` | `CustomerController@store` |
| GET | `/mockup/dashboard/customers/{customer}/edit` | `CustomerController@edit` |
| PUT | `/mockup/dashboard/customers/{customer}` | `CustomerController@update` |
| DELETE | `/mockup/dashboard/customers/{customer}` | `CustomerController@destroy` |
| GET | `/mockup/dashboard/customers-import` | `CustomerImportController@create` |
| POST | `/mockup/dashboard/customers-import` | `CustomerImportController@store` |
| GET | `/mockup/dashboard/packages` | `PackageController@index` |
| GET | `/mockup/dashboard/packages-create` | `PackageController@create` |
| POST | `/mockup/dashboard/packages` | `PackageController@store` |
| GET | `/mockup/dashboard/packages/{package}/edit` | `PackageController@edit` |
| PUT | `/mockup/dashboard/packages/{package}` | `PackageController@update` |
| DELETE | `/mockup/dashboard/packages/{package}` | `PackageController@destroy` |
| GET | `/mockup/dashboard/routers` | `RouterController@index` |
| GET | `/mockup/dashboard/routers-create` | `RouterController@create` |
| POST | `/mockup/dashboard/routers` | `RouterController@store` |
| GET | `/mockup/dashboard/routers/{router}/edit` | `RouterController@edit` |
| PUT | `/mockup/dashboard/routers/{router}` | `RouterController@update` |
| DELETE | `/mockup/dashboard/routers/{router}` | `RouterController@destroy` |
| POST | `/mockup/dashboard/routers/{router}/test-connection` | `RouterController@testConnection` |

Rute eksplisit ini didaftarkan **sebelum** closure generik `/mockup/dashboard/{path}` yang sudah
ada di `routes/web.php`, supaya Laravel mencocokkan rute spesifik dulu (sama pola seperti dashboard
route vs catch-all publik yang sudah ada sekarang).

### View yang perlu dibuat baru

- `packages-create.blade.php`, `packages-edit.blade.php` — belum ada sama sekali, tombol "Tambah
  Paket"/"Ubah" di `packages.blade.php` saat ini cuma toast stub.
- `routers-create.blade.php`, `routers-edit.blade.php` — sama, belum ada.
- `customers-edit.blade.php` — belum ada (cuma ada create).

View yang sudah ada (`customers.blade.php`, `customers-create.blade.php`, `packages.blade.php`,
`routers.blade.php`, `customers-import.blade.php`) disambungkan ke data asli: data statis `@php`
array diganti jadi data dari controller, tombol/form stub diganti form POST/PUT/DELETE beneran
dengan `@csrf` dan `@method`.

### Validasi

`FormRequest` per entity (`StoreCustomerRequest`, `UpdateCustomerRequest`, dst) — aturan mengikuti
atribut `required` yang sudah ada di Blade sekarang (nama, WA, paket, router wajib untuk
Pelanggan; dst), plus validasi server yang belum ada di mockup: format nomor WA (`08xx`/`62xx`),
NIK 16 digit kalau diisi, `ppp_username` unik per router.

### Import CSV (disederhanakan dari dokumen rancangan)

Dokumen rancangan (§8.13) mendesain staging table `import_rows` + job queue + laporan unduhan CSV
baris gagal. Untuk S1, ini disederhanakan jadi proses sinkron dalam satu request:

1. Baca file CSV yang diupload (validasi: harus `.csv`, maks 5 MB, sesuai yang sudah ditulis di
   halaman mockup).
2. Validasi tiap baris (paket ada, HP valid, username PPPoE unik) — kumpulkan error per baris,
   jangan berhenti di baris pertama yang salah.
3. Insert baris valid dalam satu transaction DB.
4. Render ulang halaman dengan ringkasan: jumlah berhasil + daftar baris gagal (nomor baris +
   alasan) — tanpa file CSV unduhan terpisah untuk baris gagal.

`ponytail: staging table + job queue + unduhan CSV baris gagal → tambahkan kalau volume impor
riil (ratusan+ baris) bikin proses sinkron ini lambat/timeout.`

### Test Koneksi Router (disederhanakan)

Dokumen rancangan menyebut driver `NetworkDriver` dengan `testConnection()` yang nantinya login ke
RouterOS API (§10.2, itu scope S3). Untuk S1: endpoint `POST .../routers/{router}/test-connection`
cukup melakukan percobaan koneksi TCP ke `host:api_port` dengan timeout pendek (±3 detik),
mengembalikan status *reachable*/*unreachable* + waktu respons. Tidak ada autentikasi RouterOS API
dicoba di sini.

`ponytail: implementasi NetworkDriver interface penuh (createAccount/isolate/reopen/dst) →
dibangun di S3 saat fitur isolir otomatis benar-benar dikerjakan.`

### Keamanan & isolasi tenant

- `BelongsToTenant` global scope (sudah ada) otomatis membatasi query ke tenant yang login.
- RLS SQL Server (sudah aktif untuk tabel lain) perlu di-extend ke 3 tabel baru ini — tambahkan ke
  security policy yang sudah ada (migration RLS existing, `2026_10_03_000005_add_rls_security_policy.php`,
  jadi contoh pola yang diikuti).
- Controller method `edit`/`update`/`destroy`/`show` pakai route-model binding implisit yang otomatis
  kena global scope — pelanggan tenant B tidak bisa diakses lewat ID manipulasi dari sesi tenant A
  (akan 404, bukan 403, karena query tidak menemukan row-nya sama sekali — ini test case eksplisit
  di prompt QA).

## Di luar scope S1 (sengaja tidak dikerjakan)

- Area, ODP, Bank (entitas "Master" lain di dokumen) — tidak ada UI, tidak ada kebutuhan sekarang.
- `network_tasks` table / antrian perintah router — baru berguna begitu ada yang memprosesnya (S3).
  Tidak dibuat sekarang supaya tidak ada tabel kosong tak terpakai.
- Permission granular Spatie (`customer.create`, dll) — baru 1 role per tenant (`admin_isp`) yang
  ada dan dia boleh semua; permission matrix baru relevan begitu ada role kustom (kasir/teknisi).
- Invoice/Payment/Billing sama sekali — itu S2, Pelanggan di S1 belum generate tagihan apa pun.
- Integrasi Midtrans/MikroTik API/WhatsApp — distub sesuai keputusan user.

## Testing

Pest, mengikuti pola testing yang disebut dokumen §17.1:

- **Isolasi tenant** (prioritas tertinggi, eksplisit disebut kriteria penerimaan dokumen §17.2-12):
  user tenant A tidak bisa membaca/mengubah/menghapus data tenant B lewat Customer/Package/Router,
  baik lewat listing maupun akses langsung by-ID.
- CRUD + validasi tiap entity (field wajib kosong ditolak, data valid tersimpan dan terbaca balik).
- Import CSV: baris valid masuk, baris invalid dilaporkan tanpa membatalkan baris valid lainnya.
- `ppp_username` unik per router — baris kedua dengan username sama di router sama harus ditolak.

Tidak ada test end-to-end browser di level ini (itu kerja tim QA manual per prompt yang sudah
disiapkan) — verifikasi otomatis cukup di level Pest + manual click-through sebelum serah terima.

## Verifikasi end-to-end

1. `vendor/bin/pest` hijau untuk semua test baru di atas.
2. Jalan manual: login sebagai `admin@tenant-a.test`, tambah 1 paket, 1 router, 1 pelanggan,
   reload halaman masing-masing → data tetap ada (bukan hilang kayak form stub sekarang).
3. Jalan manual isolasi tenant: ulangi langkah 2 sebagai `admin@tenant-b.test`, pastikan tidak
   melihat data tenant A sama sekali.
4. Deploy ke staging (`103.191.92.163:8080`) dengan prosedur rebuild image yang benar (lihat
   catatan di riwayat kerja: `docker compose up -d --build app worker scheduler`, BUKAN `restart`
   saja — kode di-`COPY` ke image, tidak ada bind-mount).
5. Beri tahu tim QA (prompt sudah disiapkan terpisah) untuk menjalankan skenario S1 lengkap.
