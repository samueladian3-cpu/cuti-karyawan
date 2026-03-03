# Security Policy

## 🔒 Reporting a Vulnerability

Jika Anda menemukan vulnerability atau masalah keamanan, mohon **JANGAN** membuat issue publik. Hubungi maintainer secara privat.

## 🛡️ Security Best Practices

### 1. Database Security

✅ **DO:**
- Gunakan prepared statements (sudah implemented)
- Store passwords dengan hashing (bcrypt/argon2)
- Gunakan strong database passwords
- Restrict database user privileges
- Enable MySQL/MariaDB security features

❌ **DON'T:**
- Jangan commit `config/connection.php`
- Jangan hardcode credentials dalam kode
- Jangan gunakan root MySQL tanpa password
- Jangan commit SQL dumps dengan data real

### 2. Code Security

✅ **DO:**
```php
// Good: Prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

// Good: Input validation
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);

// Good: Password hashing
$hashed = password_hash($password, PASSWORD_ARGON2ID);
```

❌ **DON'T:**
```php
// Bad: SQL injection vulnerability
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];

// Bad: Plain text passwords
$password = $_POST['password']; //Store as-is

// Bad: Exposing sensitive info
echo "Error: " . mysqli_error($conn); // In production
```

### 3. Session Security

✅ **DO:**
```php
// Regenerate session ID after login
session_regenerate_id(true);

// Use secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);  // For HTTPS
ini_set('session.use_strict_mode', 1);

// Check session validity
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
```

### 4. File Upload Security (jika ada)

✅ **DO:**
- Validate file types (whitelist)
- Check file size limits
- Rename uploaded files
- Store uploads outside webroot
- Scan for malware

❌ **DON'T:**
- Jangan trust `$_FILES['file']['type']`
- Jangan execute uploaded files
- Jangan allow .php uploads

### 5. XSS Prevention

✅ **DO:**
```php
// Escape output
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// In JavaScript
console.log(encodeURIComponent(userInput));
```

### 6. CSRF Protection

✅ **TODO:** Implement CSRF tokens
```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate token
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

### 7. Production Checklist

Sebelum deploy ke production:

- [ ] Change all default passwords
- [ ] Enable HTTPS (SSL/TLS)
- [ ] Set `display_errors = Off` in php.ini
- [ ] Set `log_errors = On`
- [ ] Review and test `.gitignore`
- [ ] Remove debug files (`debug.php`, `setup-db.php`)
- [ ] Implement rate limiting untuk login
- [ ] Setup database backups otomatis
- [ ] Review file permissions (644/755)
- [ ] Enable web application firewall (WAF)
- [ ] Setup monitoring dan alerts
- [ ] Hash admin passwords (currently plain text!)

## 🚨 Known Issues (To Fix)

### CRITICAL
1. **Admin passwords stored in plain text** - Need to implement password_hash()
2. **No CSRF protection** - Add token validation

### HIGH
3. **No rate limiting** - Prevent brute force attacks
4. **Console.log in production JS** - Remove debug statements
5. **Error messages exposed** - Use generic messages for users

### MEDIUM
6. **Session timeout not configured** - Add session expiry
7. **No backup mechanism** - Implement automated backups

## 📋 Security Checklist

### Before Commit
- [ ] No database credentials
- [ ] No API keys or secrets
- [ ] No real user data
- [ ] No log files with sensitive info
- [ ] Debug code removed
- [ ] `.gitignore` updated

### Before Production
- [ ] All critical issues fixed
- [ ] Security headers configured
- [ ] HTTPS enabled
- [ ] Database backups enabled
- [ ] Monitoring setup
- [ ] Security testing done

## 🔐 Recommended Security Headers

Add to `.htaccess` atau web server config:

```apache
# Apache
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Content-Security-Policy "default-src 'self'"
```

```nginx
# Nginx
add_header X-Content-Type-Options "nosniff" always;
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

## 📚 Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [MySQL Security Best Practices](https://dev.mysql.com/doc/refman/8.0/en/security-guidelines.html)

## 📝 Security Updates

Check for updates regularly:
- PHP security patches
- MySQL/MariaDB updates
- Dependencies (jika menggunakan Composer)

---

Last updated: March 2026
