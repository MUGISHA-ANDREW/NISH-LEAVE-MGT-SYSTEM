# 🚨 Quick Fix Guide - Password Reset 500 Error

**If you're getting a 500 error, start here!**

## ⚡ Most Common Fix (Do This First!)

### 1. Set APP_KEY on Railway

```bash
# On your local machine:
php artisan key:generate --show
```

Copy the output (will look like: `base64:ABC123...`)

Then in Railway:
1. Go to your project dashboard
2. Click "Variables"
3. Add or update:
   ```
   APP_KEY=base64:ABC123...paste-your-key-here
   ```
4. Redeploy

### 2. Run Migrations on Railway

```bash
railway run php artisan migrate --force
```

### 3. Clear Cache on Railway

```bash
railway run php artisan config:clear
railway run php artisan cache:clear
```

### 4. Test Again

Visit: `https://your-railway-url.up.railway.app/forgot-password`

---

## 🔍 If Still Not Working...

### Check Railway Logs

```bash
railway logs --follow
```

Then visit the forgot-password page and watch for errors.

### Common Error Messages & Fixes

#### ❌ "No application encryption key has been specified"

**Fix:**
```bash
# Generate key locally
php artisan key:generate --show

# Copy output and set in Railway variables
APP_KEY=base64:your-key-here
```

#### ❌ "Base table or view not found: password_resets"

**Fix:**
```bash
railway run php artisan migrate --force
```

#### ❌ "Connection could not be established with host"

**Fix:** Check mail configuration in Railway variables:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

For Gmail: Use an [App Password](https://myaccount.google.com/apppasswords)

#### ❌ "Class 'App\Http\Controllers\Auth\ForgotPasswordController' not found"

**Fix:**
```bash
railway run composer dump-autoload
```

---

## 📧 Test Email Configuration

### Test 1: Check if SMTP works

```bash
php test-email-simple.php your-email@example.com
```

On Railway:
```bash
railway run php test-email-simple.php your-email@example.com
```

### Test 2: Check full config

```bash
php test-password-reset-config.php
```

---

## 🎯 Gmail Setup (Most Common)

### Step-by-Step:

1. **Enable 2-Step Verification**
   - Go to [Google Account Security](https://myaccount.google.com/security)
   - Turn on 2-Step Verification

2. **Generate App Password**
   - Still in Security settings
   - Click "App passwords"
   - Select "Mail" and your device
   - Click "Generate"
   - Copy the 16-character password (no spaces)

3. **Set in Railway Variables**
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=xxxx xxxx xxxx xxxx
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your-email@gmail.com
   MAIL_FROM_NAME="Nish Auto Limited"
   ```

4. **Test**
   ```bash
   railway run php test-email-simple.php test@example.com
   ```

---

## 🧪 Testing Order

Follow this exact order:

### 1. Local Testing
```bash
# Test configuration
php test-password-reset-config.php

# Test email
php test-email-simple.php your-email@example.com

# Test full flow
php artisan serve
# Visit: http://localhost:8000/forgot-password
```

### 2. Railway Testing
```bash
# Link to Railway
railway link

# Set variables (if not done)
railway vars set APP_KEY=base64:your-key
railway vars set MAIL_MAILER=smtp
railway vars set MAIL_HOST=smtp.gmail.com
# ... etc

# Run migration
railway run php artisan migrate --force

# Clear cache
railway run php artisan config:clear

# Test
Visit: https://your-app.railway.app/forgot-password
```

---

## 📋 Required Railway Variables Checklist

```bash
✅ APP_NAME
✅ APP_ENV=production
✅ APP_KEY=base64:...
✅ APP_DEBUG=false
✅ APP_URL=https://your-railway-url

✅ DB_CONNECTION=mysql
✅ DB_HOST=...
✅ DB_PORT=3306
✅ DB_DATABASE=railway
✅ DB_USERNAME=root
✅ DB_PASSWORD=...

✅ MAIL_MAILER=smtp
✅ MAIL_HOST=smtp.gmail.com
✅ MAIL_PORT=587
✅ MAIL_USERNAME=your-email@gmail.com
✅ MAIL_PASSWORD=your-app-password
✅ MAIL_ENCRYPTION=tls
✅ MAIL_FROM_ADDRESS=your-email@gmail.com
✅ MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🔧 Debug Commands

```bash
# Check APP_KEY is set
railway run php artisan tinker
>>> config('app.key')

# Check database connection
railway run php artisan tinker
>>> DB::connection()->getPdo();

# Check if password_resets table exists
railway run php artisan tinker
>>> Schema::hasTable('password_resets')

# Check mail config
railway run php artisan tinker
>>> config('mail.mailers.smtp')

# View migration status
railway run php artisan migrate:status

# Test email
railway run php test-email-simple.php test@example.com
```

---

## 🎭 Emergency Debug Mode

If you need to see the exact error:

1. **Enable Debug (Temporarily!)**
   ```
   APP_DEBUG=true
   ```

2. **Visit the page** and you'll see the full error

3. **Take a screenshot** of the error

4. **Turn debug OFF immediately**
   ```
   APP_DEBUG=false
   ```

⚠️ **NEVER leave APP_DEBUG=true in production!**

---

## ✅ Success Checklist

- [ ] APP_KEY is set on Railway
- [ ] Migrations ran successfully
- [ ] password_resets table exists
- [ ] MAIL settings are configured
- [ ] Test email received successfully
- [ ] /forgot-password page loads without 500 error
- [ ] Full password reset flow works
- [ ] Can login with new password

---

## 📞 Need More Help?

1. Run: `php test-password-reset-config.php`
2. Check: `railway logs --follow`
3. Read: `PASSWORD_RESET_COMPLETE_GUIDE.md`
4. Read: `RAILWAY_SETUP_CHECKLIST.md`

---

## 💡 Pro Tips

1. **Test with Mailtrap first** - easier to debug than Gmail
2. **Always clear cache** after changing .env variables
3. **Check Railway logs** for detailed errors
4. **Use the test scripts** - they'll tell you what's wrong
5. **One step at a time** - fix database, then mail, then test

---

**Most issues are fixed by:**
1. Setting APP_KEY ✅
2. Running migrations ✅  
3. Configuring mail properly ✅

Try those first! 🚀
