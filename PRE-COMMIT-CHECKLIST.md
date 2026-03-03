# Pre-Commit Security Checklist

## ⚠️ CRITICAL - Before Every Commit

### 1. Database & Credentials
- [ ] `config/connection.php` is **NOT** staged for commit
- [ ] No `.env` files (except `.env.example`) are staged
- [ ] No database passwords in code
- [ ] No API keys or tokens in code

### 2. Database Files
- [ ] No `*.sql` files are staged for commit
- [ ] `Database/` folder is ignored by git
- [ ] SQL files don't contain real user data

### 3. Logs & Debug Files
- [ ] No `*.log` files are staged
- [ ] `debug.php` is not staged
- [ ] `setup-db.php` is not staged
- [ ] `logs/` folder is properly ignored

### 4. Code Review
- [ ] No `console.log()` with sensitive data
- [ ] No `var_dump()` or `print_r()` in production code
- [ ] Error messages don't expose system details
- [ ] No commented-out sensitive code

### 5. .gitignore Verification
```bash
# Run this to check if files are properly ignored:
git check-ignore -v config/connection.php
git check-ignore -v Database/*.sql
git check-ignore -v logs/*.log
```

Expected output should show these files are ignored.

### 6. Git Status Check
```bash
# Before commit, review what's staged:
git status
git diff --staged
```

### 7. Search for Sensitive Patterns
```bash
# Search staged files for sensitive data:
git diff --staged | grep -iE "password|secret|api_key|token"
```

## 🔧 Quick Commands

### Reset accidentally staged sensitive files
```bash
# Reset specific file
git reset config/connection.php

# Reset all SQL files
git reset Database/*.sql

# Reset logs
git reset logs/*.log
```

### View what will be committed
```bash
git diff --staged --name-only
git diff --staged
```

### Run security check
```bash
# On Linux/Mac/Git Bash:
bash .pre-commit-check.sh

# Or manually check:
git diff --cached --name-only | grep -E "connection.php|\.sql$|\.log$|\.env$"
```

## ✅ Safe to Commit

These files are SAFE to commit:
- ✅ `.env.example` - Template only
- ✅ `config/connection.example.php` - Template only
- ✅ `README.md`, `SECURITY.md` - Documentation
- ✅ `Database/README.md` - Instructions only
- ✅ PHP application code (without credentials)
- ✅ CSS, JS files (review for tokens)
- ✅ `.gitignore` - Ignore rules

## ❌ NEVER Commit

These files must NEVER be committed:
- ❌ `config/connection.php` - Contains DB credentials
- ❌ `Database/*.sql` - May contain real data
- ❌ `.env`, `.env.local` - Environment variables
- ❌ `logs/*.log` - May contain sensitive info
- ❌ `debug.php` - Debug scripts
- ❌ `setup-db.php` - Setup scripts
- ❌ Any file with real user data

## 📝 Commit Message Template

```
<type>: <short description>

<detailed description if needed>

Security checklist:
- [x] No credentials committed
- [x] No sensitive data committed
- [x] .gitignore verified
```

Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`, `security`

## 🚨 If Sensitive Data Was Committed

If you accidentally committed sensitive data:

### 1. Before Pushing
```bash
# Remove last commit but keep changes
git reset --soft HEAD~1

# Remove file from staging
git reset config/connection.php

# Commit again without sensitive file
git commit
```

### 2. After Pushing (DANGER!)
```bash
# This rewrites history - coordinate with team!
git reset --hard HEAD~1
git push --force

# Or use git-filter-repo to remove specific file from history
# Contact team lead before doing this!
```

### 3. If Pushed to Public Repo
1. **Immediately change all credentials**
2. Rotate API keys and tokens
3. Remove file from git history
4. Force push (if allowed)
5. Inform security team

## 🔗 Resources

- [.gitignore file](.gitignore)
- [Security Policy](SECURITY.md)
- [Setup Guide](README.md)

---

**Remember:** It's easier to prevent than to fix! Always review before commit.
