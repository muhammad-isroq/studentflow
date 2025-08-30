StudentFlow - Sistem Manajemen Kursus(masih dalam pengembangan)

Tentang Proyek Ini


StudentFlow adalah aplikasi web modern yang dirancang untuk mengelola semua aspek administrasi di sebuah tempat kursus bahasa Inggris. Dibangun dengan Laravel 12 dan panel admin super cepat menggunakan Filament v4, aplikasi ini bertujuan untuk mengotomatiskan tugas-tugas manual sehingga staf bisa lebih fokus pada proses belajar mengajar.

Sistem ini menangani semuanya, mulai dari pendaftaran siswa, pengelolaan program dan guru, hingga pembuatan tagihan bulanan (SPP) yang berjalan secara otomatis.



Fitur Utama
Manajemen Siswa: CRUD (Create, Read, Update, Delete) lengkap untuk data siswa, termasuk biodata, foto, dan dokumen pendaftaran.

Manajemen Program & Kelas: Kemudahan untuk membuat program baru, mengatur biayanya, dan memasukkan siswa ke dalam kelas masing-masing.

Tagihan Otomatis: Sistem scheduler (cron job) yang secara otomatis membuat tagihan SPP bulanan untuk setiap siswa aktif sesuai tanggal yang ditentukan.

Manajemen Role & User: Sistem hak akses dengan dua level (Admin & Staff), di mana Admin dapat mengelola akun Staff.

Dashboard Interaktif: Dilengkapi dengan grafik pendaftaran siswa dan widget peringatan untuk tagihan yang sudah jatuh tempo.

Impor Data Massal: Fitur untuk mengimpor ratusan hingga ribuan data siswa dari file Excel, mempermudah migrasi dari sistem lama.



Teknologi yang Digunakan
Backend: Laravel 12

Frontend & Panel Admin: Filament v4 (TALL Stack: Tailwind CSS, Alpine.js, Livewire)

Database: MySQL

Jadwal Otomatis: Laravel Scheduler (Cron Job)

Roles & Permissions: Spatie Laravel Permission
