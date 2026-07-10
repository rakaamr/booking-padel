# Booking Lapangan Padel

Website Booking Lapangan Padel merupakan aplikasi berbasis web yang dibuat untuk memenuhi tugas mata kuliah Pemrograman Web. Aplikasi ini memudahkan pengguna dalam melakukan pemesanan lapangan padel secara online tanpa harus datang langsung ke lokasi.


# Deskripsi Project

Sistem ini menyediakan layanan pemesanan lapangan padel secara online dengan dua hak akses utama, yaitu User dan Admin.

Pengguna dapat melihat daftar lapangan yang tersedia, melakukan booking, memilih metode pembayaran, mengunggah bukti pembayaran, melihat riwayat pemesanan, serta mengelola profil akun.

Admin bertugas mengelola data lapangan, data pengguna, data booking, melakukan konfirmasi pembayaran, serta melihat laporan pendapatan.


# Fitur

## User

* Registrasi akun
* Login
* Melihat daftar lapangan
* Melakukan booking lapangan
* Memilih metode pembayaran
* Upload bukti pembayaran
* Melihat riwayat booking
* Edit profil
* Logout

## Admin

* Login Admin
* Mengelola data lapangan (Tambah, Edit, Hapus)
* Mengelola data pengguna
* Mengelola data booking
* Konfirmasi pembayaran
* Menolak pembayaran
* Melihat laporan pendapatan


# Teknologi yang Digunakan

* HTML5
* CSS3
* JavaScript
* PHP Native
* Bootstrap 5
* MySQL
* XAMPP


# Struktur Folder

```text
booking_padel/
│
├── admin/
│
├── assets/
│   ├── img/
│   ├── script.js
│   └── style.css
│
├── config/
│
├── user/
│
├── Index.php
├── login.php
├── register.php
├── logout.php
└── README.md
```


# Cara Instalasi

1. Install XAMPP.
2. Jalankan Apache dan MySQL.
3. Clone repository GitHub.

```bash
git clone https://github.com/username/booking-padel.git
```

4. Pindahkan folder project ke dalam:

```text
C:\xampp\htdocs\
```

5. Import database melalui phpMyAdmin.

6. Pastikan file konfigurasi database pada:

```text
config/koneksi.php
```

sudah sesuai dengan pengaturan MySQL.

7. Jalankan Web melalui browser.

```text
http://localhost/booking_padel
```


# Database

Database menggunakan MySQL dan dijalankan melalui phpMyAdmin pada XAMPP.

Pastikan database telah di-import sebelum menjalankan aplikasi.


# Hak Akses

### Admin

Admin memiliki hak untuk:

* Mengelola data lapangan
* Mengelola data pengguna
* Mengelola booking
* Mengelola pembayaran
* Melihat laporan pendapatan

### User

User dapat:

* Registrasi
* Login
* Booking lapangan
* Upload bukti pembayaran
* Melihat riwayat booking
* Mengubah profil


# Lisensi

Project ini dibuat untuk keperluan Tugas Mata Kuliah Pemrograman Web.

Seluruh source code digunakan hanya untuk kepentingan akademik.


# Kontributor

Kelompok Pemrograman Web
* Dimas Rahmat Oktavian	2024018031
* Raka Maulana Ramdan   2024018087
* Mudmainah Qolbi       2024018046
* Taufiq Kurnia Saputra	2024018081
