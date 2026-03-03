# System logs - This folder is excluded from version control

⚠️ **SECURITY**: Log files may contain sensitive information and are automatically ignored by git.

## Log Files

- `php-error.log` - PHP errors and warnings
- Other application logs

## Important

- Logs are automatically created by the application
- Never commit log files to version control
- Review logs regularly for errors and security issues
- Clear old logs periodically to save disk space

## Log Rotation

To clear old logs:
```bash
# Backup old logs
mv logs/php-error.log logs/php-error.log.old

# Or delete completely
rm logs/*.log
```


