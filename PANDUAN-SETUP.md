# Panduan Setup SIGAP (Tahap 1 — MVP)

Aplikasi ini terdiri dari 3 file:
- `index.html` — seluruh aplikasi (login, data karyawan, pola dinas, roster)
- `schema.sql` — perintah untuk membuat tabel-tabel database
- `PANDUAN-SETUP.md` — file ini

Karena pakai Supabase, kamu **tidak perlu menjalankan server apa pun** — cukup buka `index.html` di browser (bahkan dari HP) setelah konfigurasi selesai.

---

## Langkah 1 — Buat Project Supabase

1. Buka [supabase.com](https://supabase.com) dari browser HP, daftar/login (bisa pakai akun GitHub).
2. Klik **"New Project"**.
3. Isi nama project (mis. `sigap-tambang`), buat password database (simpan baik-baik), pilih region terdekat (Singapore paling dekat ke Indonesia).
4. Tunggu beberapa menit sampai project selesai dibuat.

## Langkah 2 — Buat Tabel Database

1. Di dashboard Supabase, buka menu **SQL Editor** (ikon di sidebar kiri).
2. Klik **"New Query"**.
3. Buka file `schema.sql` yang aku buatkan, **copy semua isinya**, paste ke SQL Editor.
4. Klik **"Run"**. Kalau berhasil, akan muncul pesan sukses dan tabel-tabel baru muncul di menu **Table Editor**.

## Langkah 3 — Ambil Kunci API

1. Di dashboard Supabase, buka **Project Settings** (ikon gerigi) → **API**.
2. Salin dua nilai ini:
   - **Project URL** (contoh: `https://xxxxx.supabase.co`)
   - **anon public key** (kode panjang di bagian "Project API keys")

## Langkah 4 — Masukkan ke index.html

1. Buka file `index.html` pakai editor teks apa saja (bisa langsung dari GitHub — klik file, klik ikon pensil untuk edit).
2. Cari bagian ini di dekat akhir file:
   ```js
   const SUPABASE_URL = "GANTI_DENGAN_SUPABASE_URL_KAMU";
   const SUPABASE_ANON_KEY = "GANTI_DENGAN_SUPABASE_ANON_KEY_KAMU";
   ```
3. Ganti dengan nilai yang kamu salin tadi, misalnya:
   ```js
   const SUPABASE_URL = "https://xxxxx.supabase.co";
   const SUPABASE_ANON_KEY = "eyJhbGciOiJI...(kode panjang)...";
   ```
4. Simpan file.

## Langkah 5 — Upload ke GitHub & Publikasikan

Karena ini murni file statis (HTML+JS), cara **paling simpel** untuk publikasi adalah **GitHub Pages** (gratis, tanpa perlu Railway untuk aplikasi tahap ini):

1. Bikin repo baru di GitHub, upload ketiga file (`index.html`, `schema.sql`, `PANDUAN-SETUP.md`).
2. Di repo, buka **Settings** → **Pages**.
3. Di bagian **Source**, pilih branch `main` dan folder `/ (root)`, klik **Save**.
4. Tunggu 1-2 menit, GitHub akan kasih link seperti `https://namakamu.github.io/nama-repo/` — itu link aplikasi kamu yang sudah live, bisa dibuka dari HP mana saja.

*(Railway tetap bisa dipakai nanti kalau di tahap berikutnya kamu butuh backend server sendiri — tapi untuk Tahap 1 ini, GitHub Pages sudah cukup dan lebih simpel.)*

## Langkah 6 — Coba Pakai

1. Buka link aplikasi kamu.
2. Klik **"Daftar di sini"**, buat akun pertama (ini otomatis jadi akun admin).
3. Login, lalu coba tambah data karyawan, bikin pola dinas (contoh: nama "13 Kerja 1 Off", panjang siklus `70`, definisi siklus `P,P,P,P,P,P,P,P,P,P,P,P,P,L`), lalu tugaskan karyawan ke pola itu di menu **Lihat Roster**.

---

## Catatan Penting

- Akun pertama yang daftar otomatis dapat role `admin` di tabel `profiles`. Untuk menjadikan seseorang **Manpower (super_admin)**, buka **Table Editor → profiles** di Supabase, cari barisnya, ubah kolom `role` jadi `super_admin` secara manual (fitur ubah role dari dalam aplikasi belum ada di Tahap 1 ini, menyusul di tahap berikutnya).
- Ini baru **Tahap 1**: data karyawan, pola dinas, dan roster dasar. Fitur cuti, induksi, audit log, dashboard publik, dan fleet matching menyusul di tahap berikutnya sesuai rencana MVP yang sudah kita susun.
- Kalau ada error saat coba pakai, biasanya karena `SUPABASE_URL`/`SUPABASE_ANON_KEY` salah ketik, atau `schema.sql` belum di-run. Cek dua hal itu dulu.
