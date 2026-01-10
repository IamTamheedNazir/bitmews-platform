# ✅ **PRE-INSTALLATION CHECKLIST - MUST DO BEFORE RUNNING INSTALLER**

## 🚨 **CRITICAL: DO THESE STEPS FIRST!**

The installer **WILL FAIL** if you skip these steps. Follow them **IN ORDER**.

---

## 📋 **STEP 1: VERIFY SERVER REQUIREMENTS**

### **Check PHP Version:**
```bash
php -v
# Must show: PHP 8.0 or higher
```

**In cPanel:**
1. Go to **"Select PHP Version"** or **"MultiPHP Manager"**
2. Select **PHP 8.0** or higher (8.1 or 8.2 recommended)
3. Click **"Apply"**

### **Enable Required PHP Extensions:**
In **"Select PHP Version"** → **"Extensions"**, enable:
- ✅ mbstring
- ✅ xml
- ✅ curl
- ✅ zip
- ✅ gd
- ✅ pdo_mysql
- ✅ fileinfo
- ✅ openssl
- ✅ tokenizer
- ✅ json

---

## 📋 **STEP 2: CREATE DATABASE (MANDATORY!)**

### **⚠️ INSTALLER CANNOT CREATE DATABASE!**

You **MUST** create it manually in cPanel:

### **A. Create Database:**
1. Go to **"MySQL® Databases"**
2. Under **"Create New Database"**:
   ```
   New Database: bitmews
   ```
3. Click **"Create Database"**
4. **Note the full name:** `yourusername_bitmews`

### **B. Create Database User:**
1. Scroll to **"MySQL Users"** → **"Add New User"**
2. Enter:
   ```
   Username: bitmews_user
   Password: [Generate strong password]
   ```
3. Click **"Create User"**
4. **SAVE THE PASSWORD!** You'll need it!
5. **Note the full username:** `yourusername_bitmews_user`

### **C. Add User to Database:**
1. Scroll to **"Add User To Database"**
2. Select:
   - User: `yourusername_bitmews_user`
   - Database: `yourusername_bitmews`
3. Click **"Add"**
4. On next page, check **"ALL PRIVILEGES"**
5. Click **"Make Changes"**

### **D. Save Your Credentials:**
```
Database Host: localhost
Database Port: 3306
Database Name: yourusername_bitmews
Database Username: yourusername_bitmews_user
Database Password: [your password]
```

---

## 📋 **STEP 3: UPLOAD FILES TO SERVER**

### **A. Download from GitHub:**
```
https://github.com/IamTamheedNazir/bitmews-platform
Click "Code" → "Download ZIP"
```

### **B. Extract on Your Computer:**
- Extract `bitmews-platform-main.zip`
- You'll get a folder with all files

### **C. Upload to cPanel:**

**Method 1: File Manager (Easier)**
1. Go to **"File Manager"**
2. Navigate to **`public_html`**
3. Click **"Upload"**
4. Upload the ZIP file
5. After upload, right-click ZIP → **"Extract"**
6. Move all files from `bitmews-platform-main` to `public_html`
7. Delete the empty folder and ZIP

**Method 2: FTP (Faster for large files)**
1. Use FileZilla or similar
2. Connect to your server
3. Navigate to `public_html`
4. Upload all files from extracted folder

### **D. Verify Files Uploaded:**
Check these files exist in `public_html`:
- ✅ `composer.json`
- ✅ `artisan`
- ✅ `public/index.php`
- ✅ `.env.example`
- ✅ `database/migrations/` (folder with 11 files)

---

## 📋 **STEP 4: INSTALL COMPOSER DEPENDENCIES (CRITICAL!)**

### **⚠️ THIS IS THE MOST COMMON REASON FOR FAILURE!**

The `vendor` folder contains Laravel and all dependencies. **Without it, nothing works!**

### **Option A: Via SSH (Recommended)**
```bash
cd public_html
composer install --no-dev --optimize-autoloader
```

### **Option B: Via cPanel Terminal**
```bash
cd public_html
/usr/local/bin/composer install --no-dev
```

### **Option C: Upload vendor Folder**
If you don't have Composer on server:

1. **On your local computer:**
   ```bash
   cd bitmews-platform-main
   composer install --no-dev --optimize-autoloader
   ```

2. **Upload the generated `vendor` folder:**
   - This folder will be ~50-100MB
   - Upload via FTP (File Manager will timeout)
   - Place in `public_html/vendor`

### **D. Verify vendor Folder:**
```bash
# Check if exists
ls -la vendor/

# Should see:
vendor/autoload.php
vendor/laravel/
vendor/guzzlehttp/
# ... and many more folders
```

**⚠️ If vendor folder is missing, installer WILL FAIL at migrations!**

---

## 📋 **STEP 5: SET FILE PERMISSIONS**

### **Via File Manager:**
1. Right-click `storage` folder
2. Click **"Change Permissions"**
3. Set to **755**
4. Check **"Recurse into subdirectories"**
5. Click **"Change Permissions"**

Repeat for:
- `bootstrap/cache`
- `public/uploads`

### **Via SSH:**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/uploads
```

### **Verify:**
```bash
ls -la storage
# Should show: drwxr-xr-x (755)
```

---

## 📋 **STEP 6: VERIFY FOLDER STRUCTURE**

### **Check these folders exist:**
```
public_html/
├── app/
├── bootstrap/
│   └── cache/
├── config/
├── database/
│   ├── migrations/  ← (11 files)
│   └── seeders/     ← (8 files)
├── install/
├── public/
│   ├── index.php    ← (CRITICAL!)
│   └── uploads/
├── resources/
├── routes/
├── storage/
│   ├── app/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
├── vendor/          ← (MUST EXIST!)
├── .env.example
├── artisan
└── composer.json
```

---

## 📋 **STEP 7: FINAL CHECKS BEFORE INSTALLER**

### **✅ Checklist:**
- [ ] PHP 8.0+ installed
- [ ] All PHP extensions enabled
- [ ] Database created in cPanel
- [ ] Database user created
- [ ] User added to database with ALL PRIVILEGES
- [ ] Database credentials saved
- [ ] All files uploaded to `public_html`
- [ ] `vendor` folder exists (composer install done)
- [ ] Permissions set (755 on storage, bootstrap/cache)
- [ ] `artisan` file exists
- [ ] `public/index.php` exists
- [ ] `composer.json` exists
- [ ] `.env.example` exists

---

## 🚀 **NOW RUN THE INSTALLER**

### **Visit:**
```
https://yourdomain.com/install
```

### **You Should See:**
1. ✅ Welcome screen
2. ✅ Requirements check (all green)
3. ✅ Permissions check (all green)
4. ✅ Database configuration form

### **Enter Your Database Details:**
```
Database Connection: mysql
Database Host: localhost
Database Port: 3306
Database Name: yourusername_bitmews
Database Username: yourusername_bitmews_user
Database Password: [your saved password]
```

### **Click "Test Connection":**
- Should show: ✅ "Connection Successful"
- If fails, double-check credentials

### **Continue Through Steps:**
5. ✅ Environment file created
6. ✅ Application key generated
7. ✅ Migrations run (40+ tables created)
8. ✅ Sample data seeded
9. ✅ Admin account created
10. ✅ Storage links created
11. ✅ Application optimized

---

## 🚨 **COMMON ERRORS & QUICK FIXES**

### **Error: "vendor folder not found"**
```bash
# Install composer dependencies
composer install --no-dev --optimize-autoloader
```

### **Error: "Migration failed"**
**Causes:**
1. Database doesn't exist → Create in cPanel
2. Wrong credentials → Check username/password
3. No privileges → Grant ALL PRIVILEGES
4. vendor folder missing → Run composer install

### **Error: "artisan not found"**
```bash
# Download artisan file
wget https://raw.githubusercontent.com/IamTamheedNazir/bitmews-platform/main/artisan
chmod +x artisan
```

### **Error: "Permission denied"**
```bash
# Fix permissions
chmod -R 755 storage bootstrap/cache public/uploads
```

---

## 📊 **INSTALLATION TIMELINE**

```
Manual Setup (YOU):     5-10 minutes
├── Create database:    2 min
├── Upload files:       3 min
├── Composer install:   3 min
└── Set permissions:    2 min

Installer (AUTO):       3-5 minutes
├── Requirements:       10 sec
├── Permissions:        10 sec
├── Database test:      5 sec
├── Create .env:        5 sec
├── Generate key:       5 sec
├── Run migrations:     60 sec
├── Seed data:          30 sec
├── Create admin:       10 sec
└── Optimize:           30 sec

TOTAL TIME:            8-15 minutes
```

---

## ✅ **AFTER SUCCESSFUL INSTALLATION**

### **You Should See:**
```
✅ Installation Complete!
✅ 40+ database tables created
✅ Sample data added
✅ Admin account created
✅ Application optimized
```

### **Next Steps:**
1. **Delete install folder:**
   ```bash
   rm -rf install/
   ```

2. **Login to admin:**
   ```
   https://yourdomain.com/admin
   ```

3. **Visit website:**
   ```
   https://yourdomain.com
   ```

4. **Test chatbot:**
   - Should appear bottom-right corner
   - Click and send a message

---

## 🎉 **SUCCESS INDICATORS**

### **✅ Installation Successful If:**
- Home page loads without errors
- Admin panel accessible at `/admin`
- Can login with admin credentials
- Chatbot appears on pages
- API responds at `/api/v1/health`
- No 500 errors

### **❌ Installation Failed If:**
- 500 Internal Server Error
- "Class not found" errors
- "Database connection failed"
- "Migration failed"
- Blank white page

**If failed, check this checklist again!**

---

## 📞 **NEED HELP?**

### **Before Asking for Help:**
1. ✅ Complete ALL steps in this checklist
2. ✅ Verify database exists and credentials correct
3. ✅ Verify vendor folder exists
4. ✅ Check error logs: `storage/logs/laravel.log`
5. ✅ Enable debug: `APP_DEBUG=true` in `.env`

### **Common Issues:**
- 90% of failures = vendor folder missing
- 5% of failures = wrong database credentials
- 3% of failures = wrong permissions
- 2% of failures = PHP version too old

---

## 🎯 **SUMMARY**

### **MUST DO BEFORE INSTALLER:**
1. ✅ Create database in cPanel
2. ✅ Create database user
3. ✅ Grant ALL PRIVILEGES
4. ✅ Upload all files
5. ✅ Run `composer install` (or upload vendor folder)
6. ✅ Set permissions (755)

### **THEN:**
7. ✅ Visit `/install`
8. ✅ Follow wizard
9. ✅ Enter database credentials
10. ✅ Complete installation

**FOLLOW THIS CHECKLIST = SUCCESS!** ✅

**SKIP STEPS = FAILURE!** ❌

---

**PRINT THIS CHECKLIST AND FOLLOW IT STEP BY STEP!** 📋✅
