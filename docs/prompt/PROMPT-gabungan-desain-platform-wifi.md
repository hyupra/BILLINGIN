# PROMPT DESAIN: Platform Billing & Pembayaran WiFi / RTRW Net

> Cara pakai: lampirkan keempat file .md, lalu kirim seluruh isi file ini ke agent.

---

## 0. Keputusan yang sudah ditetapkan

| # | Keputusan | Nilai |
|---|-----------|-------|
| 1 | Nama brand | **BILLINGIN** (logo = wordmark/ikon buatan sendiri, bukan milik pihak lain) |
| 2 | Gaya & warna aksen | **Dark modern**: antarmuka dark-first, permukaan gelap berlapis, satu warna aksen cerah yang kontras (arah: biru-indigo). Nilai persis palet mengikuti token di dokumen DESIGN |
| 3 | Mode tampilan | **Gelap dan terang**, ada tombol pindah mode. Default gelap, ikuti preferensi sistem perangkat |
| 4 | Lebar layar | **Responsive untuk semua ukuran HP** (sekitar 320px sampai 480px), lalu tablet dan desktop. Tanpa lebar tetap, tanpa scroll horizontal |
| 5 | Tinggi minimum tombol | 48px (area sentuh minimal 48x48px) |

Aturan konflik: jika dokumen DESIGN berbeda dengan tabel di atas, **tabel di atas yang menang**, lalu dokumen DESIGN, lalu BRD. Jangan menebak. Jika ada konflik lain yang penting, **tanyakan dulu** sebelum mulai.

---

## 1. Peran & tujuan

Kamu adalah desainer UI/UX senior. Buatkan **desain visual high-fidelity yang bisa dilihat dan diklik** (prototipe HTML interaktif, bukan hanya deskripsi) untuk platform billing & pembayaran WiFi RTRW Net, terdiri dari **tiga bagian**:

- **Bagian A:** Landing page marketing, untuk pemilik usaha WiFi/ISP/RTRW Net.
- **Bagian B:** Website pembayaran, untuk pelanggan WiFi (mobile-first).
- **Bagian C:** Dashboard mitra, untuk pemilik ISP.

---

## 2. Dokumen sumber (empat file terlampir)

| Bagian | Spesifikasi desain | Kebutuhan bisnis (BRD) |
|--------|--------------------|------------------------|
| A dan C (marketing + dashboard mitra) | `DESIGN-landing-page-pembayaran-wifi.md` | `BRD-website-pembayaran-wifi.md` |
| B (pembayaran pelanggan) | `01_DESIGN_LANDING_PAGE.md` | `02_BRD_BILLING_WIFI.md` |

Cara memakai:
- Dokumen DESIGN menentukan **tampilan**: warna, tipografi, radius, spasi, bayangan, komponen, struktur layar, copywriting.
- Dokumen BRD hanya menjadi **konteks bisnis** dan sumber angka contoh, bukan sumber gaya visual.
- Baca keempat file sampai selesai sebelum mulai. Ikuti nilai design tokens **persis**, tanpa mengubah nilainya (kecuali keputusan #2 di bagian 0).
- Semua teks UI memakai copy dari tabel copywriting di dokumen DESIGN. Teks yang tidak ada di tabel tulis sendiri dalam Bahasa Indonesia.

---

## 3. Konteks

- **Bahasa:** Indonesia.
- **Brand:** BILLINGIN.
- **Gaya:** dark modern, bersih, terpercaya seperti produk fintech. Banyak ruang kosong, kartu berlapis di atas latar gelap (versi terang: kartu putih di atas latar lembut), ramah untuk pengguna awam.
- **Audiens:** (1) pemilik usaha WiFi/RTRW Net, lebih teknis dan berorientasi bisnis; (2) pelanggan WiFi, awam dan mengakses dari HP.

---

## 4. Yang harus dibuat (urut prioritas)

### Bagian A. Landing page marketing (desktop 1280px dan mobile responsive)

14 section sesuai dokumen DESIGN bagian 4:

1. Navbar
2. Hero
3. Bukti singkat
4. Kenapa memilih kami
5. Fitur
6. Cara kerja
7. Showcase web pembayaran
8. Voucher
9. Aplikasi pelanggan
10. Harga + kalkulator interaktif
11. Testimoni
12. FAQ
13. CTA penutup
14. Footer

Kalkulator harga:
- Wajib berupa **slider** yang bisa digeser, dan **total berubah langsung**.
- Angka tarif diambil dari BRD bagian 10 sebagai contoh. Beri label bahwa itu contoh.

### Bagian B. Website pembayaran pelanggan (mobile dulu, lalu desktop 1280px)

Susunan layar dan wireframe mengikuti dokumen DESIGN bagian 5 dan dokumen `01`. Delapan layar:

1. **Cek tagihan (landing):** kartu di tengah, input nomor internet, tombol "Cek Tagihan", blok daftar pemasangan, badge Play Store/App Store, footer promo & kontak.
2. **Detail tagihan:** status badge, rincian biaya, total, tombol "Bayar Sekarang".
3. **Pilih metode bayar:** QRIS, Virtual Account, e-wallet, minimarket, transfer manual.
4. **Instruksi bayar:** varian QRIS dengan timer, varian VA dengan tombol salin.
5. **Pembayaran berhasil + struk.**
6. **Halaman isolir:** tunggakan + tombol bayar.
7. **Pendaftaran pelanggan baru:** stepper 3 langkah.
8. **Bantuan/FAQ.**

### Bagian C. Dashboard mitra (pemilik ISP)

Sesuai dokumen DESIGN bagian 7. Empat halaman:

1. Ringkasan
2. Daftar pelanggan
3. Tagihan
4. Pembayaran

---

## 5. State yang wajib ada (khususnya Bagian B)

Loading (skeleton) · kosong · nomor tidak ditemukan · error server · kedaluwarsa · lunas · belum lunas · terisolir.

Untuk Bagian A dan C, sediakan minimal state loading, kosong, dan error pada tabel/daftar utama.

---

## 6. Aturan desain (berlaku untuk semua bagian)

**Token & komponen**
- Pakai palet warna, tipografi, radius, spasi, bayangan, dan komponen **persis** seperti dokumen DESIGN (bagian 3).
- Satu warna aksen saja sesuai keputusan #2, dipakai konsisten untuk tombol utama, tautan, dan status aktif.
- Tema warna **mudah diganti lewat token** (CSS variables), tanpa warna yang ditulis langsung di komponen.
- Mode gelap dan terang sesuai keputusan #3. Kedua mode wajib lolos kontras AA.

**Komponen konsisten:** button, input, card, badge, stepper, accordion, toast, tabel.

**Aksesibilitas & responsif**
- Kontras teks minimal **AA (4.5:1)**.
- Tinggi tombol minimal 48px (keputusan #5).
- Layout fluid dan responsive (keputusan #4): uji di 320px, 360px, 390px, 412px, 430px, lalu tablet dan desktop. Tanpa scroll horizontal, teks tidak terpotong, area sentuh tetap 48px.

**Konten**
- Testimoni dan angka statistik diberi label **"contoh"** karena belum ada data nyata.
- Data pribadi pelanggan (nama, nomor, alamat) **selalu ditampilkan dengan masking**.

---

## 7. Larangan

- Jangan memakai logo, nama, atau gambar milik pihak lain. Buat ilustrasi dan ikon sendiri.
- Jangan menyalin logo, merek, atau tampilan persis dari bayarwifi.com atau situs pesaing mana pun. Pakai logo dan identitas BILLINGIN buatan sendiri.
- Jangan menambah layar atau fitur di luar dokumen.
- Jangan mengubah nilai design tokens.

---

## 8. Cara kerja & hasil akhir

**Sebelum mulai:** jika ada hal penting yang belum jelas atau ada konflik antar dokumen, ajukan pertanyaan **sekali di awal** dalam satu daftar singkat. Jika tidak ada, langsung kerjakan.

**Urutan kerja:** A (landing page) → B (pembayaran pelanggan) → C (dashboard mitra). Selesaikan satu bagian sebelum lanjut.

**Keluaran:**
- Satu file desain per layar, dengan komponen yang sama antar file, dan navigasi antar layar bisa diklik.
- Mode gelap/terang bisa dipindah dari prototipe, dan tampilan bisa dicoba pada beberapa lebar layar HP.

**Setelah selesai, laporkan:**
1. Keputusan desain yang kamu ambil, beserta alasannya.
2. Bagian dokumen yang **belum bisa dipenuhi**, atau asumsi yang kamu pakai.
3. Daftar konflik antar dokumen yang kamu temukan dan cara menyelesaikannya.
