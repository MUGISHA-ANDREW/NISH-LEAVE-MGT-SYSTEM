<?php

/**
 * Simple Email Test Script
 * 
 * Run this to quickly test if email sending works
 * Usage: php test-email-simple.php your-email@example.com
 */

if (php_sapi_name() != 'cli') {
    die('This script can only be run from the command line.');
}

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

$testEmail = $argv[1] ?? null;

if (!$testEmail) {
    echo "❌ Please provide an email address\n";
    echo "Usage: php test-email-simple.php your-email@example.com\n";
    exit(1);
}

if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid email address: $testEmail\n";
    exit(1);
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   EMAIL TEST SCRIPT                                           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "📧 Testing email to: $testEmail\n\n";

// Show configuration
echo "📋 Current Configuration:\n";
echo str_repeat("-", 60) . "\n";
echo "   Mail Driver: " . config('mail.default') . "\n";
echo "   SMTP Host: " . config('mail.mailers.smtp.host', 'Not set') . "\n";
echo "   SMTP Port: " . config('mail.mailers.smtp.port', 'Not set') . "\n";
echo "   SMTP Encryption: " . config('mail.mailers.smtp.encryption', 'Not set') . "\n";
echo "   From Address: " . config('mail.from.address', 'Not set') . "\n";
echo "   From Name: " . config('mail.from.name', 'Not set') . "\n\n";

// Test email sending
echo "📤 Sending test email...\n";

try {
    Mail::raw('This is a test email from your Laravel application. If you received this, email sending is working correctly!', function($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('Test Email from ' . config('app.name'));
    });
    
    if (count(Mail::failures()) > 0) {
        echo "❌ Email failed to send\n";
        echo "   Failed addresses: " . implode(', ', Mail::failures()) . "\n";
        exit(1);
    }
    
    echo "✅ Email sent successfully!\n\n";
    
    // Additional info based on mail driver
    $driver = config('mail.default');
    if ($driver === 'log') {
        echo "ℹ️  Mail driver is 'log' - email was logged to:\n";
        echo "   storage/logs/laravel.log\n\n";
        echo "   To send real emails, configure SMTP in your .env file.\n";
    } elseif ($driver === 'smtp') {
        echo "ℹ️  Check your inbox at: $testEmail\n";
        echo "   (Don't forget to check spam folder)\n";
    }
    
    echo "\n✅ Email test completed\n";
    
} catch (\Exception $e) {
    echo "❌ Email sending FAILED!\n\n";
    echo "Error Message:\n";
    echo str_repeat("-", 60) . "\n";
    echo $e->getMessage() . "\n\n";
    
    echo "Common Solutions:\n";
    echo str_repeat("-", 60) . "\n";
    
    if (strpos($e->getMessage(), 'Connection') !== false) {
        echo "❌ Connection Error\n";
        echo "   → Check MAIL_HOST and MAIL_PORT are correct\n";
        echo "   → Check firewall isn't blocking the port\n";
        echo "   → Verify SMTP server is accessible\n\n";
        
        echo "   Common SMTP settings:\n";
        echo "   Gmail: smtp.gmail.com:587 (Use App Password!)\n";
        echo "   Mailtrap: sandbox.smtp.mailtrap.io:2525\n";
        echo "   SendGrid: smtp.sendgrid.net:587\n";
    } elseif (strpos($e->getMessage(), 'authentication') !== false || strpos($e->getMessage(), 'Authenticate') !== false) {
        echo "❌ Authentication Error\n";
        echo "   → Check MAIL_USERNAME is correct\n";
        echo "   → Check MAIL_PASSWORD is correct\n";
        echo "   → For Gmail: Use App Password, not regular password\n";
        echo "   → Ensure 2-Step Verification is enabled (for Gmail)\n";
    } else {
        echo "❌ Unknown Error\n";
        echo "   → Check .env file for typos\n";
        echo "   → Run: php artisan config:clear\n";
        echo "   → Check Laravel logs: storage/logs/laravel.log\n";
    }
    
    echo "\n";
    exit(1);
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║   For more help, see: PASSWORD_RESET_COMPLETE_GUIDE.md       ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
