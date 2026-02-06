@echo off
echo ========================================
echo   RUN MIGRATION ON RAILWAY
echo ========================================
echo.

echo Step 1: Checking Railway connection...
railway whoami
if errorlevel 1 (
    echo.
    echo ERROR: Not logged in to Railway
    echo Please run: railway login
    echo Then run this script again
    pause
    exit /b 1
)

echo.
echo Step 2: Linking to Railway project...
railway link
if errorlevel 1 (
    echo ERROR: Failed to link project
    pause
    exit /b 1
)

echo.
echo Step 3: Running migrations...
railway run php artisan migrate --force

echo.
echo Step 4: Checking if password_resets table exists...
railway run php artisan tinker --execute="echo DB::table('password_resets')->count() . ' records in password_resets table';"

echo.
echo ========================================
echo   MIGRATION COMPLETE!
echo ========================================
echo.
echo Now test the password reset:
echo https://nish-leave-mgt-system-production.up.railway.app/forgot-password
echo.
pause
