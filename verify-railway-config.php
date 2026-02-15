<?php

/**
 * Railway Configuration Verification Script
 * 
 * Run this on Railway to verify everything is configured correctly
 * Usage: railway run php verify-railway-config.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   RAILWAY CONFIGURATION VERIFICATION                          ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$issues = [];
$warnings = [];

// 1. APP_KEY
echo "✅ 1. APP_KEY: Set\n";

// 2. APP_URL
$appUrl = config('app.url');
echo "✅ 2. APP_URL: " . $appUrl . "\n";

// 3. Database
echo "\n📋 3. DATABASE CONNECTION\n";
try {
    DB::connection()->getPdo();
    echo "   ✅ Connected to: " . config('database.connections.mysql.host') . "\n";
    echo "   ✅ Database: " . config('database.connections.mysql.database') . "\n";
} catch (\Exception $e) {
    echo "   ❌ Connection failed: " . $e->getMessage() . "\n";
    $issues[] = "Database connection failed";
}

// 4. Password Resets Table
echo "\n📋 4. PASSWORD_RESETS TABLE\n";
try {
    if (Schema::hasTable('password_resets')) {
        echo "   ✅ Table exists\n";
        $count = DB::table('password_resets')->count();
        echo "   Current entries: " . $count . "\n";
    } else {
        echo "   ❌ Table NOT found\n";
        echo "   Run: railway run php artisan migrate --force\n";
        $issues[] = "password_resets table missing";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $issues[] = "Cannot access password_resets table";
}

// 5. Users Table
echo "\n📋 5. USERS TABLE\n";
try {
    $userCount = DB::table('users')->count();
    echo "   ✅ Users in database: " . $userCount . "\n";
    if ($userCount > 0) {
        $sampleUser = DB::table('users')->first();
        echo "   Sample email for testing: " . $sampleUser->email . "\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 6. Mail Configuration
echo "\n📋 6. MAIL CONFIGURATION\n";
$mailConfig = config('mail.mailers.smtp');
echo "   Driver: " . config('mail.default') . "\n";
echo "   Host: " . $mailConfig['host'] . "\n";
echo "   Port: " . $mailConfig['port'] . "\n";
echo "   Encryption: " . $mailConfig['encryption'] . "\n";
echo "   Username: " . $mailConfig['username'] . "\n";
echo "   Password: " . (isset($mailConfig['password']) && $mailConfig['password'] ? '***set***' : '❌ NOT SET') . "\n";
echo "   From: " . config('mail.from.address') . " (" . config('mail.from.name') . ")\n";

// Port warning
if ($mailConfig['port'] == 465 && $mailConfig['encryption'] == 'ssl') {
    echo "   ✅ Port 465 with SSL encryption (correct)\n";
} elseif ($mailConfig['port'] == 587 && $mailConfig['encryption'] == 'tls') {
    echo "   ✅ Port 587 with TLS encryption (correct)\n";
} else {
    echo "   ⚠️  Port/Encryption mismatch detected\n";
    $warnings[] = "Port " . $mailConfig['port'] . " should use " . ($mailConfig['port'] == 465 ? 'SSL' : 'TLS');
}

// 7. Routes Check
echo "\n📋 7. PASSWORD RESET ROUTES\n";
$requiredRoutes = ['password.request', 'password.email', 'password.reset', 'password.update.reset'];
$existingRoutes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function($route) {
    return $route->getName();
})->filter()->toArray();

foreach ($requiredRoutes as $routeName) {
    if (in_array($routeName, $existingRoutes)) {
        echo "   ✅ " . $routeName . "\n";
    } else {
        echo "   ❌ " . $routeName . " NOT FOUND\n";
        $issues[] = "Route missing: " . $routeName;
    }
}

// 8. Controller Check
echo "\n📋 8. CONTROLLER\n";
if (class_exists('App\Http\Controllers\Auth\ForgotPasswordController')) {
    echo "   ✅ ForgotPasswordController exists\n";
} else {
    echo "   ❌ ForgotPasswordController NOT FOUND\n";
    $issues[] = "Controller missing";
}

// 9. Views Check
echo "\n📋 9. VIEWS\n";
$views = [
    'auth.forgot-password',
    'auth.reset-password',
    'emails.password-reset'
];
foreach ($views as $view) {
    if (view()->exists($view)) {
        echo "   ✅ " . $view . "\n";
    } else {
        echo "   ❌ " . $view . " NOT FOUND\n";
        $issues[] = "View missing: " . $view;
    }
}

// Summary
echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║   SUMMARY                                                      ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

if (empty($issues)) {
    echo "🎉 ALL CHECKS PASSED!\n\n";
    echo "Your password reset system is ready to use.\n\n";
    echo "Test it now:\n";
    echo "1. Visit: " . $appUrl . "/forgot-password\n";
    echo "2. Enter a user email\n";
    echo "3. Check inbox at: " . $mailConfig['username'] . "\n";
    echo "4. Click the reset link\n";
    echo "5. Enter new password\n\n";
} else {
    echo "⚠️  ISSUES FOUND:\n\n";
    foreach ($issues as $issue) {
        echo "   ❌ " . $issue . "\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  WARNINGS:\n\n";
    foreach ($warnings as $warning) {
        echo "   ⚠️  " . $warning . "\n";
    }
    echo "\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
