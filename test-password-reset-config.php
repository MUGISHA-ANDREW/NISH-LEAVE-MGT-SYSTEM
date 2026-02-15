<?php

/**
 * Password Reset Configuration Test Script
 * 
 * Run this to verify your password reset setup is correct
 * Usage: php test-password-reset-config.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   PASSWORD RESET CONFIGURATION TEST                           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Application Key
echo "📋 1. APPLICATION KEY\n";
echo str_repeat("-", 60) . "\n";
$appKey = config('app.key');
if ($appKey) {
    echo "   ✅ APP_KEY is set\n";
    echo "   Key: " . substr($appKey, 0, 20) . "...\n";
} else {
    echo "   ❌ APP_KEY is MISSING!\n";
    echo "   Fix: Run 'php artisan key:generate'\n";
}
echo "\n";

// Test 2: Application URL
echo "📋 2. APPLICATION URL\n";
echo str_repeat("-", 60) . "\n";
$appUrl = config('app.url');
echo "   URL: " . $appUrl . "\n";
if (strpos($appUrl, 'localhost') !== false || strpos($appUrl, '127.0.0.1') !== false) {
    echo "   ⚠️  Using local URL\n";
} else {
    echo "   ✅ Production URL configured\n";
}
echo "\n";

// Test 3: Database Connection
echo "📋 3. DATABASE CONNECTION\n";
echo str_repeat("-", 60) . "\n";
try {
    DB::connection()->getPdo();
    echo "   ✅ Database connected successfully\n";
    echo "   Driver: " . config('database.default') . "\n";
    echo "   Host: " . config('database.connections.' . config('database.default') . '.host') . "\n";
    echo "   Database: " . config('database.connections.' . config('database.default') . '.database') . "\n";
} catch (\Exception $e) {
    echo "   ❌ Database connection FAILED!\n";
    echo "   Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Password Resets Table
echo "📋 4. PASSWORD_RESETS TABLE\n";
echo str_repeat("-", 60) . "\n";
try {
    if (Schema::hasTable('password_resets')) {
        echo "   ✅ password_resets table exists\n";
        $count = DB::table('password_resets')->count();
        echo "   Current entries: " . $count . "\n";
        
        // Check table structure
        $columns = Schema::getColumnListing('password_resets');
        echo "   Columns: " . implode(', ', $columns) . "\n";
        
        $expected = ['email', 'token', 'created_at'];
        $missing = array_diff($expected, $columns);
        if (empty($missing)) {
            echo "   ✅ All required columns present\n";
        } else {
            echo "   ⚠️  Missing columns: " . implode(', ', $missing) . "\n";
        }
    } else {
        echo "   ❌ password_resets table NOT FOUND!\n";
        echo "   Fix: Run 'php artisan migrate'\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Table check failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Mail Configuration
echo "📋 5. MAIL CONFIGURATION\n";
echo str_repeat("-", 60) . "\n";
$mailDriver = config('mail.default');
echo "   Driver: " . $mailDriver . "\n";

if ($mailDriver === 'smtp') {
    echo "   Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "   Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "   Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "   Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "   Password: " . (config('mail.mailers.smtp.password') ? '***set***' : '❌ NOT SET') . "\n";
    echo "   From Address: " . config('mail.from.address') . "\n";
    echo "   From Name: " . config('mail.from.name') . "\n";
    
    if (config('mail.mailers.smtp.host') && config('mail.mailers.smtp.password')) {
        echo "   ✅ SMTP configuration appears complete\n";
    } else {
        echo "   ⚠️  SMTP configuration incomplete\n";
    }
} else {
    echo "   ⚠️  Using '" . $mailDriver . "' driver (not SMTP)\n";
    if ($mailDriver === 'log') {
        echo "   📝 Emails will be logged to storage/logs/laravel.log\n";
        echo "   This is fine for testing but won't send real emails\n";
    }
}
echo "\n";

// Test 6: Users Table Check
echo "📋 6. USERS TABLE CHECK\n";
echo str_repeat("-", 60) . "\n";
try {
    if (Schema::hasTable('users')) {
        echo "   ✅ users table exists\n";
        $userCount = DB::table('users')->count();
        echo "   Total users: " . $userCount . "\n";
        
        if ($userCount > 0) {
            $sampleUser = DB::table('users')->first();
            echo "   Sample email: " . $sampleUser->email . "\n";
            echo "   ✅ You can test with this email\n";
        } else {
            echo "   ⚠️  No users in database\n";
            echo "   Create a user first to test password reset\n";
        }
    } else {
        echo "   ❌ users table NOT FOUND!\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 7: Routes Check
echo "📋 7. PASSWORD RESET ROUTES\n";
echo str_repeat("-", 60) . "\n";
$routes = [
    'password.request' => '/forgot-password',
    'password.email' => '/forgot-password (POST)',
    'password.reset' => '/reset-password/{token}',
    'password.update.reset' => '/reset-password (POST)',
];

$existingRoutes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function($route) {
    return $route->getName();
})->filter()->toArray();

foreach ($routes as $name => $path) {
    if (in_array($name, $existingRoutes)) {
        echo "   ✅ " . $name . " → " . $path . "\n";
    } else {
        echo "   ❌ " . $name . " → MISSING!\n";
    }
}
echo "\n";

// Test 8: Controller Check
echo "📋 8. CONTROLLER CHECK\n";
echo str_repeat("-", 60) . "\n";
$controllerPath = app_path('Http/Controllers/Auth/ForgotPasswordController.php');
if (file_exists($controllerPath)) {
    echo "   ✅ ForgotPasswordController exists\n";
    echo "   Path: " . $controllerPath . "\n";
} else {
    echo "   ❌ ForgotPasswordController NOT FOUND!\n";
}
echo "\n";

// Test 9: Email Template Check
echo "📋 9. EMAIL TEMPLATE CHECK\n";
echo str_repeat("-", 60) . "\n";
$emailTemplate = resource_path('views/emails/password-reset.blade.php');
if (file_exists($emailTemplate)) {
    echo "   ✅ Email template exists\n";
    echo "   Path: " . $emailTemplate . "\n";
} else {
    echo "   ❌ Email template NOT FOUND!\n";
    echo "   Expected: " . $emailTemplate . "\n";
}
echo "\n";

// Test 10: View Files Check
echo "📋 10. VIEW FILES CHECK\n";
echo str_repeat("-", 60) . "\n";
$views = [
    'auth/forgot-password.blade.php' => resource_path('views/auth/forgot-password.blade.php'),
    'auth/reset-password.blade.php' => resource_path('views/auth/reset-password.blade.php'),
];

foreach ($views as $name => $path) {
    if (file_exists($path)) {
        echo "   ✅ " . $name . "\n";
    } else {
        echo "   ❌ " . $name . " NOT FOUND\n";
    }
}
echo "\n";

// Final Summary
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   SUMMARY                                                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$issues = [];

if (!$appKey) {
    $issues[] = "❌ APP_KEY is missing - Run: php artisan key:generate";
}

if (!Schema::hasTable('password_resets')) {
    $issues[] = "❌ password_resets table missing - Run: php artisan migrate";
}

if ($mailDriver === 'log') {
    $issues[] = "⚠️  Mail driver is 'log' - Emails won't be sent (OK for testing)";
} elseif ($mailDriver === 'smtp' && !config('mail.mailers.smtp.host')) {
    $issues[] = "❌ SMTP host not configured - Set MAIL_HOST in .env";
}

if (empty($issues)) {
    echo "   🎉 ALL CHECKS PASSED!\n";
    echo "   Your password reset system is properly configured.\n\n";
    echo "   Next steps:\n";
    echo "   1. Visit " . config('app.url') . "/forgot-password\n";
    echo "   2. Enter a user email and test the flow\n";
    if ($mailDriver === 'log') {
        echo "   3. Check storage/logs/laravel.log for the reset email\n";
    } else {
        echo "   3. Check your inbox for the reset email\n";
    }
} else {
    echo "   ⚠️  ISSUES FOUND:\n\n";
    foreach ($issues as $issue) {
        echo "   " . $issue . "\n";
    }
    echo "\n   Fix these issues and run this script again.\n";
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   For more help, see: PASSWORD_RESET_COMPLETE_GUIDE.md       ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
