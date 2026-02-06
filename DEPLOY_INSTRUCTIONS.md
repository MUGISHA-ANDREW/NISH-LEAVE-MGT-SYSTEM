# Quick Setup Guide for Password Reset Email

## What I Fixed

1. ✅ Added proper error handling to catch database and email errors
2. ✅ Added detailed error logging
3. ✅ The controller will now show the exact error message

## Deploy Now

### Option 1: Use the batch file (Easiest)

```powershell
.\deploy.bat
```

### Option 2: Manual commands

```powershell
git add .
git commit -m "Fix password reset error handling"
git push
```

## After Deployment

### 1. Test the forgot password page:

Visit: https://nish-leave-mgt-system-production.up.railway.app/forgot-password

### 2. What will happen:

- **If password_resets table missing:** You'll see an error message with details
- **If email config missing:** Email won't send (but won't crash)
- **If everything is OK:** You'll see "We have emailed your password reset link!"

### 3. Check Railway Logs:

```powershell
railway logs
```

Look for errors like:
- `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'railway.password_resets' doesn't exist`
- `Swift_TransportException`

## Configure Email (IMPORTANT!)

### For Gmail:

Go to Railway Dashboard → Your Service → Variables → Add these:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=Leave Management System
```

**How to get Gmail App Password:**
1. Go to https://myaccount.google.com/apppasswords
2. Sign in
3. Create new app password
4. Copy the 16-character password (remove spaces)
5. Use it as MAIL_PASSWORD

### For Testing (Mailtrap - Recommended):

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_FROM_ADDRESS=noreply@example.com
```

Sign up free at: https://mailtrap.io

## Run Migrations on Railway

If you see "table doesn't exist" error:

```powershell
railway run php artisan migrate --force
```

Or check your `nixpacks.toml` - it should automatically run migrations.

## Test Locally First (Optional)

```powershell
# Start local server
php artisan serve

# Visit http://localhost:8000/forgot-password
# Enter a valid email from your database
# Check storage/logs/laravel.log for the email content
```

## Troubleshooting

### Error: "Table 'railway.password_resets' doesn't exist"

**Fix:**
```powershell
railway run php artisan migrate --force
```

### Error: "Connection could not be established with host smtp.gmail.com"

**Fix:**
1. Check MAIL_USERNAME and MAIL_PASSWORD are set
2. Make sure you're using App Password (not regular password)
3. Check MAIL_PORT is 587

### Error: "The email field is required"

**Fix:** You need to enter an email address in the form

### Error: "The selected email is invalid"

**Fix:** The email doesn't exist in your database. Use a valid email from:
```powershell
railway run php artisan tinker
User::pluck('email');
exit
```

## Expected Flow

1. User visits `/forgot-password`
2. Enters their email
3. Clicks "Send Reset Link"
4. System stores token in `password_resets` table
5. System sends email with reset link
6. User clicks link in email
7. Opens `/reset-password/{token}?email=...`
8. Enters new password
9. Password is updated
10. Token is deleted
11. User can login with new password

## Quick Check Commands

```powershell
# Check if running
railway status

# View logs
railway logs

# Run migrations
railway run php artisan migrate --force

# Check database
railway run php artisan tinker
DB::table('password_resets')->count();
User::count();
exit

# Test email config
railway run php test-password-reset.php
```
