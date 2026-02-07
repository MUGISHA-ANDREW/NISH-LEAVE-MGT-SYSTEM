# 📧 Gmail API Setup for Railway (SMTP Alternative)

Railway **blocks Gmail SMTP** (ports 587 & 465), but we can use **Gmail API** instead. It uses HTTPS (port 443) which is never blocked.

## ✅ Benefits
- Works on Railway (uses HTTPS, not SMTP)
- No port blocking issues
- More reliable than SMTP  
- Official Gmail integration
- Still uses your Gmail account

---

## 🚀 Setup (15 minutes)

### Step 1: Run the Setup Script

I've created an interactive script to help you:

```powershell
php gmail-setup.php
```

The script will guide you through:
1. Creating a Google Cloud project
2. Enabling Gmail API
3. Getting OAuth credentials
4. Generating a refresh token

### Step 2: Follow the Script Instructions

The script will:
- Give you step-by-step instructions
- Generate the authorization URL
- Help you get the refresh token
- Show you exactly what to add to .env and Railway

### Step 3: Update Local .env

Add the variables shown by the script to your `.env` file.

Example:
```env
MAIL_MAILER=gmail
GMAIL_CLIENT_ID="123456789-abc.apps.googleusercontent.com"
GMAIL_CLIENT_SECRET="YOUR_SECRET"
GMAIL_REFRESH_TOKEN="1//abc123"
MAIL_FROM_ADDRESS="andrewmugisha699@gmail.com"
MAIL_FROM_NAME="Nish Auto Limited"
```

### Step 4: Test Locally

```powershell
php artisan config:clear
php test-email-quick.php
```

### Step 5: Deploy to Railway

1. Add the same variables to **Railway Dashboard** > **Variables**
2. Also add/keep these essential variables:
   ```
   APP_ENV=production
   APP_DEBUG=false
   LOG_CHANNEL=stderr
   ```
3. Git push your config changes:
   ```powershell
   git add .
   git commit -m "Add Gmail API support"
   git push
   ```
4. Wait for Railway to deploy

---

## 🔍 Troubleshooting

### "Access blocked: This app's request is invalid"
- Make sure you added `http://localhost` to Authorized redirect URIs
- Try using `http://localhost:8080` as well

### "No refresh token received"
- The auth URL must include `prompt=consent`
- Try revoking access at https://myaccount.google.com/permissions and start over

### "Invalid refresh token"
- Regenerate the token using `php gmail-setup.php`
- Make sure you copied the entire token (no spaces or line breaks)

### Email still not sending on Railway
- Check Railway logs for specific errors
- Verify all variables are set correctly
- Make sure `MAIL_MAILER=gmail` (not `smtp`)

---

## 📝 Important Notes

1. **OAuth Consent Screen**: You can keep it in "Testing" mode - it works fine for your purposes
2. **Token Expiry**: The refresh token doesn't expire unless you revoke access
3. **Multiple Users**: The refresh token is tied to one Gmail account (andrewmugisha699@gmail.com)
4. **Security**: Keep your client secret and refresh token private (don't commit to public repos)

---

## ⚡ Alternative: Use SendGrid (Simpler)

If Gmail API setup feels too complex, SendGrid is simpler:
1. Sign up at sendgrid.com (free)
2. Get API key
3. Update 5 Railway variables (see QUICK_EMAIL_FIX.md)

But Gmail API gives you true Gmail sending, not "via SendGrid".
