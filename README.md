# SIMPEG-KPI — Web Admin (Laravel 12)

Aplikasi web admin Sistem Informasi Kepegawaian untuk KPI Pusat, dibangun sesuai PRD SIMPEG-KPI (modul mobile menyusul). Berisi 4 modul inti:

1. **Data Pegawai** — CRUD data induk pegawai, unit kerja, jabatan, dokumen (KTP/SK), atasan langsung.
2. **Pendidikan & Pelatihan (Diklat)** — riwayat pendidikan, riwayat pelatihan + alur verifikasi, rekap capaian JP per pegawai vs target tahunan.
3. **Absensi** — monitoring rekap harian, absensi manual (jalur cadangan bila GPS/selfie mobile bermasalah).
4. **Cuti & Izin** — pengajuan cuti dengan alur persetujuan dua tahap (Atasan → HR/Admin), saldo cuti otomatis berkurang saat disetujui HR.

Ditambah: dashboard ringkasan, laporan ekspor Excel/PDF, pengaturan sistem (jam kerja, radius GPS, kuota cuti, target JP), master data unit/jabatan, pengajuan perubahan data mandiri (untuk nanti disambungkan ke mobile), dan audit log aktivitas.

## Peran & Akses

| Role | Akses Web |
|---|---|
| **admin** (HR/Kepegawaian) | Semua modul: kelola data pegawai, verifikasi diklat, absensi manual, approval cuti tahap final, laporan, pengaturan |
| **atasan** | Dashboard, lihat profil & rekap anggota tim, approval cuti tahap 1, lihat absensi tim |
| **pegawai** | Belum ada tampilan web (akses via mobile app — menyusul) |

## Prasyarat

- PHP >= 8.2 dengan ekstensi umum (mbstring, pdo_sqlite/pdo_mysql, fileinfo, gd)
- Composer 2.x
- Node.js >= 18 & npm
- Database: SQLite (default, langsung jalan tanpa setup) atau MySQL (untuk produksi, sesuai PRD)

## Instalasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# Jika pakai SQLite (default, paling cepat untuk development):
touch database/database.sqlite

# Jika ingin pakai MySQL sesuai target produksi PRD, edit .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=simpeg_kpi
# DB_USERNAME=root
# DB_PASSWORD=

php artisan migrate --seed
php artisan storage:link

npm run build
# atau untuk development: npm run dev (di terminal terpisah)

php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Akun Demo (dari seeder)

| Role | Email | Password |
|---|---|---|
| Admin/HR | admin@kpi.go.id | password |
| Atasan | atasan@kpi.go.id | password |
| Pegawai (mobile, belum ada UI web) | pegawai@kpi.go.id | password |

Seeder juga membuat 5 unit kerja, 5 jabatan, 6 pegawai anggota tim beserta contoh riwayat pendidikan, pelatihan, dan absensi 5 hari terakhir — supaya dashboard & tabel langsung terisi data saat pertama dibuka.

## Struktur Modul (ringkas)

- `app/Models` — Pegawai, UnitKerja, Jabatan, RiwayatPendidikan, RiwayatPelatihan, Absensi, Cuti, SaldoCuti, Pengaturan, AuditLog, PengajuanPerubahanData
- `app/Http/Controllers` — satu controller per modul, dipisah dari logic approval cuti dua tahap yang ada di `Cuti` model (`setujuiAtasan`, `setujuiHr`, dst.)
- `app/Http/Middleware/EnsureUserHasRole.php` — middleware `role:admin,atasan` untuk proteksi rute
- `app/Exports` — kelas export Excel (Maatwebsite\Excel) untuk laporan pegawai/absensi/cuti
- `resources/views` — Blade + Tailwind CSS v4 + Alpine.js, dark mode via toggle (tersimpan di localStorage), palet warna identitas KPI Pusat didefinisikan di `resources/css/app.css` (`@theme`)

## Catatan Penting

- **Absensi & Cuti mobile** (GPS/selfie, pengajuan dari HP) belum dibangun — modul ini baru menyediakan sisi **admin/monitoring** di web sesuai permintaan Anda. Endpoint API untuk Flutter (Sanctum) bisa ditambahkan berikutnya saat mobile digarap.
- Kolom `role` pada tabel `users` sudah disiapkan (`admin`, `atasan`, `pegawai`) supaya API mobile nanti tinggal pakai model & tabel yang sama.
- Upload file (foto, KTP, SK, ijazah, sertifikat) tersimpan di disk `public` — jangan lupa `php artisan storage:link` setelah install, kalau tidak file tidak akan bisa diakses lewat browser.
- Laporan Excel/PDF pakai `maatwebsite/excel` dan `barryvdh/laravel-dompdf` — sudah didaftarkan di `composer.json`, otomatis terpasang saat `composer install`.
- File migration `0001_01_01_000001a_create_sessions_table.php` sengaja ditambahkan karena `SESSION_DRIVER=database` di `.env.example`.

## Langkah Lanjutan yang Disarankan

1. Jalankan `composer install && npm install && php artisan migrate --seed` lalu cek semua modul jalan normal di browser Anda.
2. Sesuaikan data master (unit kerja, jabatan) lewat menu **Pengaturan** sesuai struktur organisasi KPI Pusat sebenarnya.
3. Ganti akun demo dengan akun asli, hapus/nonaktifkan akun seeder sebelum dipakai produksi.
4. Saat siap ke tahap mobile, tambahkan `laravel/sanctum` routes API (`routes/api.php`) untuk endpoint Flutter (absensi GPS/selfie, cuti self-service, lihat slip data).
