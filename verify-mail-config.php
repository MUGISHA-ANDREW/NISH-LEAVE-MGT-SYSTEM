<?php

/**
 * Mail Configuration Verification Script
 * Visit this page to see your current mail configuration
 * URL: https://your-app.railway.app/verify-mail-config.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

header('Content-Type: text/plain');

echo "==================================================\n";
echo "   MAIL CONFIGURATION VERIFICATION\n";
echo "==================================================\n\n";

echo "Environment Variables:\n";
echo "----------------------\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . env('APP_DEBUG') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n\n";

echo "Mail Configuration:\n";
echo "-------------------\n";
echo "Default Mailer: " . config('mail.default') . "\n";
echo "MAIL_MAILER env: " . env('MAIL_MAILER') . "\n\n";

echo "SMTP Settings:\n";
echo "--------------\n";
echo "Host: " . config('mail.mailers.smtp.host') . "\n";
echo "Port: " . config('mail.mailers.smtp.port') . "\n";
echo "Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
echo "Username: " . (config('mail.mailers.smtp.username') ? '***' . substr(config('mail.mailers.smtp.username'), -4) : 'NOT SET') . "\n";
echo "Password: " . (config('mail.mailers.smtp.password') ? 'SET (' . strlen(config('mail.mailers.smtp.password')) . ' chars)' : 'NOT SET') . "\n";
echo "Timeout: " . config('mail.mailers.smtp.timeout') . "\n\n";

echo "From Address:\n";
echo "-------------\n";
echo "Address: " . config('mail.from.address') . "\n";
echo "Name: " . config('mail.from.name') . "\n\n";

echo "Database Connection:\n";
echo "--------------------\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "✓ Database connected successfully\n";
    echo "Driver: " . DB::connection()->getDriverName() . "\n";
    
    // Check if password_resets table exists
    $tableExists = DB::select("SHOW TABLES LIKE 'password_resets'");
    if (count($tableExists) > 0) {
        echo "✓ password_resets table exists\n";
        $count = DB::table('password_resets')->count();
        echo "  Records in table: " . $count . "\n";
    } else {
        echo "✗ password_resets table DOES NOT exist\n";
        echo "  ACTION REQUIRED: Run migrations with: php artisan migrate --force\n";
    }
} catch (\Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n";
echo "Test Email:\n";
echo "-----------\n";
echo "To test email sending, visit: /test-email-send\n";

echo "\n==================================================\n";
echo "If you see this page, PHP is working correctly.\n";
echo "==================================================\n";
