# Database Setup

⚠️ **SECURITY NOTICE**: This folder is excluded from version control (.gitignore)

## Database Files

- `cuti_karyawan.sql` - Main database structure for employee leave system
- `users_table.sql` - User table structure with sample data

## Setup Instructions

1. Create a new database named `cuti_karyawan` in your MySQL/MariaDB server
   ```sql
   CREATE DATABASE cuti_karyawan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Import the SQL files:
   ```bash
   mysql -u root -p cuti_karyawan < Database/cuti_karyawan.sql
   mysql -u root -p cuti_karyawan < Database/users_table.sql
   ```

   Or via phpMyAdmin:
   - Open phpMyAdmin
   - Select `cuti_karyawan` database
   - Go to Import tab
   - Choose the SQL file and click Go

3. Create an admin account:
   ```sql
   CREATE TABLE IF NOT EXISTS admin (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(50) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       nama_lengkap VARCHAR(100) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   
   -- Insert default admin (password: admin123)
   -- TODO: Change this password after first login!
   INSERT INTO admin (username, password, nama_lengkap) 
   VALUES ('admin', 'admin123', 'Administrator');
   ```

4. Update your database connection:
   - Copy `config/connection.example.php` to `config/connection.php`
   - Update the credentials in `connection.php`

## Security Notes

- **Never commit SQL files with real data** to version control
- **Sample data is for testing only** - remove before production
- **Change default admin password** immediately after setup
- **Use strong passwords** for all accounts
- **Use prepared statements** for all SQL queries (already implemented)

## Backup

To backup your database:
```bash
mysqldump -u root -p cuti_karyawan > backup_$(date +%Y%m%d).sql
```
