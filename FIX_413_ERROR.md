# Fix 413 "Request Entity Too Large" Error

## 🔍 What This Error Means

The 413 error means your video file is too large for the server to accept. This is controlled by multiple settings:

1. **PHP Settings** (`php.ini`)
2. **Web Server Settings** (Apache/Nginx)
3. **Laravel Validation** (already increased to 1GB)

## 🔧 Fix Steps

### Step 1: Increase PHP Upload Limits

#### For XAMPP (macOS):

1. **Find your PHP configuration file:**
   ```bash
   php --ini
   ```
   This will show the path to `php.ini`

2. **Edit `php.ini` file:**
   ```bash
   # Usually located at:
   # /Applications/XAMPP/xamppfiles/etc/php.ini
   
   # Or use:
   sudo nano /Applications/XAMPP/xamppfiles/etc/php.ini
   ```

3. **Find and update these settings:**
   ```ini
   ; Maximum allowed size for uploaded files
   upload_max_filesize = 1024M
   
   ; Maximum size of POST data
   post_max_size = 1024M
   
   ; Maximum execution time (for large uploads)
   max_execution_time = 3600
   
   ; Maximum input time
   max_input_time = 3600
   
   ; Memory limit
   memory_limit = 512M
   ```

4. **Restart Apache:**
   ```bash
   # In XAMPP Control Panel, stop and start Apache
   # Or via command line:
   sudo /Applications/XAMPP/xamppfiles/bin/apachectl restart
   ```

### Step 2: Increase Web Server Limits

#### For Apache (XAMPP):

1. **Edit Apache configuration:**
   ```bash
   sudo nano /Applications/XAMPP/xamppfiles/etc/httpd.conf
   ```

2. **Add or update these settings:**
   ```apache
   # Increase request body size limit
   LimitRequestBody 1073741824
   
   # Or add to .htaccess in your Laravel public folder:
   # php_value upload_max_filesize 1024M
   # php_value post_max_size 1024M
   # php_value max_execution_time 3600
   # php_value max_input_time 3600
   ```

3. **Or create/update `.htaccess` in `public` folder:**
   ```apache
   php_value upload_max_filesize 1024M
   php_value post_max_size 1024M
   php_value max_execution_time 3600
   php_value max_input_time 3600
   php_value memory_limit 512M
   ```

4. **Restart Apache**

### Step 3: Verify Settings

1. **Create a test PHP file** (`public/test-php-info.php`):
   ```php
   <?php
   phpinfo();
   ?>
   ```

2. **Visit:** `http://localhost/test-php-info.php`

3. **Check these values:**
   - `upload_max_filesize` - Should be 1024M
   - `post_max_size` - Should be 1024M
   - `max_execution_time` - Should be 3600
   - `memory_limit` - Should be 512M or higher

4. **Delete the test file** after checking (security)

### Step 4: Test Upload

Try uploading a video again. The 413 error should be resolved.

## 🚨 Alternative: Use Chunked Upload

If you still have issues or want to support very large files (>1GB), consider implementing chunked upload:

1. Split video into chunks on client
2. Upload chunks sequentially
3. Reassemble on server
4. Upload to YouTube

This is more complex but supports unlimited file sizes.

## 📋 Quick Checklist

- [ ] Updated `php.ini`:
  - [ ] `upload_max_filesize = 1024M`
  - [ ] `post_max_size = 1024M`
  - [ ] `max_execution_time = 3600`
  - [ ] `memory_limit = 512M`
- [ ] Updated Apache config or `.htaccess`
- [ ] Restarted Apache
- [ ] Verified settings with `phpinfo()`
- [ ] Tested video upload

## 🔍 Troubleshooting

### Still getting 413?

1. **Check if settings took effect:**
   ```bash
   php -i | grep upload_max_filesize
   php -i | grep post_max_size
   ```

2. **Check web server error logs:**
   ```bash
   tail -f /Applications/XAMPP/xamppfiles/logs/error_log
   ```

3. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Try smaller file first** to verify the fix works

### File size limits by component:

- **Laravel validation:** 1GB (1024000 KB) ✅
- **PHP upload_max_filesize:** Should be 1024M ✅
- **PHP post_max_size:** Should be 1024M ✅
- **Apache LimitRequestBody:** Should be 1GB+ ✅

## ✅ After Fixing

1. Restart Apache
2. Clear Laravel cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
3. Try uploading video again

