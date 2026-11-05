# ♻️ Waste Bank Management System

<h3 align="center">🌱 KPP Bina Lindung — Zero Waste</h3>

<p align="center">
  Sistem Informasi Pengelolaan Bank Sampah Berbasis Web
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-Framework-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-Backend-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-Styling-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" />
</p>

---

## 🌍 Tentang Project

**Waste Bank Management System** merupakan aplikasi berbasis web yang dikembangkan untuk membantu pengelolaan aktivitas bank sampah di lingkungan RW secara digital, terstruktur, dan terintegrasi.

Aplikasi ini bertujuan mempermudah warga dalam melakukan pengajuan penjualan sampah serta membantu pengelola dalam mengelola data warga, transaksi, penimbangan, dan keuangan.

Setiap akun warga terhubung dengan identitas rumah tangga, sehingga seluruh aktivitas penjualan sampah dapat tercatat berdasarkan pemilik rumah, alamat, blok, nomor rumah, dan RT.

## 🎯 Tujuan Pengembangan

- ♻️ Mendukung pengelolaan sampah berbasis digital.
- 🏡 Mempermudah warga dalam melakukan penjualan sampah.
- 📊 Meningkatkan transparansi pencatatan transaksi.
- 💰 Mempermudah pengelolaan saldo dan keuangan warga.
- 🔔 Memberikan informasi mengenai perkembangan pengajuan dan transaksi.
- 🌱 Mendukung terciptanya lingkungan yang lebih bersih dan berkelanjutan.

## ✨ Fitur Utama

### 👤 User (Warga)

- 🏠 **Dashboard** — Menampilkan ringkasan saldo dan aktivitas bank sampah.
- ♻️ **Jual Sampah** — Mengajukan penjualan sampah melalui formulir pengajuan.
- 📋 **Riwayat** — Melihat riwayat pengajuan dan transaksi penjualan sampah.
- 💰 **Keuangan** — Memantau saldo, pemasukan, dan riwayat pencairan.
- 🔔 **Notifikasi** — Menerima informasi terkait status pengajuan dan transaksi.
- 👤 **Profil** — Mengelola informasi akun dan identitas rumah tangga.

### 🛠️ Admin (Pengelola)

- 📊 **Dashboard** — Menampilkan ringkasan aktivitas bank sampah.
- 🏡 **Data Warga** — Mengelola informasi dan akun warga.
- 📥 **Pengajuan Sampah** — Memeriksa dan mengelola pengajuan penjualan sampah.
- ⚖️ **Transaksi** — Mengelola hasil penimbangan dan perhitungan nilai sampah.
- 💵 **Keuangan** — Memantau saldo dan mengelola pencairan warga.
- 🔔 **Notifikasi** — Mengelola pemberitahuan kepada warga.
- 📑 **Laporan** — Menampilkan rekapitulasi sampah dan transaksi.
- ⚙️ **Pengaturan** — Mengelola jenis sampah dan harga per kilogram.

> 🚧 Fitur aplikasi dikembangkan secara bertahap dan dapat mengalami penyesuaian selama proses pengembangan.

## 💻 Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| 🐘 PHP | Bahasa pemrograman backend |
| 🚀 Laravel | Framework aplikasi |
| 🎨 Blade | Template engine |
| 🌿 Tailwind CSS | Styling dan responsive layout |
| 🗄️ MySQL | Database management |
| ⚡ Vite | Asset bundling |

## 📂 Struktur Project

```text
waste-bank-management-system/
│
├── app/
│   ├── Http/
│   ├── Models/
│   └── Providers/
│
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
│
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│
├── routes/
│   ├── web.php
│   └── console.php
│
├── storage/
├── tests/
├── artisan
├── composer.json
└── package.json
```

## ⚙️ Instalasi dan Konfigurasi

### 1. Clone Repository

```bash
git clone https://github.com/Rifaaaiz/waste-bank-management-system.git
```

### 2. Masuk ke Folder Project

```bash
cd waste-bank-management-system
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Salin File Environment

```bash
cp .env.example .env
```

Untuk Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Konfigurasi Database

Buka file `.env`, kemudian sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=waste_bank
DB_USERNAME=root
DB_PASSWORD=
```

Pastikan database `waste_bank` sudah dibuat di MySQL.

### 7. Jalankan Migration

```bash
php artisan migrate
```

### 8. Install Frontend Dependencies

```bash
npm install
```

### 9. Jalankan Aplikasi

Buka dua terminal:

Terminal pertama:

```bash
php artisan serve
```

Terminal kedua:

```bash
npm run dev
```

Akses aplikasi melalui:

```text
http://127.0.0.1:8000
```

## 🌱 Konsep Sistem

Aplikasi ini menggunakan konsep satu rumah tangga dengan satu akun utama. Setiap transaksi penjualan sampah dikaitkan dengan identitas rumah tangga yang terdaftar.

Alur transaksi:

```text
👤 Warga
   │
   ▼
📝 Mengajukan Penjualan Sampah
   │
   ▼
🛠️ Admin Memeriksa Pengajuan
   │
   ▼
⚖️ Penimbangan Sampah
   │
   ▼
💰 Perhitungan Nilai Sampah
   │
   ▼
✅ Konfirmasi Transaksi
   │
   ▼
🏦 Saldo Warga Bertambah
```

## 🚀 Development Status

🟡 **Status: In Development**

Project ini masih dalam tahap pengembangan dan penyempurnaan fitur.

## 👨‍💻 Developer

**Rifaaaiz**

GitHub: [@Rifaaaiz](https://github.com/Rifaaaiz)

---

<p align="center">
  ♻️ <b>Waste Less, Live More.</b> 🌱
  <br/>
  <i>Small Actions, Big Impact for a Greener Environment.</i>
</p>
