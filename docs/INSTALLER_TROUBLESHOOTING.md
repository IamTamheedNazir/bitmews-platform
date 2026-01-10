# 🔧 **INSTALLER TROUBLESHOOTING GUIDE**

## 🚨 **COMMON INSTALLATION ERRORS & FIXES**

---

## ❌ **ERROR: ".env.example file not found"**

### **Cause:**
The installer cannot find the `.env.example` template file.

### **Solution 1: Verify File Exists**
```bash
# Check if file exists
ls -la .env.example

# If missing, download it
wget https://raw.githubusercontent.com/IamTamheedNazir/bitmews-platform/main/.env.example
```

### **Solution 2: Create Manually**
Create `.env.example` in root directory with this content:
```env
APP_NAME=BitMews
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitmews
DB_USERNAME=root
DB_PASSWORD=
```

### **Solution 3: Fixed in Latest Version**
The installer now has a fallback that creates `.env.example` automatically if missing.

**Update your code:**
```bash
# Re-download from GitHub
# The latest version includes automatic fallback
```

---

## ❌ **ERROR: "artisan file not found"**

### **Cause:**
Laravel's `artisan` command-line tool is missing.

### **Solution:**
```bash
# Download artisan file
wget https://raw.githubusercontent.com/IamTamheedNazir/bitmews-platform/main/artisan

# Make it executable
chmod +x artisan

# Verify
php artisan --version
```

---

## ❌ **ERROR: "composer.json not found"**

### **Cause:**
Composer dependencies file is missing.

### **Solution:**
```bash
# Download composer.json
wget https://raw.githubusercontent.com/IamTamheedNazir/bitmews-platform/main/composer.json

# Install dependencies
composer install --no-dev --optimize-autoloader
```

---

## ❌ **ERROR: "vendor directory not found"**

### **Cause:**
Composer dependencies not installed.

### **Solution 1: Install via Composer (Recommended)**
```bash
composer install --no-dev --optimize-autoloader
```

### **Solution 2: Upload vendor folder**
If you don't have Composer on server:
1. Run `composer install` on your local computer
2. Upload the generated `vendor` folder to server
3. This folder will be ~50-100MB

---

## ❌ **ERROR: "Permission denied" on storage folders**

### **Cause:**
Storage folders don't have write permissions.

### **Solution:**
```bash
# Set correct permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/uploads

# If 755 doesn't work, try 775
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod -R 775 public/uploads

# Verify
ls -la storage
```

---

## ❌ **ERROR: "Failed to generate application key"**

### **Cause:**
Cannot execute `php artisan key:generate` command.

### **Solution 1: Manual Key Generation**
The installer now automatically generates a key manually if artisan fails.

### **Solution 2: Generate Manually**
```bash
# Generate random key
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"

# Copy the output and add to .env
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
```

---

## ❌ **ERROR: "Database connection failed"**

### **Cause:**
Incorrect database credentials or database doesn't exist.

### **Solutions:**

#### **1. Verify Database Exists**
```sql
-- Login to MySQL
mysql -u root -p

-- Check databases
SHOW DATABASES;

-- Create if missing
CREATE DATABASE bitmews;
```

#### **2. Check Credentials**
```bash
# Test connection
mysql -h localhost -u your_username -p your_database

# If fails, check:
- Database name (include cPanel prefix: username_bitmews)
- Username (include cPanel prefix: username_user)
- Password (correct password)
- Host (try 'localhost' or '127.0.0.1')
```

#### **3. Grant Permissions**
```sql
GRANT ALL PRIVILEGES ON bitmews.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

---

## ❌ **ERROR: "Class not found" or "Namespace not found"**

### **Cause:**
Composer autoload not generated.

### **Solution:**
```bash
# Regenerate autoload
composer dump-autoload

# Or full reinstall
rm -rf vendor
composer install --no-dev --optimize-autoloader
```

---

## ❌ **ERROR: "500 Internal Server Error"**

### **Causes & Solutions:**

#### **1. Check PHP Version**
```bash
# Check version
php -v

# Should be 8.0 or higher
# If not, update in cPanel → Select PHP Version
```

#### **2. Check Error Logs**
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View server logs
tail -f /var/log/apache2/error.log
```

#### **3. Enable Debug Mode**
```env
# In .env file
APP_DEBUG=true
APP_ENV=local

# Refresh page to see actual error
```

#### **4. Check .htaccess**
```apache
# Ensure .htaccess exists in public_html
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## ❌ **ERROR: "Migration failed"**

### **Cause:**
Database migration errors.

### **Solutions:**

#### **1. Check Database Connection**
```bash
php artisan migrate:status
```

#### **2. Reset and Retry**
```bash
# Drop all tables
php artisan migrate:fresh

# Or manually in phpMyAdmin:
# Drop all tables and retry
```

#### **3. Check Migration Files**
```bash
# Verify migrations exist
ls -la database/migrations/

# Should have 11+ migration files
```

---

## ❌ **ERROR: "Seeder failed"**

### **Cause:**
Sample data seeding errors.

### **Solution:**
```bash
# Skip seeders (optional)
# You can add data manually later

# Or run specific seeder
php artisan db:seed --class=CurrencySeeder
php artisan db:seed --class=BlockchainSeeder
```

---

## ❌ **ERROR: "Storage link failed"**

### **Cause:**
Cannot create symbolic link.

### **Solution 1: Via Artisan**
```bash
php artisan storage:link
```

### **Solution 2: Manual Link**
```bash
# Create symbolic link manually
ln -s ../storage/app/public public/storage
```

### **Solution 3: Skip (Not Critical)**
Storage link is optional. You can skip this step.

---

## 🎯 **COMPLETE INSTALLATION CHECKLIST**

### **Before Installation:**
- [ ] PHP 8.0+ installed
- [ ] MySQL database created
- [ ] Database user created with ALL PRIVILEGES
- [ ] All files uploaded to server
- [ ] Composer dependencies installed (`vendor` folder exists)
- [ ] Permissions set (755 on storage, bootstrap/cache)

### **During Installation:**
- [ ] Visit `/install`
- [ ] Pass requirements check
- [ ] Pass permissions check
- [ ] Enter correct database credentials
- [ ] Test database connection (should show ✅)
- [ ] Create admin account
- [ ] Wait for all steps to complete

### **After Installation:**
- [ ] Delete `/install` folder
- [ ] Login to `/admin`
- [ ] Test home page
- [ ] Test chatbot
- [ ] Test API: `/api/v1/health`

---

## 🚀 **QUICK FIX COMMANDS**

### **Reset Everything:**
```bash
# Delete .env
rm .env

# Clear cache
rm -rf bootstrap/cache/*
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*

# Reset permissions
chmod -R 755 storage bootstrap/cache

# Retry installation
```

### **Manual Installation (If Installer Fails):**
```bash
# 1. Copy .env.example to .env
cp .env.example .env

# 2. Edit .env with your database credentials
nano .env

# 3. Generate key
php artisan key:generate

# 4. Run migrations
php artisan migrate --force

# 5. Run seeders
php artisan db:seed --force

# 6. Create admin manually in database
# Or use: php artisan make:admin

# 7. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set permissions
chmod -R 755 storage bootstrap/cache
```

---

## 📞 **STILL HAVING ISSUES?**

### **Check These:**

1. **PHP Version:** Must be 8.0+
2. **PHP Extensions:** mbstring, xml, curl, zip, gd, pdo_mysql, fileinfo
3. **File Permissions:** 755 on storage, bootstrap/cache
4. **Database:** Exists, user has privileges
5. **Composer:** Dependencies installed (vendor folder exists)
6. **Files:** All files uploaded (check composer.json, artisan, public/index.php)

### **Get Help:**
- Check error logs: `storage/logs/laravel.log`
- Enable debug mode: `APP_DEBUG=true` in `.env`
- Check server error logs
- Contact hosting support for server-level issues

---

## ✅ **LATEST FIXES APPLIED**

The installer has been updated with:
- ✅ Automatic `.env.example` fallback (creates if missing)
- ✅ Better path detection (tries multiple locations)
- ✅ Manual key generation fallback (if artisan fails)
- ✅ Better error messages
- ✅ Multiple PHP executable detection

**Re-download from GitHub to get latest fixes!**

---

## 🎉 **AFTER SUCCESSFUL INSTALLATION**

You should see:
- ✅ Home page loads
- ✅ Admin panel accessible at `/admin`
- ✅ Chatbot appears (bottom-right)
- ✅ API responds at `/api/v1/health`
- ✅ Can register/login users
- ✅ Can create posts

**Congratulations! Your platform is live!** 🚀
