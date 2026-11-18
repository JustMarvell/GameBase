# GameBase
 
Website perpustakaan dan ulasan game. Pengunjung bisa menjelajahi game, membaca ulasan, dan (setelah mendaftar) menyimpan game ke library pribadi, menulis ulasan, serta mengatur profil. Admin mengelola data game lewat dashboard.
 
Proyek akhir mata kuliah **Framework Programming**, dibuat dengan Laravel 12.

## Instalasi (Lokal)
 
1. Salin proyek dan pasang dependensi:
```bash
git clone [URL repository] game-library
cd game-library
composer install
npm install
```
 
2. Buat file `.env`:
```bash
cp .env.example .env
php artisan key:generate
```
 
3. Buat database kosong bernama `db_game_library` (lewat phpMyAdmin atau MySQL), lalu ubah bagian ini di `.env`:
```env
APP_NAME=GameBase
APP_URL=http://127.0.0.1:8000
APP_LOCALE=id
 
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_game_library
DB_USERNAME=root
DB_PASSWORD=
```
 
4. Jalankan migration dan seeder, lalu hubungkan folder storage:
```bash
php artisan migrate --seed
php artisan storage:link
```
 
   Seeder membuat role, akun admin, akun contoh, beberapa genre, platform, game, dan ulasan.
 
5. Jalankan aplikasi:
```bash
composer run dev
```
