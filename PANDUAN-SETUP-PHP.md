# Panduan Setup SIGAP — Versi PHP + MySQL (Tahap 1 MVP)

Beda dari versi sebelumnya (Supabase), versi ini **dirender di server** — jadi begitu halaman kebuka, isinya udah lengkap. Nggak ada lagi drama "nunggu skrip JavaScript dari CDN".

Konsekuensinya: file PHP **tidak bisa dibuka langsung dari file manager HP** kayak file HTML biasa. Harus dijalankan lewat server (di sini kita pakai Railway).

## Struktur File

```
public/
  config.php          <- koneksi database & helper login
  includes/
    header.php        <- sidebar & tampilan bersama
    footer.php
  register.php         <- daftar akun
  login.php            <- masuk
  logout.php
  index.php            <- Data Karyawan (halaman utama setelah login)
  pola_dinas.php        <- Pola Dinas
  roster.php            <- Tugaskan & lihat roster
schema-mysql.sql        <- perintah bikin tabel database
nixpacks.toml            <- konfigurasi supaya Railway tau cara jalanin PHP
PANDUAN-SETUP-PHP.md     <- file ini
```

---

## Langkah 1 — Upload ke GitHub

1. Di repo GitHub kamu (`zulfaeuis1326/MPE-system`), **hapus semua file lama** (index.html, schema.sql, dll dari versi Supabase) supaya nggak campur aduk.
2. Upload **seluruh isi folder** project PHP ini ke repo — pastikan strukturnya persis kayak di atas (folder `public/` harus tetap jadi folder, jangan di-flatten).

## Langkah 2 — Tambah Database MySQL di Railway

1. Buka project Railway kamu (`MPE-system`).
2. Klik **"+ New"** atau **"Create"** → pilih **"Database"** → **"Add MySQL"**.
3. Railway otomatis bikin database dan nyediain kredensialnya — kamu **tidak perlu isi apa pun secara manual**, karena `config.php` sudah otomatis membaca variabel `MYSQLHOST`, `MYSQLUSER`, dll yang disediakan Railway.
4. Klik service MySQL yang baru dibuat → tab **"Variables"** → pastikan ada variabel seperti `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`.

## Langkah 3 — Buat Tabel Database

1. Di service MySQL Railway, buka tab **"Data"** (atau **"Query"**) — ada editor SQL bawaan Railway.
2. Copy semua isi `schema-mysql.sql`, paste, jalankan (Run).
3. Pastikan 4 tabel muncul: `users`, `karyawan`, `pola_dinas`, `penugasan_roster`.

*(Kalau Railway versi kamu tidak punya editor SQL bawaan, kamu bisa pakai aplikasi seperti Adminer/phpMyAdmin, atau tanya aku lagi — ada cara lain.)*

## Langkah 4 — Deploy Service PHP

1. Kembali ke project Railway, pastikan service `MPE-system` (yang terhubung ke GitHub) otomatis re-deploy setelah kamu upload file PHP tadi (Railway biasanya auto-deploy tiap ada push baru).
2. Buka service itu → tab **"Settings"** → cari **"Networking"** → klik **"Generate Domain"** kalau belum ada domain publik.
3. Buka domain yang dikasih Railway (formatnya `https://nama-project.up.railway.app`).

## Langkah 5 — Hubungkan Service PHP ke Database

Ini penting: service PHP kamu perlu tau kredensial database MySQL tadi.

1. Di service PHP (`MPE-system`), buka tab **"Variables"**.
2. Klik **"+ New Variable"** → pilih opsi **"Add Reference"** (bukan ketik manual) → pilih service MySQL kamu → pilih semua variabel yang tersedia (`MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`).
3. Railway otomatis nyambungin variabel itu ke service PHP kamu. Tunggu re-deploy selesai.

## Langkah 6 — Coba Pakai

1. Buka domain Railway kamu.
2. Karena belum ada halaman depan, langsung tambahkan `/register.php` di akhir URL (misal `https://nama-project.up.railway.app/register.php`).
3. Daftar akun pertama (otomatis jadi Manpower/super_admin).
4. Login, coba tambah karyawan, bikin pola dinas, tugaskan ke roster.

---

## Catatan

- Kalau ada halaman muncul **"Gagal konek ke database"**, itu paling sering karena Langkah 5 (hubungkan variabel) belum dilakukan atau belum selesai re-deploy. Cek lagi tab Variables di service PHP.
- Akun kedua dan seterusnya yang daftar otomatis jadi role `admin`. Untuk mengubah jadi `super_admin`, edit langsung di tabel `users` lewat editor database Railway (kolom `role`).
- Ini baru **Tahap 1**. Fitur cuti, induksi, audit log, beranda publik, dan fleet matching menyusul di tahap berikutnya sesuai rencana MVP yang sudah kita susun.
