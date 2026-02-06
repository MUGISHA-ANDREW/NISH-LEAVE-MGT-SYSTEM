# 🐛 Password Reset Debugging Guide

## ✅ What Was Fixed

I've enhanced the `ForgotPasswordController.php` with comprehensive debugging:

### Features Added:
- ✅ **Separate error handling** for database vs email failures
- ✅ **Detailed logging** at every step of the process
- ✅ **Specific error messages** in debug mode
- ✅ **Stack traces** logged for deep debugging
- ✅ **Graceful degradation** when email fails

---

## 🔍 How to Debug

### Step 1: Enable Debug Mode (Temporarily)

In Railway, set:
```
APP_DEBUG=true
```

This will show detailed error messages on the page. **Remember to set it back to `false` after debugging!**

### Step 2: Run Diagnostic Script

Locally:
```powershell
php test-password-reset.php
```

On Railway:
```powershell
railway run php test-password-reset.php
```

This checks:
- ✅ password_resets table exists
- ✅ Email configuration
- ✅ View files exist
- ✅ Routes are registered
- ✅ Sample users in database

### Step 3: Check Logs

View real-time Railway logs:
```powershell
railway logs --follow
```

Or on Railway Dashboard:
- Go to your service
- Click "Deployments"
- Click latest deployment
- View logs

---

## 📊 What the Logs Show

When someone tries to reset their password, you'll see:

### Success Flow:
```
[INFO] Password reset requested for email: user@example.com
[INFO] Token stored successfully in password_resets table
[INFO] Password reset email sent successfully to: user@example.com
```

### Database Error:
```
[ERROR] Database error when storing password reset token: [error details]
```
**Fix:** Run `railway run php artisan migrate --force`

### Email Error:
```
[INFO] Token stored successfully in password_resets table
[ERROR] Email sending error: [error details]
```
**Fix:** Check MAIL_* environment variables

---

## 🔧 Common Issues & Solutions

### Issue 1: "password_resets table doesn't exist"

**Symptoms:**
- 500 error when submitting email
- Log shows: "Database error when storing password reset token"

**Solution:**
```powershell
# Check if table exists
railway run php artisan migrate:status

# Run migrations
railway run php artisan migrate --force

# Verify
railway run php artisan tinker
DB::table('password_resets')->count();
exit
```

---

### Issue 2: "Failed to send email"

**Symptoms:**
- Success message but no email received
- Log shows: "Email sending error"

**Solution:**

**For Gmail:**
1. Go to Railway Dashboard → Variables
2. Set these variables:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youremail@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@gmail.com
MAIL_FROM_NAME=Leave Management System
```

3. Generate App Password:
   - Go to: https://myaccount.google.com/apppasswords
   - Create new app password
   - Use that 16-character password (no spaces)

**For Testing (Mailtrap):**
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
```

---

### Issue 3: "Email validation failed"

**Symptoms:**
- Error: "The email must be a valid email address"
- Or: "The selected email is invalid"

**Solution:**
The email doesn't exist in your database.

**Check existing emails:**
```powershell
railway run php artisan tinker
User::all(['email', 'first_name', 'last_name']);
exit
```

---

### Issue 4: Token doesn't work on reset page

**Symptoms:**
- Click link in email
- Get "Invalid reset token" error

**Possible Causes:**
1. **Token expired** (60 minutes)
   - Request a new reset link
2. **Token already used**
   - Request a new reset link
3. **Email parameter missing**
   - Link might be malformed

**Debug:**
```powershell
# Check tokens in database
railway run php artisan tinker
DB::table('password_resets')->get();
exit
```

---

## 📋 Step-by-Step Testing

### Test Password Reset End-to-End:

1. **Ensure migrations ran:**
   ```powershell
   railway run php artisan migrate:status
   ```
   Look for `2026_02_06_094332_create_password_resets_table` with status "Ran"

2. **Get a valid test email:**
   ```powershell
   railway run php artisan tinker
   User::first()->email
   exit
   ```

3. **Visit forgot password page:**
   - Go to: `https://your-app.up.railway.app/forgot-password`

4. **Enter the test email and submit**

5. **Watch logs in real-time:**
   ```powershell
   railway logs --follow
   ```

6. **Look for:**
   - "Password reset requested for email: ..."
   - "Token stored successfully"
   - "Password reset email sent successfully" OR "Email sending error"

7. **If using log driver,** find email in logs:
   ```powershell
   railway logs | grep -A 50 "Password Reset Request"
   ```

8. **If using SMTP,** check your email inbox and spam

9. **Click the reset link**

10. **Enter new password twice**

11. **Submit and verify success**

---

## 🧪 Test Locally Before Deploying

1. **Set up local .env:**
   ```env
   DB_CONNECTION=mysql
   DB_DATABASE=your_database
   MAIL_MAILER=log
   APP_DEBUG=true
   ```

2. **Run migrations:**
   ```powershell
   php artisan migrate
   ```

3. **Start server:**
   ```powershell
   php artisan serve
   ```

4. **Test password reset:**
   - Visit: http://localhost:8000/forgot-password
   - Enter a valid email
   - Check: storage/logs/laravel.log for the email content

5. **If it works locally, deploy:**
   ```powershell
   git add .
   git commit -m "Add comprehensive password reset debugging"
   git push
   ```

---

## 📨 Email Configuration Examples

### Gmail (Recommended for production):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=yourapp@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=yourapp@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Mailtrap (Best for testing):
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### SendGrid:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=verified@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Log Driver (Development only):
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Emails will be written to `storage/logs/laravel.log`

---

## 🎯 Quick Commands Reference

```powershell
# View logs
railway logs
railway logs --follow

# Run migrations
railway run php artisan migrate --force

# Check migration status
railway run php artisan migrate:status

# Clear cache
railway run php artisan config:clear
railway run php artisan cache:clear

# Check database
railway run php artisan tinker
DB::table('password_resets')->count();
User::pluck('email');
exit

# Run diagnostic script
railway run php test-password-reset.php

# Test email configuration
railway run php artisan tinker
Mail::raw('Test email', function($msg) { $msg->to('your@email.com')->subject('Test'); });
exit
```

---

## ✅ Final Checklist

Before marking as fixed:

### Database:
- [ ] Migrations ran successfully
- [ ] `password_resets` table exists
- [ ] Table has correct schema (email, token, created_at)

### Email:
- [ ] MAIL_* variables set in Railway
- [ ] Using Gmail App Password (not regular password)
- [ ] FROM address is valid
- [ ] Test email can be sent

### Code:
- [ ] ForgotPasswordController updated
- [ ] All views exist (forgot-password, reset-password, email template)
- [ ] Routes registered correctly

### Testing:
- [ ] Can access /forgot-password
- [ ] Can submit form without 500 error
- [ ] Sees success or specific error message
- [ ] Email received or logged
- [ ] Can click link and access reset page
- [ ] Can reset password successfully
- [ ] Can login with new password

---

## 🆘 Still Not Working?

1. **Enable debug mode** and screenshot the error
2. **Run diagnostic:** `railway run php test-password-reset.php`
3. **Copy recent logs:** `railway logs > debug-logs.txt`
4. **Share:** Error message, diagnostic output, and logs

The logs will show exactly where it's failing!
