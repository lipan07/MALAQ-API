# Fix Large File Upload Limits

## Problem
Laravel's `ValidatePostSize` middleware is rejecting large file uploads with "POST data is too large" error.

## Solutions Applied

### 1. Custom Middleware (Already Added)
- Created `AllowLargeUploads` middleware
- Applied to `/backblaze/upload-video` route
- Increases PHP limits for that specific route

### 2. Server Configuration

You need to update your server's PHP configuration. Choose the method based on your server setup:

#### For Apache with mod_php:
The `.htaccess` file has been updated. Make sure your Apache server allows `.htaccess` overrides:
```apache
AllowOverride All
```

#### For PHP-FPM / Nginx:
Update your `php.ini` file (usually in `/etc/php/8.x/fpm/php.ini` or `/etc/php/8.x/cli/php.ini`):

```ini
upload_max_filesize = 1024M
post_max_size = 1024M
max_execution_time = 3600
max_input_time = 3600
memory_limit = 512M
```

Then restart PHP-FPM:
```bash
sudo systemctl restart php8.x-fpm
# or
sudo service php8.x-fpm restart
```

#### For Nginx:
Also update Nginx configuration (usually `/etc/nginx/nginx.conf` or site config):

```nginx
client_max_body_size 1024M;
```

Then restart Nginx:
```bash
sudo systemctl restart nginx
# or
sudo service nginx restart
```

### 3. Check Current Limits

To check your current PHP limits, create a test file:

**File: `Reuse-Backend/public/phpinfo.php`** (temporary, delete after checking)
```php
<?php
phpinfo();
```

Visit: `https://your-domain.com/phpinfo.php`

Look for:
- `upload_max_filesize`
- `post_max_size`
- `max_execution_time`
- `memory_limit`

### 4. Verify Configuration

After making changes, verify with:
```bash
php -i | grep -E "upload_max_filesize|post_max_size|max_execution_time|memory_limit"
```

## Quick Fix (If Still Not Working)

If the above doesn't work, you can temporarily disable `ValidatePostSize` for the upload route by modifying `app/Http/Kernel.php`:

```php
protected $middleware = [
    // ... other middleware
    // \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, // Comment this out
    // ... rest
];
```

**Note:** Only do this if you've properly configured PHP limits, as it removes an important security check.

## Testing

After configuration, test with:
```bash
curl -X POST https://your-domain.com/api/backblaze/upload-video \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "video=@test-video.mp4" \
  -F "fileName=test.mp4"
```

