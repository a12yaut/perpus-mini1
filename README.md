# 📚 Perpustakaan Mini

Aplikasi manajemen perpustakaan berbasis web dengan PHP Native, Tailwind CSS, dan MySQL.

## ✨ Fitur

- **Katalog Buku** - Lihat, cari, dan filter buku
- **Detail Buku** - Informasi lengkap buku dengan sinopsis
- **Peminjaman Multi-Buku** - Maksimal 3 buku per peminjaman
- **Pengembalian Otomatis** - Update stok dan hitung denda
- **Sistem Denda** - Denda keterlambatan Rp 2.000/hari
- **Dashboard Anggota** - Lihat peminjaman aktif dan riwayat
- **Dashboard Admin** - Kelola buku, anggota, peminjaman & statistik

## 🚀 Cara Instalasi

### Untuk XAMPP / Local Server

1. Clone atau download ZIP project ini
2. Copy folder `perpustakaan-mini` ke `htdocs`
3. Buat database MySQL dan import `database/schema.sql`
4. Copy `.env.example` menjadi `.env` dan sesuaikan kredensial database
5. Buka browser: `http://localhost/perpustakaan-mini/api/index.php`

### Untuk Vercel (Deployment)

1. Push project ke GitHub
2. Hubungkan repository ke Vercel
3. Tambahkan environment variables di Vercel Dashboard:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
4. Deploy!

## 🔑 Akun Default

- **Admin**: admin@perpus.com / admin123
- **Anggota**: Daftar sendiri di halaman register

## 📊 Struktur Database

### users
- `id` - Primary key
- `nama` - Nama lengkap
- `email` - Email (unique)
- `password` - Password hash
- `role` - admin / anggota
- `created_at` - Waktu pendaftaran

### books
- `id` - Primary key
- `judul` - Judul buku
- `penulis` - Nama penulis
- `kategori` - Kategori buku
- `tahun_terbit` - Tahun terbit
- `sinopsis` - Deskripsi buku
- `stok` - Jumlah stok
- `created_at` - Waktu input

### loans
- `id` - Primary key
- `user_id` - Foreign key ke users
- `book_id` - Foreign key ke books
- `tanggal_pinjam` - Tanggal pinjam
- `tanggal_jatuh_tempo` - Tanggal jatuh tempo (7 hari)
- `tanggal_kembali` - Tanggal kembali (null jika belum)
- `denda` - Denda keterlambatan
- `status` - dipinjam / dikembalikan
- `created_at` - Waktu input

## 🛠️ Teknologi

- PHP 7.4+
- MySQL 5.7+
- Tailwind CSS
- Vercel (deployment)

## 📁 Struktur Folder
