#!/bin/bash

# Railway Post-Deployment Verification Script
# This runs automatically after Railway deployment

echo "═══════════════════════════════════════════════════════════"
echo "  RAILWAY POST-DEPLOYMENT CHECK"
echo "═══════════════════════════════════════════════════════════"

# Check if running on Railway
if [ -z "$RAILWAY_ENVIRONMENT" ]; then
    echo "❌ Not running on Railway"
    exit 0
fi

echo "✅ Running on Railway environment: $RAILWAY_ENVIRONMENT"

# 1. Check APP_KEY
if [ -z "$APP_KEY" ]; then
    echo "❌ CRITICAL: APP_KEY is not set!"
    exit 1
else
    echo "✅ APP_KEY is set"
fi

# 2. Check Database Connection
echo ""
echo "📋 Checking database connection..."
php artisan tinker --execute="DB::connection()->getPdo(); echo '✅ Database connected';" || {
    echo "❌ Database connection failed"
    exit 1
}

# 3. Verify password_resets table
echo ""
echo "📋 Checking password_resets table..."
php artisan tinker --execute="echo Schema::hasTable('password_resets') ? '✅ password_resets table exists' : '❌ Table missing';"

# 4. Check Mail Configuration
echo ""
echo "📋 Mail Configuration:"
php artisan tinker --execute="echo 'Driver: ' . config('mail.default'); echo '\nHost: ' . config('mail.mailers.smtp.host'); echo '\nPort: ' . config('mail.mailers.smtp.port');"

# 5. Test Routes
echo ""
echo "📋 Checking password reset routes..."
php artisan route:list --name=password --compact || echo "⚠️  Could not list routes"

echo ""
echo "═══════════════════════════════════════════════════════════"
echo "✅ DEPLOYMENT VERIFICATION COMPLETE"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "Test your password reset at:"
echo "$APP_URL/forgot-password"
echo ""
