# Email Configuration Guide - Password Reset Feature

## Current Status ✅
- **Password reset functionality**: Fully implemented
- **Email validation on registration**: Already configured (prevents duplicate emails)
- **Database**: password_resets table created

## Email Setup Instructions

### Option 1: Gmail SMTP (Recommended for Testing)

1. **Update your `.env` file** with these settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-specific-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@nishautolimited.com"
MAIL_FROM_NAME="Nish Auto Limited - Leave Management"
```

2. **Generate Gmail App Password**:
   - Go to Google Account → Security
   - Enable 2-Step Verification
   - Search for "App Passwords"
   - Select "Mail" and "Other (Custom name)"
   - Copy the 16-character password
   - Use this password in `MAIL_PASSWORD` (not your regular Gmail password)

### Option 2: Mailtrap (Best for Development/Testing)

Perfect for testing emails without sending to real inboxes.

1. **Sign up at**: https://mailtrap.io (Free tier available)

2. **Update `.env`**:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@nishautolimited.com"
MAIL_FROM_NAME="Nish Auto Limited - Leave Management"
```

3. **Get credentials**: From your Mailtrap inbox settings

### Option 3: SendGrid (Production Ready)

1. **Sign up at**: https://sendgrid.com (Free: 100 emails/day)

2. **Create API Key**:
   - Go to Settings → API Keys
   - Create API Key with "Mail Send" permissions

3. **Update `.env`**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@nishautolimited.com"
MAIL_FROM_NAME="Nish Auto Limited - Leave Management"
```

### Option 4: Mailgun (Production Ready)

1. **Sign up at**: https://mailgun.com

2. **Update `.env`**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@nishautolimited.com"
MAIL_FROM_NAME="Nish Auto Limited - Leave Management"
```

## After Configuration

1. **Clear config cache**:
```bash
php artisan config:clear
php artisan cache:clear
```

2. **Test the feature**:
   - Visit login page: https://your-app.up.railway.app
   - Click "Forgot password?"
   - Enter email: admin@example.com
   - Check email inbox (or Mailtrap inbox if testing)
   - Click reset link
   - Set new password

## How It Works

### Forgot Password Flow:
1. User clicks "Forgot password?" on login page
2. User enters their email address
3. System validates email exists in database
4. System generates secure reset token (60-minute expiry)
5. System sends email with reset link
6. User clicks link, enters new password
7. Password is updated and user can login

### Security Features:
- ✅ Tokens are hashed before database storage
- ✅ Tokens expire after 60 minutes
- ✅ Token is deleted after successful password reset
- ✅ Email must exist in database
- ✅ Password requires minimum 8 characters + confirmation
- ✅ Old tokens are automatically replaced when new request is made

### Email Registration Validation:
The user creation form already has validation to prevent duplicate emails:
```php
'email' => 'required|email|unique:users'
```

This means:
- ✅ Email format must be valid
- ✅ Email must be unique (can't register with existing email)
- ✅ Shows error message if email already exists

## Troubleshooting

### Issue: "Failed to send reset email"
**Solutions**:
- Check SMTP credentials are correct
- Ensure `config:clear` was run after .env changes
- Check firewall/network allows outbound SMTP connections
- Verify email service (Gmail, etc.) is working

### Issue: "Invalid reset link"
**Solutions**:
- Token may have expired (60 minutes)
- Request a new password reset
- Check URL wasn't broken when copying

### Issue: "Email already exists" on registration
**This is expected behavior** - it means:
- The email is already in the database
- User should use "Forgot Password" instead
- Or use a different email address

## Production Deployment Notes

For Railway deployment:
1. Set environment variables in Railway dashboard
2. Don't commit `.env` file with real credentials
3. Use environment-specific email settings
4. Consider using SendGrid or Mailgun for production
5. Monitor email delivery rates

## Email Template

The password reset email includes:
- Professional branded design
- Clear "Reset Password" button
- Backup text link
- 60-minute expiry notice
- Security warning about unsolicited emails
- Company branding

Location: `resources/views/emails/password-reset.blade.php`
