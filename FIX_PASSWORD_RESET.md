# 🔧 Fix Password Reset Error (500)

## Problem
The `password_resets` table doesn't exist in your production database, causing a 500 error when users try to reset their password.

## ✅ Quick Solution (EASIEST METHOD)

Your `nixpacks.toml` is already configured to run migrations automatically. You just need to **trigger a fresh deployment**:

### Method 1: Push to Git (Recommended)

```powershell
git add .
git commit -m "Fix password reset error handling"
git push
```

Railway will automatically detect the push and redeploy, running migrations.

### Method 2: Manual Redeploy on Railway

1. Go to Railway Dashboard
2. Click your project
3. Click your service
4. Click **"Deploy"** or **"Redeploy"**

---

## Alternative: Run Migration Manually

If you prefer to run the migration manually without redeploying:

```powershell
# Install Railway CLI if you haven't
npm i -g @railway/cli

# Login to Railway
railway login

# Link your project (only needed once)
railway link

# Run migrations
railway run php artisan migrate --force
```

---

## Verify Email Configuration

Make sure these environment variables are set in Railway:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Leave Management System"
```

**For Gmail:**
- You need to use an "App Password", not your regular password
- Enable 2-factor authentication on your Google account
- Generate an App Password at: https://myaccount.google.com/apppasswords

---

## What Was Fixed in the Code

I updated [ForgotPasswordController.php](app/Http/Controllers/auth/ForgotPasswordController.php) to:
- ✅ Wrap all database operations in try-catch (not just email)
- ✅ Log errors for debugging
- ✅ Show user-friendly error messages
- ✅ Prevent 500 errors from crashing the page

---

## After Deployment

1. **Test forgot password** with a valid email address
2. **Check for success message** or error
3. **Check email** (including spam folder)
4. **View logs** if issues persist: `railway logs`

---

## Troubleshooting

### Still getting 500 error?

Check Railway logs:
```powershell
railway logs
```

Look for database or email errors.

### Email not sending?

1. Verify email environment variables are set
2. Make sure you're using an App Password for Gmail (not regular password)
3. Check spam folder
4. View logs: `railway logs`

### Migration not running?

Check deployment logs on Railway:
1. Go to Railway Dashboard > Your Service > Deployments
2. Click the latest deployment
3. Look for "Running migrations" in the logs
4. Verify "Migration table created successfully" or similar message

---

**Quick Command Reference:**
```powershell
# View logs
railway logs

# Clear cache
railway run php artisan config:clear
railway run php artisan cache:clear

# Run migrations
railway run php artisan migrate --force

# Check migration status  
railway run php artisan migrate:status
```
