# Quick Setup Checklist

## ✅ What's Already Done:

1. ✅ Password reset migration created and run
2. ✅ ForgotPasswordController implemented
3. ✅ Password reset routes added
4. ✅ Beautiful password reset forms created
5. ✅ Professional email template designed
6. ✅ Email validation on registration (prevents duplicates)
7. ✅ "Forgot password?" link active on login page
8. ✅ Security features (token hashing, 60-min expiry)

## 🔧 What You Need to Do:

### Step 1: Configure Email (REQUIRED)

Edit your `.env` file and replace these placeholder values:

```env
MAIL_USERNAME=your-email@gmail.com      # ← Replace with your Gmail
MAIL_PASSWORD=your-app-password         # ← Replace with Gmail App Password
```

#### How to Get Gmail App Password:
1. Go to: https://myaccount.google.com/security
2. Enable "2-Step Verification"
3. Search for "App passwords"
4. Select "Mail" → "Other (Custom name)" → Type "Leave Management"
5. Click "Generate"
6. Copy the 16-character password
7. Paste in `.env` as `MAIL_PASSWORD`

### Step 2: Update Configuration

For production on Railway, update these too:
```env
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Your Company Name"
```

### Step 3: Apply Changes

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Test It!

**Option A: Use the test script**
```bash
php test-email-config.php
```

**Option B: Test on website**
1. Go to: https://your-app.up.railway.app
2. Click "Forgot password?"
3. Enter: admin@example.com
4. Check email inbox
5. Click reset link
6. Set new password

## 📧 Alternative Email Services

### For Testing (Recommended):
**Mailtrap** - Catches emails without sending to real inboxes
- Sign up: https://mailtrap.io (Free)
- Update `.env` with Mailtrap credentials
- View all test emails in Mailtrap inbox

### For Production:
- **SendGrid**: 100 emails/day free
- **Mailgun**: 100 emails/day free  
- **Amazon SES**: Pay as you go

See [EMAIL_SETUP_GUIDE.md](EMAIL_SETUP_GUIDE.md) for detailed instructions.

## 🔒 Email Registration Validation

**Already implemented!** The system prevents duplicate emails:

```php
// In UserController.php (line 155)
'email' => 'required|email|unique:users'
```

What this means:
- ✅ Users can't register with an existing email
- ✅ Shows error: "The email has already been taken"
- ✅ User must use different email or reset password

## 🚀 Everything Ready!

Once you add your email credentials to `.env`:
1. Password reset emails will be sent automatically
2. Users can reset forgotten passwords
3. Registration blocks duplicate emails
4. All security features active

## Need Help?

Check [EMAIL_SETUP_GUIDE.md](EMAIL_SETUP_GUIDE.md) for:
- Detailed setup instructions
- Troubleshooting guide
- Multiple email service options
- Security features explained
