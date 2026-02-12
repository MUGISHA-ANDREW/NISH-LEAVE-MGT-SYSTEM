# 🚂 Railway Password Reset Setup Checklist

Use this checklist to ensure your password reset system works on Railway.

## ✅ Pre-Deployment Checklist

### 1. Local Testing
- [ ] Password reset works locally
- [ ] `password_resets` table exists in local database
- [ ] Email sending works (even with Mailtrap)
- [ ] All views display correctly
- [ ] Token validation works
- [ ] Password update successfully

### 2. Code Verification
- [ ] `ForgotPasswordController.php` exists and has all methods
- [ ] Email template exists at `resources/views/emails/password-reset.blade.php`
- [ ] Routes defined in `routes/web.php`
- [ ] Migration file exists: `*_create_password_resets_table.php`

---

## 🔧 Railway Configuration Steps

### Step 1: Set Environment Variables

Go to Railway Dashboard → Your Project → Variables

#### ✅ Core Variables
```bash
APP_NAME="Nish Auto Limited"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://nish-leave-mgt-system-production.up.railway.app
```

#### ✅ Generate and Set APP_KEY
Run locally:
```bash
php artisan key:generate --show
```

Copy the output and add to Railway:
```bash
APP_KEY=base64:your-generated-key-here
```

#### ✅ Mail Configuration (Choose One Option)

**Option A: Gmail (Production)**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Option B: Mailtrap (Testing)**
```bash
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@nishauto.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Option C: SendGrid (Production)**
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@nishauto.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 2: Run Database Migrations

After setting variables, run migrations on Railway:

**Method 1: Railway CLI**
```bash
railway link
railway run php artisan migrate --force
```

**Method 2: Add to Build Phase**

Update `nixpacks.toml`:
```toml
[phases.build]
cmds = [
  'php artisan config:clear',
  'php artisan cache:clear',
  'php artisan migrate --force'
]
```

Then push to trigger new deployment.

### Step 3: Verify Migration

```bash
railway run php artisan migrate:status
```

Look for: ✅ `2026_02_06_094332_create_password_resets_table.php` - Status: Ran

### Step 4: Clear Cache

```bash
railway run php artisan config:clear
railway run php artisan cache:clear
railway run php artisan view:clear
```

---

## 🧪 Testing on Railway

### Test 1: Check Configuration
```bash
railway run php artisan tinker
```

Then run:
```php
config('app.key')        // Should show the key
config('mail.default')   // Should be 'smtp'
config('mail.mailers.smtp.host')  // Should show SMTP host
Schema::hasTable('password_resets')  // Should return true
```

### Test 2: Test Email Sending
```bash
railway run php artisan tinker
```

Then:
```php
use Illuminate\Support\Facades\Mail;
Mail::raw('Test from Railway', function($msg) {
    $msg->to('your-email@example.com')->subject('Railway Test');
});
```

Check your inbox. If you get the email, SMTP is working!

### Test 3: Full Flow Test

1. Go to: `https://your-railway-app.up.railway.app/forgot-password`
2. Enter a valid user email
3. Check inbox for reset email
4. Click the reset link
5. Enter new password
6. Should redirect to login
7. Login with new password

---

## 🐛 Troubleshooting

### Problem: 500 Error on Forgot Password Page

**Check:**
```bash
railway logs --follow
```

**Common Causes:**
1. ❌ APP_KEY not set
2. ❌ Database migration not run
3. ❌ Mail configuration missing

**Solutions:**
```bash
# Check APP_KEY
railway run php artisan tinker
>>> config('app.key')

# Check migration
railway run php artisan migrate:status

# Check mail config
railway run php artisan tinker
>>> config('mail.mailers.smtp')
```

### Problem: Token Invalid or Expired

**Causes:**
- Token older than 60 minutes
- Token not in database
- Database connection issue

**Solution:**
Request a new password reset link. Tokens expire after 60 minutes for security.

### Problem: Email Not Sending

**Check Mail Logs:**
```bash
railway logs | grep -i mail
railway logs | grep -i "password reset"
```

**Test SMTP Connection:**
```bash
railway run php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;

// Test basic email
try {
    Mail::raw('Test', function($m) {
        $m->to('test@example.com')->subject('Test');
    });
    echo "Email sent successfully!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

**Common Solutions:**
1. For Gmail: Use App Password (not regular password)
2. Check MAIL_HOST, MAIL_PORT are correct
3. Check MAIL_USERNAME and MAIL_PASSWORD have no extra spaces
4. Try Mailtrap first to isolate the issue

### Problem: "No Application Encryption Key"

**Solution:**
```bash
# Generate locally
php artisan key:generate --show

# Copy output and add to Railway
# In Railway Dashboard → Variables:
APP_KEY=base64:copied-key-here

# Restart deployment
```

### Problem: "SQLSTATE[42S02]: Base table or view not found"

**Solution:**
```bash
railway run php artisan migrate --force
```

If migration exists but not running:
```bash
# Check migration files
railway run ls -la database/migrations | grep password

# Force re-run specific migration
railway run php artisan migrate:refresh --path=/database/migrations/2026_02_06_094332_create_password_resets_table.php --force
```

---

## 📊 Health Check Script

Run this to verify everything is working:

```bash
# Download and run the test script
php test-password-reset-config.php
```

On Railway:
```bash
railway run php test-password-reset-config.php
```

---

## 🔐 Gmail App Password Setup

If using Gmail, you MUST use an App Password:

### Steps:
1. Go to [Google Account](https://myaccount.google.com/)
2. Click "Security"
3. Enable "2-Step Verification" (if not already)
4. Go back to Security
5. Click "App passwords"
6. Select "Mail" and your device
7. Generate password
8. Copy the 16-character password (no spaces)
9. Use this as `MAIL_PASSWORD` in Railway

---

## ✅ Final Checklist

Before marking as "DONE", verify:

- [ ] Railway variables all set correctly
- [ ] APP_KEY generated and set
- [ ] Database migrations ran successfully
- [ ] `password_resets` table exists
- [ ] Email configuration tested
- [ ] Test email received successfully
- [ ] Full password reset flow works on Railway
- [ ] Can login with new password
- [ ] Error logs are clean
- [ ] APP_DEBUG set to `false` in production

---

## 🎯 Quick Command Reference

```bash
# Link to Railway project
railway link

# View variables
railway vars

# Set a variable
railway vars set VARIABLE_NAME=value

# Run migrations
railway run php artisan migrate --force

# Check migration status
railway run php artisan migrate:status

# Clear cache
railway run php artisan config:clear

# View logs
railway logs --follow

# Run Tinker
railway run php artisan tinker

# Test configuration
railway run php test-password-reset-config.php
```

---

## 📞 Still Having Issues?

1. Check Railway logs: `railway logs --follow`
2. Run the test script: `php test-password-reset-config.php`
3. Enable debug temporarily: `APP_DEBUG=true`
4. Check the comprehensive guide: `PASSWORD_RESET_COMPLETE_GUIDE.md`
5. Verify all steps in this checklist

---

**Most Common Fix:**
```bash
# 1. Set APP_KEY
php artisan key:generate --show
# Copy to Railway

# 2. Run migrations
railway run php artisan migrate --force

# 3. Clear cache
railway run php artisan config:clear

# 4. Test
Visit: https://your-app.railway.app/forgot-password
```

Good luck! 🚀
