# Sistem Manajemen Cuti Karyawan

Aplikasi web untuk mengelola cuti karyawan dengan dashboard admin dan portal karyawan.

## 🚀 Fitur

- **Portal Karyawan**: Login dengan NPK, lihat sisa cuti, ajukan cuti
- **Dashboard Admin**: Kelola data karyawan, approve/reject cuti, statistik
- **Manajemen Cuti**: Tracking cuti normal, tentative, dan pinjam cuti
- **API REST**: Endpoint untuk integrasi dengan sistem lain
- **Responsive Design**: Tampilan optimal di desktop dan mobile

## 📋 Requirements

- PHP 7.2 atau lebih tinggi
- MySQL 5.7 / MariaDB 10.2 atau lebih tinggi
- Web Server (Apache/Nginx)
- Extension PHP: mysqli, json

## 🔧 Installation

### 1. Clone Repository

```bash
git clone <repository-url>
cd cuti-karyawan
```

### 2. Setup Database

```bash
# Buat database
mysql -u root -p -e "CREATE DATABASE cuti_karyawan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import database structure
mysql -u root -p cuti_karyawan < Database/cuti_karyawan.sql
mysql -u root -p cuti_karyawan < Database/users_table.sql
```

Atau gunakan phpMyAdmin untuk import file SQL.

### 3. Konfigurasi Database

```bash
# Copy file konfigurasi
cp config/connection.example.php config/connection.php

# Edit file dan isi dengan kredensial database Anda
nano config/connection.php
```

Update values:
```php
$host = 'localhost';
$user = 'root';          // Your DB username
$pass = 'your_password'; // Your DB password
$db   = 'cuti_karyawan';
```

### 4. Setup Admin Account

Jalankan SQL berikut untuk membuat tabel admin dan akun default:

```sql
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin (username, password, nama_lengkap) 
VALUES ('admin', 'admin123', 'Administrator');
```

⚠️ **PENTING**: Ganti password default setelah login pertama kali!

### 5. Permissions

```bash
# Pastikan folder logs dapat ditulis
chmod 755 logs/
chmod 644 logs/*.log
```

## 🎮 Usage

### Akses Aplikasi

- **Portal Karyawan**: `http://localhost/cuti-karyawan/`
- **Admin Dashboard**: `http://localhost/cuti-karyawan/admin-cuti/`

### Login Default

**Admin:**
- Username: `admin`
- Password: `admin123`

**Karyawan:**
- NPK: `EMP001` (atau NPK lain dari database)

## 📁 Struktur Folder

```
cuti-karyawan/
├── admin-cuti/          # Dashboard admin
│   ├── dashboard.php    # Main admin dashboard
│   ├── login-admin.php  # Admin login
│   └── api-*.php        # Admin API endpoints
├── api/                 # REST API
│   ├── config.php       # API configuration
│   └── data.php         # API endpoints
├── assets/              # CSS, JS, images
│   ├── css/
│   └── js/
├── config/              # Configuration files (SENSITIVE)
│   ├── connection.php   # DB connection (ignored by git)
│   └── functions.php    # Helper functions
├── Database/            # SQL files (ignored by git)
├── logs/                # Application logs (ignored by git)
├── index.php            # Employee portal home
└── login.php            # Employee login
```

## 🔒 Security

### ⚠️ Files NOT Committed to Git (Sensitive)

- `config/connection.php` - Database credentials
- `Database/*.sql` - Database dumps with data
- `.env` - Environment variables
- `logs/*.log` - Log files
- `debug.php` - Debug scripts

### 🔐 Security Best Practices

1. **Change default passwords** immediately after setup
2. **Use strong passwords** for database and admin accounts
3. **Enable HTTPS** in production
4. **Restrict file permissions** (644 for files, 755 for folders)
5. **Keep PHP and MySQL updated**
6. **Review logs regularly** for suspicious activity
7. **Backup database regularly**

### 🛡️ Implemented Security Features

- Prepared statements (SQL injection protection)
- Session management
- CSRF protection (implement token validation)
- Input validation and sanitization
- Error logging (not displayed to users)

## 🐛 Development

### Debug Mode

File `debug.php` tersedia untuk troubleshooting (tidak di-commit):
```
http://localhost/cuti-karyawan/debug.php
```

### API Testing

Test API endpoints:
```bash
# Get leave data
curl http://localhost/cuti-karyawan/api/data.php

# Get statistics (admin)
curl http://localhost/cuti-karyawan/admin-cuti/api-get-statistik.php
```

## 📝 Database Schema

### Main Tables

- `users` - Data karyawan (NPK, nama, sisa cuti)
- `leaves` - Request cuti (pending/approved/rejected)
- `admin` - Akun admin
- `admin_cuti` - Detail cuti karyawan (tracking)

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### Commit Guidelines

- Pastikan tidak ada data sensitif
- Test semua perubahan
- Update dokumentasi jika perlu
- Follow coding standards

## 📄 License

[Add your license here]

## 📧 Contact

[Add your contact information]

## 🙏 Acknowledgments

- Bootstrap untuk UI framework
- PHP mysqli untuk database connectivity

---

**⚠️ IMPORTANT NOTES:**

1. Jangan commit file yang berisi:
   - Database credentials
   - API keys atau tokens
   - Data karyawan real
   - Log files

2. Gunakan `.env.example` sebagai template untuk setup lokal

3. Review `.gitignore` sebelum commit untuk memastikan file sensitif tidak ter-upload

4. Backup database secara berkala!
