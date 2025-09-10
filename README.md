# 🚀 Kasir App: Modern POS System

**Kasir App** adalah sebuah aplikasi Point of Sale (POS) berbasis web yang dirancang untuk memberikan pengalaman kasir yang modern, efisien, dan mulus. Dibangun dengan fondasi **Laravel 10** yang solid dan antarmuka **Tailwind CSS** yang elegan, aplikasi ini siap membantu bisnis dalam mengelola penjualan dan inventaris dengan akurat.

Tujuan utama dari proyek ini adalah untuk menunjukkan bagaimana teknologi modern dapat disatukan untuk menciptakan solusi bisnis yang kuat dan mudah digunakan.

---

## ✨ Fitur Unggulan

-   **Antarmuka Kasir Interaktif:** Pengalaman kasir yang *real-time* dan responsif untuk pencarian produk, manajemen keranjang, dan penyelesaian transaksi.
-   **Alur Pembayaran Lengkap:** Mendukung pembayaran tunai dengan perhitungan kembalian otomatis, serta pembayaran non-tunai melalui simulasi bank dan QRIS.
-   **Laporan Penjualan Visual:** Dashboard yang informatif menampilkan total pendapatan, jumlah transaksi, dan produk terlaris dengan data yang difilter berdasarkan tanggal dan disajikan dalam bentuk grafik.
-   **Manajemen Produk Komprehensif:** Kontrol penuh atas data produk dengan fitur **CRUD** (Create, Read, Update, Delete) yang efisien.
-   **Manajemen Pengguna Fleksibel:** Sistem autentikasi pengguna dengan peran (`admin` dan `kasir`) untuk membedakan hak akses dan menjaga keamanan data.
-   **Tampilan Responsif:** Desain yang disesuaikan untuk desktop dan mobile, dengan *navbar* yang elegan dan menu *hamburger* yang intuitif.

---

## ⚙️ Teknologi yang Digunakan

-   **Backend:** PHP 8.1+, Laravel 10, MySQL
-   **Frontend:** Tailwind CSS, JavaScript (Vanilla JS), Vite
-   **Library:** Maatwebsite/Excel (untuk ekspor laporan), Chart.js (untuk visualisasi data), Toastify.js (untuk notifikasi).

---

## 🚀 Cara Menjalankan Proyek Secara Lokal

Ikuti langkah-langkah di bawah ini untuk memulai proyek di lingkungan lokal Anda.

### 1. Klon Repositori
```bash
git clone [https://github.com/BekaGensss/kasir-app-laravel.git](https://github.com/BekaGensss/kasir-app-laravel.git)
cd kasir-app-laravel

2. Instalasi Dependensi
Bash
composer install
npm install

3. Konfigurasi Database
Buat file .env dari .env.example dan atur kredensial database MySQL Anda.
Bash
cp .env.example .env

4. Migrasi Database
Jalankan migrasi untuk membuat tabel yang diperlukan.
Bash
php artisan migrate

5. Jalankan Server
Buka dua terminal terpisah dan jalankan perintah berikut:
Bash

# Terminal 1: Untuk server backend
php artisan serve
Bash

# Terminal 2: Untuk server frontend
npm run dev
Aplikasi kini dapat diakses di http://127.0.0.1:8000.

🤝 Kontributor
BekaGensss

(Bara Kusuma.)
