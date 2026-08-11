-- ============================================================
-- SIGAP — Skema Database Tahap 1 (MVP)
-- Jalankan seluruh isi file ini di: Supabase Dashboard > SQL Editor > New Query > Run
-- ============================================================

-- 1. Tabel profil (menyimpan role tambahan untuk tiap akun login)
create table if not exists profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  nama text not null,
  role text not null default 'admin' check (role in ('super_admin','admin')),
  created_at timestamptz default now()
);

-- 2. Tabel karyawan
create table if not exists karyawan (
  id uuid primary key default gen_random_uuid(),
  nama text not null,
  nip text,
  jabatan text,
  status text not null default 'aktif' check (status in ('aktif','nonaktif')),
  created_at timestamptz default now()
);

-- 3. Tabel pola dinas (template roster)
create table if not exists pola_dinas (
  id uuid primary key default gen_random_uuid(),
  nama_pola text not null,
  panjang_siklus int not null,
  definisi_siklus jsonb not null, -- contoh: ["P","P","P","L","P","P","P","P","P","P","P","P","P","L"]
  created_at timestamptz default now()
);

-- 4. Tabel penugasan roster (karyawan <-> pola dinas)
create table if not exists penugasan_roster (
  id uuid primary key default gen_random_uuid(),
  karyawan_id uuid references karyawan(id) on delete cascade,
  pola_dinas_id uuid references pola_dinas(id) on delete cascade,
  tanggal_mulai_siklus date not null,
  aktif boolean not null default true,
  created_at timestamptz default now()
);

-- ============================================================
-- Keamanan (Row Level Security) — hanya user yang login yang boleh akses
-- ============================================================
alter table profiles enable row level security;
alter table karyawan enable row level security;
alter table pola_dinas enable row level security;
alter table penugasan_roster enable row level security;

-- Semua user yang login boleh baca & kelola data (disederhanakan untuk Tahap 1;
-- nanti bisa dipersempit per role di tahap berikutnya)
create policy "login boleh baca profiles" on profiles for select using (auth.uid() is not null);
create policy "user boleh baca profil sendiri utk update" on profiles for update using (auth.uid() = id);

create policy "login boleh kelola karyawan" on karyawan for all using (auth.uid() is not null) with check (auth.uid() is not null);
create policy "login boleh kelola pola_dinas" on pola_dinas for all using (auth.uid() is not null) with check (auth.uid() is not null);
create policy "login boleh kelola penugasan_roster" on penugasan_roster for all using (auth.uid() is not null) with check (auth.uid() is not null);

-- ============================================================
-- Trigger: otomatis bikin baris di profiles saat ada user baru daftar
-- ============================================================
create or replace function public.handle_new_user()
returns trigger as $$
begin
  insert into public.profiles (id, nama, role)
  values (new.id, coalesce(new.raw_user_meta_data->>'nama', new.email), 'admin');
  return new;
end;
$$ language plpgsql security definer;

drop trigger if exists on_auth_user_created on auth.users;
create trigger on_auth_user_created
  after insert on auth.users
  for each row execute procedure public.handle_new_user();
