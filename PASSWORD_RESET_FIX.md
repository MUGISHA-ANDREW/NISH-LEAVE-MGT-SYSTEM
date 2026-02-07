# Password Reset Email Fix - COMPLETE GUIDE

## Issues Fixed ✅

1. **Added missing encryption setting to SMTP configuration in `config/mail.php`**
   - Added `'encryption' => env('MAIL_ENCRYPTION', 'tls'),` to the SMTP mailer

## SendGrid Setup Requirements ⚠️

**CRITICAL**: For SendGrid to send emails, you MUST verify your sender email address:

### Step 1: Verify Sender Identity in SendGrid

1. **Login to SendGrid Dashboard**: https://app.sendgrid.com/
   
2. **Navigate to Settings → Sender Authentication**
   
3. **Option A: Single Sender Verification** (Quickest)
   - Click "Verify a Single Sender"
   - Enter: `andrewmugisha699@gmail.com`
   - Fill in sender details (name, address, etc.)
   - Check your Gmail inbox for verification email
   - Click the verification link
   
4. **Option B: Domain Authentication** (Recommended for production)
   - Verify your entire domain (if you own andrewmugisha699@gmail.com domain)
   - Follow SendGrid's DNS record setup instructions

### Step 2: Redeploy to Railway

Since we updated the `config/mail.php` file, you need to redeploy:

```bash
# Commit the changes
git add config/mail.php
git commit -m "Fix: Add encryption setting to SMTP mail config"
git push origin main
```

Railway will automatically redeploy your application.

### Step 3: Verify Railway Environment Variables

Make sure these variables are set correctly in your Railway project:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=<your-sendgrid-api-key>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=andrewmugisha699@gmail.com
MAIL_FROM_NAME="Nish Auto Limited - Leave Management"
```

### Step 4: Verify password_resets Table Exists

Connect to your Railway database and verify the table exists:

```bash
# In Railway dashboard, open your MySQL database service
# Click "Query" tab and run:
SHOW TABLES LIKE 'password_resets';

# If it doesn't exist, run the migration:
# In Railway project settings, add a one-time command:
php artisan migrate --force
```

## Testing the Password Reset

### Test Locally First (Optional)

1. Update your local `.env` with the same mail settings
2. Run migrations: `php artisan migrate`
3. Start server: `php artisan serve`
4. Visit: `http://localhost:8000/forgot-password`
5. Enter a valid email from your database
6. Check if email is sent

### Test on Railway

1. Visit: `https://nish-leave-mgt-system-production.up.railway.app/forgot-password`
2. Enter a valid email address from your users table
3. Click "Send Reset Link"
4. Check the email inbox (and spam folder)

## Troubleshooting

### If emails still don't send:

1. **Check Railway Logs**:
   ```bash
   # In Railway dashboard, go to your service → Deployments → View Logs
   # Look for errors containing "SMTP" or "Mail"
   ```

2. **Verify SendGrid API Key is Valid**:
   - Login to SendGrid
   - Go to Settings → API Keys
   - Verify your API key hasn't expired
   - If needed, create a new one with "Mail Send" permissions

3. **Check SendGrid Activity Feed**:
   - Go to Activity Feed in SendGrid dashboard
   - See if send attempts are being made
   - Check for bounce/spam reports

4. **Test SendGrid API Key**:
   Create a test file `test-sendgrid.php` in your project root:
   
   ```php
   <?php
   require __DIR__.'/vendor/autoload.php';
   
   use Illuminate\Support\Facades\Mail;
   
   $app = require_once __DIR__.'/bootstrap/app.php';
   $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
   
   try {
       Mail::raw('Test email from Laravel', function ($message) {
           $message->to('andrewmugisha699@gmail.com')
                   ->subject('Test Email');
       });
       
       echo "Email sent successfully!\n";
   } catch (\Exception $e) {
       echo "Error: " . $e->getMessage() . "\n";
   }
   ```
   
   Run on Railway: `php test-sendgrid.php`

### Common SendGrid Errors:

- **"Sender identity is not verified"**: Complete Step 1 above
- **"Authentication failed"**: Check your API key is correct
- **"Connection timeout"**: Check port 587 is allowed by Railway
- **"TLS error"**: Encryption setting is now fixed in config

## Alternative: Use Gmail SMTP (If SendGrid doesn't work)

If SendGrid continues to have issues, you can use Gmail:

1. Enable 2-Factor Authentication on your Gmail
2. Generate an "App Password":
   - Google Account → Security → 2-Step Verification → App Passwords
   - Create password for "Mail"
   
3. Update Railway variables:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=andrewmugisha699@gmail.com
   MAIL_PASSWORD=<your-16-digit-app-password>
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=andrewmugisha699@gmail.com
   MAIL_FROM_NAME="Nish Auto Limited - Leave Management"
   ```

## What Was Changed

### File: `config/mail.php`

**Before:**
```php
'smtp' => [
    'transport' => 'smtp',
    'scheme' => env('MAIL_SCHEME'),
    'url' => env('MAIL_URL'),
    'host' => env('MAIL_HOST', '127.0.0.1'),
    'port' => env('MAIL_PORT', 2525),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'timeout' => 30,
    'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
],
```

**After:**
```php
'smtp' => [
    'transport' => 'smtp',
    'scheme' => env('MAIL_SCHEME'),
    'url' => env('MAIL_URL'),
    'host' => env('MAIL_HOST', '127.0.0.1'),
    'port' => env('MAIL_PORT', 2525),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),  // ← ADDED THIS LINE
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'timeout' => 30,
    'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
],
```

## Summary

The fix is complete! The main issue was the missing `encryption` setting in the SMTP mailer configuration. 

**Next Steps:**
1. ✅ Verify sender email in SendGrid (MOST IMPORTANT)
2. ✅ Commit and push the changes
3. ✅ Wait for Railway to redeploy
4. ✅ Test password reset functionality
5. ✅ Check logs if issues persist

Your password reset should now work correctly!
