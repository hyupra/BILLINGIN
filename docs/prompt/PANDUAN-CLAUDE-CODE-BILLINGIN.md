# Panduan Menjalankan Proyek BILLINGIN di Claude Code (VS Code)

## 1. Struktur folder proyek

Buat satu folder proyek, lalu taruh semua dokumen di `docs/`. Jangan menempel isi dokumen ke chat. Cukup rujuk lewat `@path`.

```
billingin/
├── CLAUDE.md                      ← diisi dari bagian 3 (dibaca otomatis tiap sesi)
├── docs/
│   ├── spec/
│   │   ├── BACKEND-BILLINGIN-laravel-sqlserver.md   ← SPEK UTAMA backend
│   │   ├── BRD-website-pembayaran-wifi.md           ← konteks bisnis
│   │   └── 02_BRD_BILLING_WIFI.md                   ← konteks bisnis (fitur detail)
│   ├── design/                    ← hasil ekspor Claude Design (bagian 2)
│   │   ├── html/   (satu file .html per layar)
│   │   └── png/    (satu file .png per layar, sebagai acuan visual)
│   └── prompt/
│       └── PROMPT-gabungan-desain-platform-wifi.md  ← arsip saja, tidak perlu dibaca Claude Code
└── (kode Laravel dibuat oleh Claude Code di sini)
```

## 2. Dokumen mana untuk tahap mana

| Tahap | Dokumen yang diberikan | Tidak perlu |
|---|---|---|
| **Backend** (S0–S6) | `BACKEND-...md` (utama) + kedua BRD (konteks) | Hasil desain, prompt desain |
| **Frontend** (setelah backend S2 jalan) | `BACKEND-...md` bagian 13 (API) + **ekspor desain (HTML + PNG)** | BRD lengkap |
| Prompt desain gabungan | **Tidak diberikan ke Claude Code.** Itu prompt untuk Claude Design, tugasnya sudah selesai | |

Alasannya: Claude Code bekerja baik bila konteks kecil dan terarah. Kerjakan **satu sprint per sesi**, bukan semua dokumen sekaligus.

### Ekspor desain: pakai format apa

Dari menu Export di Claude Design (seperti pada layar Anda):

| Format | Pakai? | Alasan |
|---|---|---|
| **HTML** | **Ya, utama** | Claude Code bisa membaca struktur, CSS variables (token warna, radius, spasi), dan teks UI. Bahan terbaik untuk menyusun komponen Vue |
| **PNG** | **Ya, pendamping** | Acuan visual untuk dicocokkan hasilnya. Claude Code bisa melihat gambar |
| PDF | Tidak | Hanya untuk cetak/berbagi, struktur hilang |

Ekspor **satu file per layar** (C1 Ringkasan, dan seterusnya), beri nama berurutan, mis. `A01-hero.html`, `B03-pilih-metode.html`, `C01-ringkasan.html`. Simpan di `docs/design/html` dan `docs/design/png`.

Catatan: file HTML dari desain adalah **acuan**, bukan kode produksi. Perintahkan Claude Code untuk **membuat ulang sebagai komponen Vue + Tailwind** memakai token yang sama, bukan menyalin HTML mentah.

## 3. Isi `CLAUDE.md` (taruh di root proyek)

```markdown
# BILLINGIN

Platform billing & pembayaran WiFi multi-ISP (multi-tenant). Bahasa UI: Indonesia.

## Sumber kebenaran
- Spek backend: @docs/spec/BACKEND-BILLINGIN-laravel-sqlserver.md (ikuti, termasuk ERD, status, aturan §7, driver §10)
- Konteks bisnis: @docs/spec/BRD-website-pembayaran-wifi.md dan @docs/spec/02_BRD_BILLING_WIFI.md
- Desain UI: docs/design/ (HTML = struktur & token, PNG = acuan visual)
- Jika spek ambigu atau bertentangan: BERHENTI dan tanya saya. Jangan menebak.

## Stack
Laravel + SQL Server (sqlsrv) + Redis + Horizon + Inertia/Vue 3/TypeScript/Tailwind. Semua jalan di Docker.
Midtrans (QRIS) + transfer manual. WhatsApp lewat driver. MikroTik lewat NetworkDriver (mikrotik_pppoe | manual).

## Aturan wajib
1. Uang = bigint rupiah. Dilarang float. Biaya gateway pakai integer math (spek §7.2).
2. Semua tabel bisnis punya tenant_id + trait BelongsToTenant. Job queue wajib membawa tenant_id.
3. Webhook harus idempoten (event_hash unik, lockForUpdate, cek signature + gross_amount + konfirmasi status ke Midtrans).
4. Kode MikroTik hanya di modul Network. Perintah router selalu lewat network_tasks (antrian), bukan dari request web.
5. Kredensial (router, Midtrans, WA, NIK) terenkripsi, tidak pernah dikirim ke frontend. Data pribadi selalu di-mask di halaman publik.
6. Setiap fitur wajib ada tes Pest. Jalankan tes sebelum menyatakan selesai.
7. Jangan commit .env atau kunci apa pun. Midtrans pakai SANDBOX saja sampai saya bilang produksi.
8. Verifikasi detail API Midtrans ke dokumentasi resmi sebelum menulis integrasi.

## Cara kerja
- Kerjakan satu sprint per sesi (lihat spek §18). Mulai dengan rencana (plan mode), tunggu persetujuan saya.
- Commit kecil dan sering, pesan commit dalam bahasa Inggris (conventional commits).
- Di akhir tiap sprint: ringkas apa yang selesai, apa yang belum, dan keputusan yang perlu saya ambil.
```

## 4. Langkah menjalankan di VS Code (Windows)

1. Pasang **Docker Desktop** (WSL2 aktif), **Git**, dan ekstensi **Claude Code** untuk VS Code (atau jalankan `claude` di terminal VS Code).
2. Buat folder `billingin`, jalankan `git init`, salin struktur §1, simpan `CLAUDE.md`.
3. Buka folder itu di VS Code. Buka terminal di root proyek, jalankan `claude`.
4. Tekan **Shift+Tab** sampai masuk **plan mode** (Claude merencanakan dulu, tidak langsung mengubah file).
5. Kirim **Prompt S0** (bagian 5). Baca rencananya, koreksi, baru setujui.
6. Setelah sprint selesai dan tes lolos: `git commit`, lalu mulai sesi baru (`/clear`) untuk sprint berikutnya.
7. Bila Claude Code tidak mengenali spek, ketik `@docs/spec/` lalu pilih file dari daftar.

Tips Windows: simpan proyek di dalam filesystem WSL (`\\wsl$\...`) agar Docker lebih cepat; atur `git config core.autocrlf input` agar akhiran baris konsisten.

## 5. Prompt untuk Claude Code

### Prompt S0 (Fondasi). Kirim pertama

```
Baca @CLAUDE.md dan @docs/spec/BACKEND-BILLINGIN-laravel-sqlserver.md (terutama §1-§5, §14-§16, §18).

Tugas: kerjakan SPRINT S0 (Fondasi) dari §18.

Cakupan:
1. Buat proyek Laravel di folder ini dengan Docker (nginx, app php-fpm, worker/Horizon, scheduler, redis). SQL Server dipakai dari host terpisah atau kontainer; tanyakan saya mana yang dipakai sebelum mengunci.
2. Pasang driver sqlsrv/pdo_sqlsrv + ODBC 18 di image sesuai §16.1. Pastikan versi PHP kompatibel, jelaskan pilihannya.
3. Konfigurasi koneksi sqlsrv, Redis, Horizon, queue terpisah: payments, network, messages, default.
4. Buat trait BelongsToTenant (global scope + auto-fill tenant_id), middleware set tenant context, dan Row-Level Security SQL Server sesuai §5.7.
5. Auth: Fortify + 2FA, guard `web` dan `customer`, Sanctum, spatie/laravel-permission mode teams.
6. Migrasi + model untuk: tenants, users, roles/permissions, login_histories, audit_logs.
7. Seeder: 1 superadmin, 2 tenant contoh, 1 admin per tenant.
8. Pest: tes bahwa user tenant A TIDAK bisa membaca data tenant B (lewat global scope DAN RLS).
9. CI sederhana (GitHub Actions) yang menjalankan tes.

Batasan: jangan mengerjakan fitur di luar S0.
Mulai dengan RENCANA terperinci (daftar file, urutan, risiko, pertanyaan untuk saya). Jangan menulis kode sebelum saya setujui.
```

### Prompt S1 (Master data + Pelanggan). Setelah S0 lolos

```
Baca @CLAUDE.md dan spek backend §5 (ERD 1 dan 2), §8.13, §8.14, §10, §13.3.
Kerjakan SPRINT S1: master data (areas, odps, packages, bank_accounts, routers + TEST KONEKSI lewat NetworkDriver) dan pelanggan (CRUD, impor Excel maks 500 baris dengan validasi per baris, enkripsi NIK + hash).
Aturan: pelanggan butuh >= 1 paket (§7.5).
Router: kredensial terenkripsi dan tidak pernah dikembalikan di API. Driver `manual` dan `mikrotik_pppoe` (testConnection dan createAccount dulu).
Sertakan Pest tests dan resource API sesuai §13.3.
Mulai dengan rencana, tunggu persetujuan saya.
```

### Prompt S2 (Tagihan + Pembayaran). Bagian terpenting

```
Baca @CLAUDE.md dan spek backend §5 (ERD 3), §6.1-6.2, §7, §8.2-8.5, §9.1, §11.
Kerjakan SPRINT S2: generator invoice (idempoten), halaman bayar publik (API), QRIS Midtrans per tenant, webhook idempoten, transfer manual + verifikasi admin, pembayaran tunai, struk PDF.
Wajib:
- Hitung biaya gateway dengan integer math sesuai §7.2 dan tulis unit test untuk beberapa nominal.
- Webhook: signature, gross_amount, konfirmasi GET status, event_hash unik, lockForUpdate. Tes: webhook ganda, signature palsu, nominal tidak cocok.
- Index unik "satu payment paid per invoice" sesuai DDL.
- Verifikasi parameter QRIS Midtrans ke dokumentasi resmi lebih dulu. Sebutkan bila ada beda dengan spek.
Gunakan SANDBOX. Mulai dengan rencana, tunggu persetujuan saya.
```

### Pola prompt sprint berikutnya (S3, S4, dst.)

```
Baca @CLAUDE.md dan spek backend bagian yang relevan untuk SPRINT S{n} di §18: [sebut nomor bagian].
Kerjakan SPRINT S{n}: [nama sprint].
Ikuti aturan di CLAUDE.md. Tulis tes Pest. Tandai bagian spek yang ambigu sebagai pertanyaan untuk saya.
Mulai dengan rencana, tunggu persetujuan saya.
```

### Prompt Frontend (setelah S2 jalan, desain sudah diekspor)

```
Baca @CLAUDE.md, spek backend §3.3 dan §13 (API), lalu lihat file di docs/design/html dan docs/design/png.

Tugas: bangun frontend Inertia + Vue 3 + TypeScript + Tailwind.
1. Ekstrak design tokens dari file HTML desain (warna, radius, spasi, bayangan, tipografi) menjadi CSS variables di satu file, dengan mode gelap dan terang. Tampilkan tabel token hasil ekstraksi ke saya dulu.
2. Buat komponen dasar (Button, Input, Card, Badge, Stepper, Accordion, Toast, Table) memakai token itu. Tombol minimal 48px. Responsive 320px ke atas.
3. Bangun halaman pembayaran pelanggan (A: cek tagihan, detail, pilih metode, QRIS, sukses, gagal/kedaluwarsa) terhubung ke API publik backend (§13.1). Jangan menyalin HTML desain mentah; buat ulang sebagai komponen.
4. Cocokkan tampilan dengan PNG di docs/design/png. Ambil screenshot hasil (Playwright) dan bandingkan.
Data pribadi harus ter-mask. Mulai dengan rencana, tunggu persetujuan saya.
```

## 6. Kesalahan yang sebaiknya dihindari

- **Jangan** minta "bangun semuanya sekaligus". Satu sprint per sesi, selesai dan teruji dulu.
- **Jangan** menempel keseluruhan dokumen ke chat. Rujuk dengan `@docs/spec/...`.
- **Jangan** memberi kunci Midtrans produksi atau kredensial router asli selama pengembangan.
- Jalankan `/clear` di antara sprint agar konteks tidak penuh. `CLAUDE.md` tetap dibaca ulang otomatis.
- Perlakukan dokumen backend sebagai **rancangan**. Bila Claude Code menemukan masalah (versi paket, parameter API), minta ia melaporkan, jangan membiarkannya diam-diam menyimpang.
