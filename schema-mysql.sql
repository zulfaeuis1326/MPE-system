-- ============================================================
-- SIGAP — Skema Database MySQL (Tahap 1 MVP)
-- Jalankan file ini di database Railway kamu (lihat PANDUAN-SETUP-PHP.md)
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('super_admin','admin') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS karyawan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(255) NOT NULL,
  nip VARCHAR(100),
  jabatan VARCHAR(255),
  status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pola_dinas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_pola VARCHAR(255) NOT NULL,
  panjang_siklus INT NOT NULL,
  definisi_siklus TEXT NOT NULL, -- contoh isi: P,P,P,P,P,P,P,P,P,P,P,P,P,L
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS penugasan_roster (
  id INT AUTO_INCREMENT PRIMARY KEY,
  karyawan_id INT NOT NULL,
  pola_dinas_id INT NOT NULL,
  tanggal_mulai_siklus DATE NOT NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (karyawan_id) REFERENCES karyawan(id) ON DELETE CASCADE,
  FOREIGN KEY (pola_dinas_id) REFERENCES pola_dinas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
