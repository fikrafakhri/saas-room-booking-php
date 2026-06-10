# SaaS Room Booking System (Multi-Role)

A web-based room booking and reservation application built using Native PHP and MySQL with industry standards. This project solves overlapping schedule problems using backend validation logic.

## 🚀 Fitur Utama
- **Role-Based Access Control (RBAC):** Pemisahan hak akses dinamis antara `User Biasa` (membuat pengajuan booking) dan `Admin Ruangan` (mengelola ruangan dan memberikan approval).
- **Anti-Double Booking Logic:** Menggunakan enkapsulasi logika query database untuk mendeteksi dan menolak jadwal reservasi yang bentrok secara *real-time*.
- **Secure Authentication:** Manajemen user menggunakan `password_hash()` standar industri dan pengamanan halaman menggunakan PHP Session.
- **PDO Database Wrapper:** Implementasi PDO untuk interaksi basis data yang aman dari ancaman *SQL Injection*.
- **Environment Isolation:** Pemisahan kredensial sensitif menggunakan file `.env` (diabaikan oleh Git menggunakan `.gitignore`).

## 🛠️ Tech Stack
- **Backend:** PHP Native (PDO)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5 (Responsive Layout)
- **Environment:** Laragon / XAMPP

## 📦 Cara Install di Lokal
1. Clone repositori ini ke folder `www` atau `htdocs` Anda.
2. Buat database baru bernama `db_room_booking` dan import file SQL yang disediakan.
3. Duplikat/buat file `.env` di root folder dan sesuaikan kredensial database Anda.
4. Akses aplikasi melalui `localhost/room-booking-saas/auth/login.php`.
