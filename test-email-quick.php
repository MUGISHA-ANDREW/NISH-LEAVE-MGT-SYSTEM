<?php

/**
 * Quick Email Configuration Test Script
 * Run this to verify your email settings work before deploying
 * 
 * Usage: php test-email-quick.php
 */

require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "===========================================\n";
echo "   EMAIL CONFIGURATION TEST\n";
echo "===========================================\n\n";

// Display current configuration
echo "Current Mail Configuration:\n";
echo "----------------------------\n";
echo "Mailer: " . Config::get('mail.default') . "\n";
echo "Host: " . Config::get('mail.mailers.smtp.host') . "\n";
echo "Port: " . Config::get('mail.mailers.smtp.port') . "\n";
echo "Encryption: " . Config::get('mail.mailers.smtp.encryption') . "\n";
echo "Username: " . Config::get('mail.mailers.smtp.username') . "\n";
echo "From Address: " . Config::get('mail.from.address') . "\n";
echo "From Name: " . Config::get('mail.from.name') . "\n\n";

// Ask for test email
echo "Enter email address to send test to: ";
$testEmail = trim(fgets(STDIN));

if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid email address\n";
    exit(1);
}

echo "\nSending test email to: $testEmail\n";
echo "Please wait...\n\n";

try {
    Mail::raw('This is a test email from your Leave Management System. If you received this, your email configuration is working correctly!', function ($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('✓ Test Email - Configuration Working');
    });
    
    // Check for failures
    if (count(Mail::failures()) > 0) {
        echo "❌ Failed to send email to: $testEmail\n";
        echo "Failures: " . print_r(Mail::failures(), true) . "\n";
        exit(1);
    }
    
    echo "✅ SUCCESS! Email sent successfully!\n";
    echo "----------------------------\n";
    echo "Check the inbox (and spam folder) of: $testEmail\n\n";
    echo "If you received the email, your configuration is correct!\n";
    echo "You can now commit and deploy your changes.\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: Failed to send email\n";
    echo "----------------------------\n";
    echo "Error Message: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "1. Check your MAIL_* environment variables\n";
    echo "2. Verify your SendGrid API key is valid\n";
    echo "3. Ensure sender email is verified in SendGrid\n";
    echo "4. Check SendGrid dashboard for more details\n\n";
    exit(1);
}

echo "===========================================\n";
