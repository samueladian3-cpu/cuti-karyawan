#!/bin/bash
# Pre-commit Security Check
# Run this before committing to ensure no sensitive data is committed

echo "🔍 Checking for sensitive data before commit..."
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check for files
ERRORS=0

# 1. Check if connection.php is staged
echo "1. Checking database credentials..."
if git diff --cached --name-only | grep -q "config/connection.php"; then
    echo -e "${RED}❌ FAIL: config/connection.php is staged for commit!${NC}"
    echo "   Run: git reset config/connection.php"
    ERRORS=$((ERRORS+1))
else
    echo -e "${GREEN}✓ Pass${NC}"
fi

# 2. Check for .sql files
echo "2. Checking SQL files..."
if git diff --cached --name-only | grep -q "\.sql$"; then
    echo -e "${RED}❌ FAIL: SQL files should not be committed!${NC}"
    echo "   Run: git reset '*.sql'"
    ERRORS=$((ERRORS+1))
else
    echo -e "${GREEN}✓ Pass${NC}"
fi

# 3. Check for log files
echo "3. Checking log files..."
if git diff --cached --name-only | grep -q "\.log$"; then
    echo -e "${RED}❌ FAIL: Log files should not be committed!${NC}"
    echo "   Run: git reset '*.log'"
    ERRORS=$((ERRORS+1))
else
    echo -e "${GREEN}✓ Pass${NC}"
fi

# 4. Check for .env files (except .env.example)
echo "4. Checking environment files..."
if git diff --cached --name-only | grep -E "^\.env$|\.env\.local$" | grep -v ".env.example"; then
    echo -e "${RED}❌ FAIL: .env files should not be committed!${NC}"
    ERRORS=$((ERRORS+1))
else
    echo -e "${GREEN}✓ Pass${NC}"
fi

# 5. Check for hardcoded passwords in staged files
echo "5. Checking for hardcoded credentials..."
if git diff --cached | grep -iE "password\s*=\s*['\"][^'\"]{3,}['\"]|api_key\s*=|secret\s*=\s*['\"]"; then
    echo -e "${YELLOW}⚠ WARNING: Possible hardcoded credentials found!${NC}"
    echo "   Review the changes carefully"
    # Not counting as error, just warning
fi

# 6. Check for database credentials in staged content
echo "6. Checking database connection strings..."
if git diff --cached | grep -iE "mysql://|postgresql://|mongodb://.*:.*@"; then
    echo -e "${RED}❌ FAIL: Database connection strings found!${NC}"
    ERRORS=$((ERRORS+1))
else
    echo -e "${GREEN}✓ Pass${NC}"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✅ All security checks passed!${NC}"
    echo "Safe to commit."
    exit 0
else
    echo -e "${RED}❌ Security check failed with $ERRORS error(s)${NC}"
    echo "Please fix the issues before committing."
    exit 1
fi
