# 🚀 SIMPEG-KPI — Web Admin & API Backend

Sistem Informasi Kepegawaian & KPI (SIMPEG-KPI) berbasis **Laravel 12** yang dirancang khusus untuk manajemen sumber daya manusia, pengawasan absensi, tata kelola cuti, rekapitulasi pelatihan (diklat), serta penyedia RESTful API backend untuk aplikasi **SIMPEG-KPI Mobile (Flutter)**.

---

## 📋 Daftar Isi
1. [Tentang Sistem](#-tentang-sistem)
2. [🧰 Tech Stack](#-tech-stack)
   - [Backend & Framework](#backend--framework)
   - [Frontend & UI/UX](#frontend--uiux)
   - [Database & Storage](#database--storage)
   - [Library & Integrasi Utama](#library--integrasi-utama)
3. [✨ Fitur Utama Web Admin](#-fitur-utama-web-admin)
4. [📲 Integrasi API Backend untuk Aplikasi Mobile](#-integrasi-api-backend-untuk-aplikasi-mobile)
5. [👥 Hak Akses & Matriks Peran (RBAC)](#-hak-akses--matriks-peran-rbac)
6. [📂 Struktur Direktori Proyek](#-struktur-direktori-proyek)
7. [⚡ Panduan Instalasi & Persiapan](#-panduan-instalasi--persiapan)
8. [🔑 Akun Demo (Default Seeders)](#-akun-demo-default-seeders)
9. [🛠 Perintah Pengembangan (Scripts)](#-perintah-pengembangan-scripts)

---

## ℹ️ Tentang Sistem

**SIMPEG-KPI Web Admin** berfungsi sebagai pusat kendali (*command center*) bagi Pengelola Kepegawaian (HR/Admin) dan Atasan/Kepala Unit. Web admin ini memfasilitasi validasi data kepegawaian, verifikasi sertifikat pelatihan, kelayakan approval cuti berjenjang, pemantauan jadwal shift absensi, serta penerbitan laporan resmi berbasis dokumen Excel dan PDF.

Selain antarmuka berbasis web, repositori ini juga berfungsi sebagai **API Provider Gateway** bagi aplikasi Android/iOS (**SIMPEG-KPI Mobile**) yang digunakan pegawai untuk absensi berbasis GPS & Selfie (Geofencing), pengajuan cuti mandiri, dan monitoring jam pelajaran (JP) pelatihan.

---

## 🧰 Tech Stack

### Backend & Framework
* **PHP**: `>= 8.2`
* **Framework**: [Laravel 12.0](https://laravel.com)
* **API Authentication**: [Laravel Sanctum 4.0](https://laravel.com/docs/sanctum) (Token-based Auth untuk Flutter Mobile)
* **CLI & Debugging**: Laravel Pail, Laravel Tinker, Laravel Pint (Code Style Formatter)

### Frontend & UI/UX
* **Template Engine**: Laravel Blade (Server-Side Rendering)
* **Styling**: [Tailwind CSS v4.0](https://tailwindcss.com) (Utility-first CSS, Dark Mode via LocalStorage)
* **Reactivity**: [Alpine.js 3.14](https://alpinejs.dev) (Interaktivitas UI & Komponen Dynamic Modal/Dropdown)
* **Charts & Data Visualization**: [Chart.js 4.5](https://www.chartjs.org) (Dashboard Analytics & Rekomendasi Cuti)
* **Build Tool**: [Vite 7.0](https://vitejs.dev) & `@tailwindcss/vite`

### Database & Storage
* **Database Engine**:
  * **SQLite** (Default untuk lingkungan pengujian & lokal development)
  * **MySQL / MariaDB** (Target untuk lingkungan produksi / enterprise deployment)
* **Session & Cache**: Database Driver / Redis Support
* **File Storage**: Laravel Disk Storage (`storage/app/public` terhubung ke `public/storage`) untuk dokumen KTP, SK, ijazah, selfie absensi, dan sertifikat diklat.

### Library & Integrasi Utama
* **Export PDF**: [`barryvdh/laravel-dompdf v3.1`](https://github.com/barryvdh/laravel-dompdf) (Cetak laporan kepegawaian, absensi, dan cuti format PDF)
* **Export & Import Excel**: [`maatwebsite/excel v3.1`](https://laravel-excel.com) (Ekspor laporan & Impor matriks jadwal shift pegawai)
* **Push Notifications Engine**: [`kreait/firebase-php v8.3`](https://github.com/kreait/firebase-php) (Integrasi Firebase Cloud Messaging / FCM untuk notifikasi instan ke aplikasi mobile)

---

## ✨ Fitur Utama Web Admin

### 1. 👨‍💼 Modul Manajemen Data Pegawai & Organisasi
* **CRUD Data Induk**: Pengelolaan NIP, nama, email, unit kerja, jabatan, atasan langsung, dan dokumen pendukung (KTP/SK).
* **Manajemen User & Akses**: Pembuatan akun pengguna, reset password, dan status aktif/non-aktif akun.
* **Hierarki Atasan-Bawahan**: Pemetaan atasan langsung untuk struktur alur approval dua tahap.

### 2. 🎓 Modul Pendidikan & Pelatihan (Diklat)
* **Riwayat Pendidikan**: Pengelolaan jenjang pendidikan, nama institusi, dan tahun kelulusan.
* **Riwayat Pelatihan & Capaian JP**: Pencatatan bentuk pelatihan, jenis kursus, jam pelajaran (JP), dan target tahunan.
* **Alur Verifikasi Sertifikat**: HR/Admin melakukan verifikasi atas pengajuan pelatihan yang diunggah pegawai.

### 3. 🕒 Modul Absensi & Shift Kerja
* **Monitoring Absensi Harian**: Pemantauan waktu masuk, waktu keluar, foto selfie, koordinat GPS, dan status (Hadir, Terlambat, Pulang Cepat, Alpha).
* **Input Absensi Manual**: Cadangan khusus HR/Admin bila GPS atau perangkat pegawai bermasalah.
* **Kelola Matrix Shift**: Pengaturan jadwal shift per periode, fitur *inline cell edit*, serta fitur impor data shift massal.

### 4. 🏖️ Modul Cuti & Ketidakhadiran
* **Alur Approval 2 Tahap**:
  1. *Tahap 1*: Persetujuan / Penolakan oleh **Atasan Langsung**.
  2. *Tahap 2*: Persetujuan final / Penolakan oleh **HR / Admin**.
* **Potong Saldo Otomatis**: Saldo cuti tahunan berkurang secara otomatis begitu disetujui HR.
* **Kalender & Analytics Cuti**: Visualisasi distribusi cuti tim dan rekomendasi analisis penumpukan jadwal cuti.

### 5. 📑 Modul Pengajuan Perubahan Data Mandiri
* Verifikasi & persetujuan perubahan profil/dokumen mandiri yang diajukan oleh pegawai via mobile app.

### 6. 📊 Modul Ekspor Laporan
* Ekspor data terfilter (Pegawai, Rekap Absensi, Rekap Cuti, Ketidakhadiran) ke format **Excel (.xlsx)** dan **PDF (.pdf)**.

### 7. ⚙️ Pengaturan Sistem & Audit Log
* Pengaturan parameter global: radius validasi GPS absensi mobile, jam kerja normal, kuota cuti default, master data unit & jabatan.
* Record **Audit Log** untuk merekam jejak aktivitas penting pengguna di sistem.

---

## 📲 Integrasi API Backend untuk Aplikasi Mobile

Sistem ini menyediakan RESTful API berbasis token **Laravel Sanctum** untuk aplikasi mobile (Flutter).

| Modul API | Endpoint | Method | Fungsi |
|---|---|---|---|
| **Auth** | `/api/login` | `POST` | Login pegawai/atasan & penerbitan token Sanctum |
| **Auth** | `/api/me` | `GET` | Ambil data profil pengguna yang sedang login |
| **Auth** | `/api/fcm-token` | `POST` | Registrasi token FCM perangkat untuk push notification |
| **Dashboard** | `/api/dashboard` | `GET` | Ringkasan statistik harian, saldo cuti, & status absensi |
| **Absensi** | `/api/absensi/masuk` | `POST` | Check-in absensi (GPS + Selfie photo upload) |
| **Absensi** | `/api/absensi/keluar` | `POST` | Check-out absensi |
| **Cuti** | `/api/cuti` | `GET` / `POST` | Lihat daftar & pengajuan cuti baru dari mobile |
| **Approval (Atasan)** | `/api/atasan/cuti-tim` | `GET` | Daftar pengajuan cuti bawahan untuk atasan |
| **Approval (Atasan)** | `/api/atasan/cuti/{id}/setujui` | `PATCH` | Approval cuti tahap 1 oleh atasan via mobile |
| **Riwayat** | `/api/riwayat/pelatihan` | `GET` / `POST` | Lihat & ajukan riwayat diklat mandiri |
| **Shift** | `/api/jadwal-shift/hari-ini` | `GET` | Cek jadwal shift kerja pegawai hari ini |

---

## 👥 Hak Akses & Matriks Peran (RBAC)

Aplikasi mengimplementasikan Middleware `EnsureUserHasRole` (`role:admin,atasan,pegawai`):

| Fitur / Modul | Admin (HR) | Atasan | Pegawai |
|---|:---:|:---:|:---:|
| **Dashboard Admin/Statistik** | ✅ | ✅ | ❌ (Via Mobile App) |
| **Kelola Data Pegawai & Master** | ✅ | ❌ | ❌ |
| **Verifikasi Sertifikat Diklat** | ✅ | ❌ | ❌ |
| **Approval Cuti Tahap 1 (Atasan)** | ✅ | ✅ | ❌ |
| **Approval Cuti Tahap 2 (HR Final)** | ✅ | ❌ | ❌ |
| **Input Absensi Manual Cadangan** | ✅ | ❌ | ❌ |
| **Kelola Shift & Import Data** | ✅ | ❌ | ❌ |
| **Ekspor Laporan Excel & PDF** | ✅ | ❌ | ❌ |
| **Absensi GPS & Selfie** | ❌ | ✅ (Via Mobile) | ✅ (Via Mobile) |

---

## 📂 Struktur Direktori Proyek

```text
simpeg-kpi-web/
├── app/
│   ├── Exports/               # Kelas ekspor data Excel (Maatwebsite Excel)
│   ├── Http/
│   │   ├── Controllers/       # Controller Web Admin & API
│   │   │   └── Api/           # REST API Controllers (Sanctum Auth)
│   │   └── Middleware/        # Middleware RBAC (EnsureUserHasRole)
│   ├── Models/                # Model Eloquent (Pegawai, Absensi, Cuti, Pelatihan, dll.)
│   └── Services/              # Business logic & Firebase Service
├── config/                    # Konfigurasi aplikasi, Sanctum, Firebase, & DomPDF
├── database/
│   ├── migrations/            # Skema tabel database
│   └── seeders/               # Data dummy awal (Admin, Atasan, Master Data)
├── public/                    # Entry point aplikasi & asset terpublikasi
├── resources/
│   ├── css/                   # app.css (Tailwind CSS v4 setup & theme)
│   ├── js/                    # app.js (Alpine.js & Chart.js setup)
│   └── views/                 # Blade templates UI Web Admin
├── routes/
│   ├── web.php                # Rute aplikasi Web Admin
│   └── api.php                # Rute API Backend (Sanctum Auth)
└── vite.config.js             # Konfigurasi Vite bundler
```

---

## ⚡ Panduan Instalasi & Persiapan

### 1. Kebutuhan Perangkat Lunak
* PHP `>= 8.2` (dengan ekstensi `pdo`, `mbstring`, `openssl`, `gd`, `fileinfo`, `xml`)
* Composer `2.x`
* Node.js `>= 18` & npm
* Web Server (Apache/Nginx/Laragon) atau `php artisan serve`

### 2. Langkah Instalasi

1. **Clone repository & masuk ke direktori proyek**:
   ```bash
   cd c:\laragon\www\simpeg-kpi-web
   ```

2. **Install dependency PHP & Node.js**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment (`.env`)**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Persiapan Database**:
   * *Opsi A: SQLite (Default Development)*
     ```bash
     touch database/database.sqlite
     ```
   * *Opsi B: MySQL / MariaDB (Produksi)*
     Buka `.env` dan sesuaikan kredensial database:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=simpeg_kpi
     DB_USERNAME=root
     DB_PASSWORD=
     ```

5. **Jalankan Migrasi Database & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

6. **Buat Symbolic Link Storage**:
   ```bash
   php artisan storage:link
   ```

7. **Build Asset Frontend**:
   ```bash
   # Mode Development (Hot Reloading)
   npm run dev

   # Mode Production Build
   npm run build
   ```

8. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Aplikasi Web Admin dapat diakses melalui browser di: `http://127.0.0.1:8000`

---

## 🔑 Akun Demo (Default Seeders)

Setelah menjalankan `php artisan migrate --seed`, Anda dapat menggunakan akun bawaan berikut untuk login ke Web Admin:

| Role / Peran | Email | Password | Hak Akses |
|---|---|---|---|
| **Admin / HR** | `admin@kpi.go.id` | `password` | Akses Penuh (Manajemen Pegawai, Shift, Approval HR, Laporan, Pengaturan) |
| **Atasan** | `atasan@kpi.go.id` | `password` | Akses Dashboard, Rekap Tim, & Approval Cuti Tahap 1 |
| **Pegawai** | `pegawai@kpi.go.id` | `password` | Akun Demo untuk Uji Coba API Mobile (Flutter) |

---

## 🛠 Perintah Pengembangan (Scripts)

Repositori ini telah disiapkan dengan perintah utilitas di `composer.json` untuk mempercepat alur kerja pengembang:

* **Menjalankan Seluruh Server Development Sekaligus**:
  ```bash
  composer run dev
  ```
  *(Perintah ini akan menjalankan HTTP Server, Worker Queue, Pail Logging, dan Vite Dev Server secara bersamaan menggunakan `concurrently`).*

* **Menjalankan Pengujian / Unit Tests**:
  ```bash
  composer run test
  ```

* **Format Code Style (Laravel Pint)**:
  ```bash
  ./vendor/bin/pint
  ```

---
*Dikembangkan untuk Sistem Informasi Kepegawaian & KPI Pusat (SIMPEG-KPI).*
