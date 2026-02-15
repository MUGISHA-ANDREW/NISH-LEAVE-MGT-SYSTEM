@echo off
REM Quick Deploy Script for Windows
REM This script will commit and push your changes to GitHub

echo ╔══════════════════════════════════════════════════════════════╗
echo ║   PASSWORD RESET DEPLOYMENT SCRIPT                          ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo 📋 Running pre-deployment checks...
echo.

REM Check if git is initialized
git status >nul 2>&1
if errorlevel 1 (
    echo ❌ Git repository not initialized
    echo Run: git init
    pause
    exit /b 1
)

echo ✅ Git repository OK
echo.

REM Run local tests
echo 🧪 Running configuration test...
php test-password-reset-config.php
if errorlevel 1 (
    echo.
    echo ❌ Configuration test failed!
    echo Fix errors before deploying.
    pause
    exit /b 1
)

echo.
echo ═══════════════════════════════════════════════════════════════
echo   All checks passed! Ready to deploy.
echo ═══════════════════════════════════════════════════════════════
echo.

REM Stage all changes
echo 📦 Staging all changes...
git add .

REM Show what will be committed
echo.
echo 📝 Files to be committed:
git status --short

echo.
echo ═══════════════════════════════════════════════════════════════
echo.

REM Prompt for commit message
set /p confirm="Continue with deployment? (Y/N): "
if /i not "%confirm%"=="Y" (
    echo.
    echo ⚠️  Deployment cancelled.
    pause
    exit /b 0
)

echo.
echo 📤 Committing changes...
git commit -m "feat: Complete password reset system with email functionality

- Implemented ForgotPasswordController with token generation
- Added password_resets migration
- Created email template for reset links
- Configured SMTP email sending (Gmail)
- Added comprehensive error handling
- Tokens expire after 60 minutes
- Email template with professional design
- All views and routes configured
- Automatic migration on deployment
- Test scripts and documentation included"

if errorlevel 1 (
    echo.
    echo ⚠️  Nothing to commit or commit failed
    echo.
    pause
    exit /b 1
)

echo.
echo 🚀 Pushing to GitHub...
git push origin main

if errorlevel 1 (
    echo.
    echo ❌ Push failed! Check your git remote and credentials.
    echo.
    echo Try:
    echo   git remote -v
    echo   git push origin main --force
    pause
    exit /b 1
)

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║   ✅ DEPLOYMENT SUCCESSFUL!                                   ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
echo 🎉 Your password reset system has been deployed!
echo.
echo 📊 Next steps:
echo    1. Monitor Railway dashboard for deployment
echo    2. Wait 2-3 minutes for build to complete
echo    3. Test at: https://nish-leave-mgt-system-production.up.railway.app/forgot-password
echo.
echo 🔍 To view Railway logs:
echo    railway logs --follow
echo.

pause
