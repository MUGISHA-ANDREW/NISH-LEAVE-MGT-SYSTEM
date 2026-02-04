# 🚀 Railway Deployment Checklist - Fix "Form is not secure"

## Before Deployment

- [x] TrustProxies middleware created
- [x] Bootstrap configured
- [x] Session config updated for HTTPS
- [x] Sessions migration created
- [x] AppServiceProvider forces HTTPS
- [x] All forms have @csrf tokens

## On Railway Dashboard

### 1. Set Environment Variables

Go to Railway → Your Project → Variables tab

Add these (copy-paste):

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.up.railway.app
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

**⚠️ REPLACE `your-app-name.up.railway.app` with your actual Railway URL!**

- [ ] APP_ENV set to production
- [ ] APP_DEBUG set to false  
- [ ] APP_URL set to your HTTPS Railway URL
- [ ] SESSION_SECURE_COOKIE set to true
- [ ] SESSION_DRIVER set to database

### 2. Wait for Auto-Deploy

Railway will automatically redeploy after you save variables.

- [ ] Deployment started
- [ ] Build completed
- [ ] Deployment successful

### 3. Verify Database

Make sure the `sessions` table exists:

- [ ] Sessions table created (migrations ran)
- [ ] Database connection works

## Testing After Deployment

### Test 1: Login Form

1. Open your Railway URL: `https://your-app.up.railway.app/login`
2. Open browser DevTools (F12)
3. Look at the login form's action attribute

**Expected:** Should be `https://...` (not `http://`)

- [ ] Login form action uses HTTPS
- [ ] No browser warning appears
- [ ] Can submit form successfully

### Test 2: All Forms Work

Try these forms:

- [ ] Login form - works on first try
- [ ] Apply leave form - works on first try  
- [ ] Edit profile form - works on first try
- [ ] Change password form - works on first try

### Test 3: No Errors

- [ ] No "419 Page Expired" errors
- [ ] No "Form is not secure" warnings
- [ ] No CSRF token mismatches
- [ ] Sessions persist across pages

## If You Still See Issues

### Browser shows "Form is not secure"?

1. Check `APP_URL` on Railway - must start with `https://`
2. Check `SESSION_SECURE_COOKIE` - must be `true`
3. Clear browser cache and cookies
4. Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)

### Getting 419 errors?

1. Run: `php artisan config:clear`
2. Run: `php artisan cache:clear`
3. Check sessions table exists in database
4. Verify `SESSION_DRIVER=database` is set

### Form action still shows HTTP?

1. Check `APP_URL` is set correctly on Railway
2. Redeploy after changing environment variables
3. Clear all caches
4. Check AppServiceProvider has `URL::forceScheme('https')`

## Success! ✅

When everything works:

- ✅ No browser warnings
- ✅ Forms submit on first try
- ✅ All URLs use HTTPS
- ✅ Sessions work correctly
- ✅ No CSRF errors

---

**Questions?** Check [RAILWAY_HTTPS_FIX.md](./RAILWAY_HTTPS_FIX.md) for detailed explanations.
