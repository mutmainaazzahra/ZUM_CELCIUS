Zum Celcius. Web Aplikasi Cuaca & Aktivitas Harian
Aplikasi web sederhana berbasis PHP Native, OOP, dan MVC. Aplikasi ini menggabungkan data cuaca real time dengan manajemen jadwal aktivitas harian personal. Dilengkapi sistem notifikasi Web Push dan analitik sederhana.

Teknologi Inti
Backend : PHP Native, Arsitektur MVC Sederhana.
Database : MySQL.
API Cuaca : OpenWeatherMap API untuk prakiraan 5 hari, 3 jam.
Geolokasi : Mapbox Geocoding API.
Notifikasi : Web Push VAPID menggunakan Composer, Minishlink/WebPush.
Email : PHPMailer.
Frontend : Bootstrap 5, Chart.js.

Fitur Utama Aplikasi
Cuaca & Analitik. Melihat cuaca real time dan prakiraan 5 hari di lokasi mana pun.
Aktivitas Harian. CRUD. Create, Read, Update, Delete jadwal aktivitas personal.
Notifikasi Otomatis. Peringatan Cuaca Hujan, Badai dan Pengingat Aktivitas 5 menit sebelum via Web Push. Fitur ini membutuhkan Cron Job.
Laporan. Admin Dashboard untuk melihat statistik aktivitas dan log notifikasi.
Autentikasi. Login, Register, dan Lupa Password dengan simulasi link reset via email.

Persiapan Awal dan Instalasi Lokal :

1. Persiapan Lingkungan
   Pastikan Anda telah menginstal XAMPP, Laragon, atau lingkungan PHP 8.1+ dan MySQL.
   Pastikan Anda telah menjalankan Composer untuk mengunduh dependencies PHPMailer, WebPush.
   composer install

2. Konfigurasi Database
   Buka PHPMyAdmin atau terminal MySQL.
   Buat database baru bernama zum_celcius.
   Import file zum_celcius.sql ke dalam database tersebut.

3. Konfigurasi Kunci API & Lingkungan
   Salin file .env menjadi .env.example. File .env.example akan di commit ke GitHub.
   Buka file .env. File ini akan diabaikan oleh Git.
   Isi variabel berikut:
   DB*HOST, DB_USER, DB_PASS. Sesuaikan dengan kredensial database lokal Anda.
   OPENWEATHER_API_KEY. Dapatkan dari OpenWeatherMap.
   MAPBOX_ACCESS_TOKEN. Dapatkan dari Mapbox.
   SMTP*. Isi kredensial email. Gunakan App Password dari Google, Yahoo jika SMTP_HOST adalah Gmail.

4. Generate VAPID Keys. Wajib untuk Notifikasi
   Jalankan skrip generator kunci VAPID:
   php generate_vapid.php
   Salin VAPID_PUBLIC_KEY dan VAPID_PRIVATE_KEY yang dihasilkan ke file .env.
   Menjalankan Aplikasi
   Pindahkan folder zum_celcius ke direktori root web server Anda htdocs atau www.

Akses di browser: http://localhost/zum_celcius/public/
Untuk mengaktifkan notifikasi otomatis, atur Cron Job untuk menjalankan php public/cron_jobs.php setiap menit.

Akun Uji :
Administrator. admin@zumcelcius.com, password.
Guest User. guest@zumcelcius.com, password.
