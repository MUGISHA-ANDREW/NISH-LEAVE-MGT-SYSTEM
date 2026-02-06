@echo off
echo ========================================
echo   DEPLOY PASSWORD RESET FIX TO RAILWAY
echo ========================================
echo.

echo Step 1: Adding changes to git...
git add .
if errorlevel 1 (
    echo ERROR: Failed to add files
    pause
    exit /b 1
)

echo Step 2: Committing changes...
git commit -m "Fix password reset - add error handling and email sending"
if errorlevel 1 (
    echo WARNING: Nothing to commit or commit failed
)

echo Step 3: Pushing to Railway...
git push
if errorlevel 1 (
    echo ERROR: Failed to push to Railway
    pause
    exit /b 1
)

echo.
echo ========================================
echo   DEPLOYMENT SUCCESSFUL!
echo ========================================
echo.
echo Next steps:
echo 1. Wait 2-3 minutes for Railway to deploy
echo 2. Go to Railway Dashboard to monitor deployment
echo 3. Check logs: railway logs
echo.
echo After deployment:
echo 1. Visit: https://nish-leave-mgt-system-production.up.railway.app/forgot-password
echo 2. Enter a valid email address
echo 3. Check your email (and spam folder)
echo.
echo If email doesn't send, configure these in Railway:
echo   MAIL_MAILER=smtp
echo   MAIL_HOST=smtp.gmail.com
echo   MAIL_PORT=587
echo   MAIL_USERNAME=your@gmail.com
echo   MAIL_PASSWORD=your-app-password
echo   MAIL_ENCRYPTION=tls
echo   MAIL_FROM_ADDRESS=your@gmail.com
echo.
pause
