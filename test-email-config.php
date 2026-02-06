#!/usr/bin/env php
<?php

/**
 * Email Configuration Test Script
 * 
 * This script tests if your email configuration is working properly
 * Run: php test-email-config.php
 */

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "\n=================================\n";
echo "EMAIL CONFIGURATION TEST\n";
echo "=================================\n\n";

// Display current configuration
echo "📧 Current Mail Configuration:\n";
echo "   Driver:     " . Config::get('mail.default') . "\n";
echo "   Host:       " . Config::get('mail.mailers.smtp.host') . "\n";
echo "   Port:       " . Config::get('mail.mailers.smtp.port') . "\n";
echo "   Username:   " . Config::get('mail.mailers.smtp.username') . "\n";
echo "   Encryption: " . Config::get('mail.mailers.smtp.transport') . "\n";
echo "   From Addr:  " . Config::get('mail.from.address') . "\n";
echo "   From Name:  " . Config::get('mail.from.name') . "\n\n";

// Check if credentials are configured
$username = Config::get('mail.mailers.smtp.username');
if (empty($username) || $username === 'your-email@gmail.com') {
    echo "⚠️  WARNING: Email credentials not configured!\n";
    echo "   Please update your .env file with real SMTP credentials.\n";
    echo "   See EMAIL_SETUP_GUIDE.md for instructions.\n\n";
    exit(1);
}

// Ask for test email
echo "Enter email address to send test to (or press Enter to skip): ";
$testEmail = trim(fgets(STDIN));

if (!empty($testEmail)) {
    echo "\n📤 Sending test email to: $testEmail\n";
    
    try {
        Mail::raw('This is a test email from Nish Auto Limited Leave Management System.', function($message) use ($testEmail) {
            $message->to($testEmail)
                    ->subject('Test Email - Password Reset Configuration');
        });
        
        echo "✅ Test email sent successfully!\n";
        echo "   Check your inbox (and spam folder).\n\n";
    } catch (\Exception $e) {
        echo "❌ Failed to send test email!\n";
        echo "   Error: " . $e->getMessage() . "\n\n";
        echo "Common issues:\n";
        echo "   1. Invalid SMTP credentials\n";
        echo "   2. Less secure apps blocked (Gmail)\n";
        echo "   3. Need to use App Password (Gmail)\n";
        echo "   4. Firewall blocking outbound SMTP\n\n";
        exit(1);
    }
} else {
    echo "⏭️  Skipped test email send.\n\n";
}

echo "=================================\n";
echo "Test completed!\n";
echo "=================================\n\n";

// Test password reset database
echo "🔍 Checking password_resets table...\n";
try {
    $tableExists = \Illuminate\Support\Facades\Schema::hasTable('password_resets');
    if ($tableExists) {
        echo "✅ password_resets table exists\n";
        
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('password_resets');
        echo "   Columns: " . implode(', ', $columns) . "\n";
    } else {
        echo "❌ password_resets table NOT found!\n";
        echo "   Run: php artisan migrate\n";
    }
} catch (\Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";
