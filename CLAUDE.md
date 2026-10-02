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
