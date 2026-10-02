# Business Requirements Document (BRD)
## Platform Billing & Website Pembayaran WiFi (RTRW Net / ISP Lokal)

| Item | Isi |
|---|---|
| Versi | 0.1 (Draft) |
| Tanggal | 3 Oktober 2026 |
| Penyusun | Wahyu Prayoga |
| Status | Draft untuk ditinjau |
| Referensi | Analisis publik halaman sisbro.id (struktur fitur & model harga sebagai pembanding, bukan untuk ditiru persis) |

> Catatan: angka harga, biaya gateway, dan target adalah **asumsi awal** dan harus divalidasi sebelum peluncuran.

---

## 1. Ringkasan Eksekutif

Banyak pengusaha WiFi lokal / RTRW Net menagih pelanggan secara manual (chat, transfer, tunai), sehingga tagihan terlambat, rekonsiliasi sulit, dan pemutusan/pembukaan layanan dilakukan manual. Proyek ini membangun platform yang:

1. Menyediakan **halaman pembayaran online** (QRIS, Virtual Account, gerai minimarket) untuk pelanggan WiFi.
2. Mengotomatiskan **penagihan, isolir, dan buka isolir** pada router MikroTik.
3. Menyediakan **dashboard mitra** untuk pelanggan, tagihan, laporan.
4. (Fase lanjut) menjual **voucher hotspot online** dan menyediakan aplikasi pelanggan.

Model bisnis: langganan berbasis jumlah pelanggan aktif per bulan, dengan trial gratis.

---

## 2. Latar Belakang & Masalah

| Masalah | Dampak |
|---|---|
| Penagihan manual via chat/transfer | Waktu admin terbuang, salah catat |
| Konfirmasi pembayaran manual | Pelanggan menunggu internet aktif kembali, komplain naik |
| Isolir dilakukan manual di router | Telat/terlewat, pendapatan bocor |
| Metode bayar terbatas | Pelanggan tanpa mobile banking sulit bayar |
| Laporan keuangan tersebar | Sulit tahu tunggakan dan arus kas |

---

## 3. Tujuan Bisnis & Indikator Keberhasilan

| Tujuan | KPI | Target (12 bulan, asumsi) |
|---|---|---|
| Mengurangi kerja manual penagihan | Waktu admin untuk penagihan per bulan | Turun ≥ 70% |
| Mempercepat pelunasan | Rata-rata hari keterlambatan | Turun ≥ 40% |
| Otomatisasi layanan | Persentase buka isolir otomatis ≤ 5 menit setelah bayar | ≥ 95% |
| Pertumbuhan platform | Jumlah mitra aktif | 50 mitra |
| Volume | Jumlah pelanggan akhir terkelola | 10.000 |
| Konversi | Trial → berbayar | ≥ 30% |
| Keandalan | Uptime halaman bayar | ≥ 99,5% |

---

## 4. Ruang Lingkup

### 4.1 Dalam Lingkup (MVP — Fase 1)
- Landing page marketing + kalkulator harga
- Registrasi mitra, trial 3 hari
- Manajemen pelanggan dan paket
- Pembuatan tagihan berulang otomatis (bulanan)
- Halaman pembayaran pelanggan (QRIS, VA bank, gerai)
- Webhook konfirmasi pembayaran dan pencatatan transaksi
- Integrasi MikroTik: isolir dan buka isolir otomatis (PPPoE secret / profile)
- Notifikasi WhatsApp: tagihan, pengingat, bukti bayar
- Dashboard mitra: ringkasan, pelanggan, tagihan, pembayaran, pengaturan
- Laporan dasar (pendapatan, tunggakan)
- Penagihan langganan platform ke mitra

### 4.2 Fase 2
- Voucher hotspot online + toko per mitra + pengiriman WhatsApp
- Tiket komplain pelanggan
- Pencatatan pengeluaran dan laporan keuangan lengkap
- Broadcast pesan massal
- Multi router tak terbatas, monitoring traffic/secret online-offline
- Halaman isolir kustom

### 4.3 Fase 3
- Aplikasi Android pelanggan
- Peta ODP interaktif
- Integrasi manajemen ONT (GenieACS/TR-069): ganti SSID, password WiFi, restart
- Program reward marketing, upgrade paket mandiri
- Multi bahasa (ID/EN)

### 4.4 Di Luar Lingkup
- Menjadi penyelenggara payment gateway sendiri (memakai gateway pihak ketiga berlisensi)
- Penyediaan hardware jaringan
- Akuntansi penuh (pajak, neraca)

---

## 5. Pemangku Kepentingan & Persona

| Peran | Kepentingan |
|---|---|
| Pemilik platform (Anda) | Pendapatan langganan, reputasi, kepatuhan |
| Mitra / pemilik ISP | Penagihan otomatis, laporan, kontrol layanan |
| Admin/teknisi mitra | Operasional harian, tiket |
| Pelanggan WiFi | Bayar mudah, internet cepat aktif, bukti bayar |
| Pembeli voucher | Beli cepat, kredensial instan |
| Penyedia payment gateway | Transaksi sah, rekonsiliasi |
| Penyedia WhatsApp gateway | Pengiriman pesan sesuai kebijakan |

**Persona 1 — Pak Andi (mitra):** 150 pelanggan, 2 router MikroTik, mengelola sendiri bersama satu admin; ingin tidak lagi menagih manual.
**Persona 2 — Bu Sari (pelanggan):** tidak punya mobile banking, bayar di minimarket; butuh kode bayar sederhana.
**Persona 3 — Raka (pembeli voucher):** pengguna hotspot, ingin beli voucher dari HP tanpa antre.

---

## 6. Proses Bisnis Utama

### 6.1 Siklus Tagihan
1. Sistem membuat tagihan otomatis pada tanggal terjadwal.
2. Sistem mengirim tagihan + tautan bayar via WhatsApp.
3. Pelanggan membuka halaman bayar, memilih metode, membayar.
4. Gateway mengirim webhook → sistem memvalidasi (signature, nominal, idempotensi).
5. Sistem menandai lunas, membuka isolir di router, kirim bukti bayar.
6. Jika lewat jatuh tempo: pengingat → isolir otomatis sesuai kebijakan mitra.

### 6.2 Onboarding Mitra
Daftar → verifikasi nomor/email → trial 3 hari → input router dan paket → impor pelanggan → hubungkan gateway → aktif berlangganan.

### 6.3 Penjualan Voucher (Fase 2)
Pembeli pilih paket → bayar → webhook sukses → sistem membuat user di MikroTik → kirim kredensial ke WhatsApp.

### 6.4 Pencairan Dana
Alternatif model (keputusan terbuka, lihat bagian 14):
- **A.** Dana langsung ke rekening mitra (sub-merchant gateway).
- **B.** Dana masuk ke platform lalu dicairkan (settlement) — membutuhkan kepatuhan lebih berat.

---

## 7. Kebutuhan Bisnis (Business Requirements)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| BR-01 | Platform harus memungkinkan mitra menagih pelanggan secara otomatis | Must |
| BR-02 | Pelanggan harus bisa membayar tanpa login dengan beberapa metode | Must |
| BR-03 | Status layanan harus otomatis menyesuaikan status pembayaran | Must |
| BR-04 | Mitra harus melihat posisi tagihan dan pendapatan real-time | Must |
| BR-05 | Biaya platform harus transparan dan dapat disimulasikan | Must |
| BR-06 | Data setiap mitra harus terisolasi dari mitra lain | Must |
| BR-07 | Platform harus menyediakan jejak audit transaksi | Must |
| BR-08 | Mitra dapat menjual voucher hotspot online | Should |
| BR-09 | Pelanggan dapat mengajukan komplain dan memantau progresnya | Should |
| BR-10 | Mitra dapat mengelola perangkat ONT dari dashboard | Could |
| BR-11 | Tersedia aplikasi mobile pelanggan | Could |

---

## 8. Kebutuhan Fungsional (Ringkas)

### 8.1 Landing Page & Registrasi
- FR-01 Tampilkan fitur, demo, harga, FAQ (lihat dokumen DESIGN).
- FR-02 Kalkulator harga: input jumlah pelanggan dan router, output biaya per bulan dan per periode.
- FR-03 Registrasi mitra dengan verifikasi; mulai trial otomatis.

### 8.2 Manajemen Pelanggan & Paket
- FR-10 CRUD pelanggan, impor CSV.
- FR-11 CRUD paket (nama, kecepatan, harga, siklus).
- FR-12 Status pelanggan: aktif, isolir, berhenti.
- FR-13 Biaya tambahan dan diskon per pelanggan.

### 8.3 Penagihan
- FR-20 Generate tagihan berulang terjadwal.
- FR-21 Pengingat otomatis (H-3, H-0, H+n) via WhatsApp.
- FR-22 Pencatatan pembayaran manual (tunai/transfer) oleh admin.
- FR-23 Cetak/unduh bukti bayar.

### 8.4 Halaman Pembayaran Pelanggan
- FR-30 Cek tagihan via ID pelanggan atau nomor HP.
- FR-31 Pilihan metode: QRIS, VA, gerai.
- FR-32 Status pembayaran otomatis diperbarui (webhook + polling).
- FR-33 Penanganan kedaluwarsa dan pembayaran ulang.
- FR-34 Penyamaran data pribadi di halaman publik.

### 8.5 Integrasi Jaringan
- FR-40 Tambah router MikroTik (API/VPN), uji koneksi.
- FR-41 Isolir dan buka isolir otomatis.
- FR-42 Sinkronisasi daftar secret/user.
- FR-43 Monitoring status online/offline (Fase 2).

### 8.6 Dashboard & Laporan
- FR-50 Ringkasan pendapatan, tunggakan, jumlah isolir.
- FR-51 Laporan transaksi dan ekspor CSV/Excel.
- FR-52 Rekonsiliasi dengan data gateway.

### 8.7 Langganan Platform
- FR-60 Hitung tagihan platform = jumlah pelanggan aktif × tarif + biaya router/VPN (variabel konfigurasi).
- FR-61 Periode pembayaran (mis. per 3 bulan), pengingat, pembatasan akun saat menunggak.

---

## 9. Kebutuhan Non-Fungsional

| Kategori | Persyaratan |
|---|---|
| Keamanan | HTTPS wajib; verifikasi signature webhook; hash password; rate-limit; enkripsi kredensial router dan API key; RBAC |
| Isolasi data | Multi-tenant dengan pemisahan data per mitra |
| Ketersediaan | Uptime ≥ 99,5%; backup harian; RPO ≤ 24 jam, RTO ≤ 4 jam |
| Performa | Halaman bayar < 2 detik (4G); pemrosesan webhook < 5 detik |
| Skalabilitas | Mendukung 100 mitra / 50.000 pelanggan tanpa redesain |
| Idempotensi | Webhook ganda tidak menyebabkan pembayaran ganda |
| Observabilitas | Log terstruktur, alert kegagalan webhook/isolir |
| Kompatibilitas | Browser modern, mobile-first |
| Kepatuhan | UU Pelindungan Data Pribadi (UU PDP), ketentuan gateway dan OJK/BI yang berlaku |
| Lokalisasi | Bahasa Indonesia, zona waktu WIB/WITA/WIT |

---

## 10. Model Bisnis & Harga (Asumsi, Perlu Validasi)

Pembanding publik: pesaing menawarkan tarif sekitar **Rp 350 per pelanggan per bulan**, biaya VPN per router, tanpa biaya setup, trial 3 hari, pembayaran per 3 bulan. Angka ini hanya acuan pasar.

| Komponen | Usulan awal |
|---|---|
| Langganan | Per pelanggan aktif per bulan (variabel konfigurasi) |
| Add-on | Biaya per router/VPN, voucher, aplikasi pelanggan (Fase 2–3) |
| Trial | 3 hari, tanpa setup fee |
| Periode tagih | Bulanan atau per 3 bulan (diskon untuk periode panjang) |
| Biaya transaksi | Mengikuti biaya gateway + margin tetap (disiapkan sebagai opsi: ditanggung mitra atau pelanggan) |

**Contoh perhitungan (ilustrasi):** 100 pelanggan × Rp 350 = Rp 35.000 + 1 router × Rp 5.000 = **Rp 40.000/bulan** → Rp 120.000 per 3 bulan.

**Titik impas kasar:** biaya tetap bulanan (server, WhatsApp gateway, domain, dukungan) dibagi tarif per pelanggan. Hitung ulang setelah biaya riil diketahui.

---

## 11. Asumsi, Batasan, dan Ketergantungan

**Asumsi**
- Mayoritas mitra memakai MikroTik.
- Mitra bersedia memberi akses API/VPN ke router.
- Pelanggan akhir lebih nyaman bayar via QRIS/VA/gerai daripada transfer manual.

**Batasan**
- Tim kecil, anggaran terbatas → MVP harus ramping.
- Bergantung pada payment gateway dan WhatsApp gateway pihak ketiga.

**Ketergantungan**
- Payment gateway berlisensi (QRIS, VA, gerai) — perlu proses onboarding/KYB.
- Penyedia WhatsApp (resmi atau gateway) — perhatikan kebijakan pesan.
- Server dan koneksi VPN ke router mitra.

---

## 12. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Regulasi dana pihak ketiga (jika dana lewat platform) | Tinggi | Gunakan model sub-merchant/langsung ke mitra; konsultasi legal |
| Nomor WhatsApp diblokir | Tinggi | Gunakan penyedia resmi, template disetujui, batas kirim, fallback SMS/email |
| Keamanan kredensial router | Tinggi | Enkripsi, VPN, hak akses minimum, audit |
| Webhook gagal/ganda | Sedang | Idempotensi, retry, rekonsiliasi harian |
| Persaingan harga | Sedang | Diferensiasi UX, dukungan, fitur lokal; jangan hanya perang harga |
| Churn mitra karena dukungan lambat | Sedang | SLA dukungan, panduan onboarding, basis pengetahuan |
| Kebocoran data pelanggan | Tinggi | Kepatuhan UU PDP, minimisasi data, enkripsi, penyamaran |
| Ketergantungan satu gateway | Sedang | Abstraksi gateway agar bisa tambah penyedia |

---

## 13. Rencana Rilis (Usulan)

| Fase | Durasi (perkiraan) | Hasil |
|---|---|---|
| 0. Persiapan | 2–3 minggu | Nama brand, legal, pilih gateway, desain |
| 1. MVP | 8–10 minggu | Landing, registrasi, pelanggan, tagihan, halaman bayar, isolir, WhatsApp, dashboard dasar |
| 2. Beta tertutup | 4 minggu | 5–10 mitra uji, perbaikan |
| 3. Rilis publik | — | Pemasaran, dukungan |
| 4. Voucher & tiket | 6–8 minggu | Fase 2 lingkup |
| 5. Aplikasi & ONT | 10–12 minggu | Fase 3 lingkup |

---

## 14. Keputusan Terbuka (Perlu Jawaban Anda)

1. Alur dana: langsung ke rekening mitra atau lewat platform?
2. Payment gateway mana yang dipakai?
3. WhatsApp: gateway resmi (API) atau gateway tidak resmi? (berpengaruh pada risiko blokir)
4. Teknologi backend/frontend yang dikuasai tim?
5. Apakah fokus awal hanya MikroTik?
6. Tarif akhir dan siapa yang menanggung biaya transaksi?
7. Nama brand dan domain.

---

## 15. Kriteria Penerimaan MVP

- [ ] Mitra dapat mendaftar, menambah router, paket, dan pelanggan.
- [ ] Tagihan terbuat otomatis dan terkirim via WhatsApp.
- [ ] Pelanggan dapat membayar via minimal QRIS + 1 VA, status berubah lunas otomatis.
- [ ] Pelanggan terisolir dibuka otomatis ≤ 5 menit setelah bayar.
- [ ] Pembayaran ganda/webhook ulang tidak menggandakan transaksi.
- [ ] Dashboard menampilkan pendapatan dan tunggakan yang cocok dengan data gateway.
- [ ] Uji keamanan dasar (OWASP Top 10) lulus.
- [ ] Beta dengan ≥ 5 mitra berjalan 1 siklus tagihan penuh tanpa insiden kritis.

---

## 16. Kesimpulan

**Yang dipelajari dari referensi:**
- Pasar RTRW Net membeli **otomatisasi yang jelas**: bayar → internet aktif lagi tanpa admin.
- Daya tarik utama referensi: harga murah per pelanggan, transparan, bisa dihitung sendiri, dengan demo nyata dan dukungan cepat.
- Fitur inti yang terbukti dibutuhkan: gateway multi-metode, auto isolir, notifikasi WhatsApp, laporan. Fitur lain (ONT, peta ODP, aplikasi) adalah pelengkap yang bisa menyusul.

**Rekomendasi:**
1. Mulai dari **MVP sempit**: halaman bayar + tagihan otomatis + isolir MikroTik + WhatsApp + dashboard dasar. Jangan membangun 12 fitur sekaligus.
2. Diferensiasi jangan hanya harga; unggul di pengalaman bayar pelanggan, onboarding mudah, dan dukungan.
3. **Putuskan alur dana dan gateway lebih dulu** karena menentukan aspek legal, arsitektur, dan margin.
4. Siapkan risiko terbesar sejak awal: keamanan kredensial router, pemblokiran WhatsApp, dan kepatuhan UU PDP.
5. Validasi dengan 5–10 mitra beta sebelum rilis publik, lalu sesuaikan tarif berdasarkan biaya riil.

> Dokumen ini bukan nasihat hukum atau keuangan; konsultasikan aspek regulasi pembayaran dengan pihak berwenang/penasihat hukum sebelum peluncuran.
