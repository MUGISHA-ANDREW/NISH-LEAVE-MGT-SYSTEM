# 🔧 Fix Email Timeout (SocketStream Error)

The error `Maximum execution time of 30 seconds exceeded` in `SocketStream.php` means the server is trying to connect to Gmail but the connection is timing out. This usually happens because Port 587 (TLS) is blocked or slow on the cloud provider.

## ✅ Solution: Switch to Port 465 (SSL)

Port 465 with SSL is often more reliable on Railway than Port 587.

### 1. Update Railway Variables

Go to **Railway Dashboard** > **Variables** and update these values:

| Variable | Connection |
| :--- | :--- |
| `MAIL_PORT` | `465` |
| `MAIL_ENCRYPTION` | `ssl` |
| `MAIL_TIMEOUT` | `60` |

### 2. Verify Other Variables

Ensure these are still correct:

| Variable | Value |
| :--- | :--- |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.gmail.com` |
| `MAIL_USERNAME` | `andrewmugisha699@gmail.com` |
| `MAIL_PASSWORD` | `fgjocmethlogxhdl` |

### 3. Deploy Changes

1. I have updated `config/mail.php` to accept a custom timeout.
2. Push the code changes:

```powershell
git add .
git commit -m "Allow configurable mail timeout and update variables"
git push
```

3. Wait for Railway to deploy.

---

## 🔍 If it STILL fails (Alternative)

If Port 465 also times out, try using the default PHP `mail` function as a fallback (though less reliable for delivery):

`MAIL_MAILER=sendmail`

But stick with **SMTP on Port 465** first.
