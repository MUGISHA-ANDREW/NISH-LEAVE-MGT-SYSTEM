#!/usr/bin/env php
<?php

/**
 * HTTPS Configuration Verification Script
 * 
 * Run this script to verify all HTTPS-related configurations are correct
 */

echo "=== Laravel HTTPS Configuration Check ===\n\n";

$baseDir = __DIR__;
$errors = [];
$warnings = [];
$success = [];

// Check 1: TrustProxies middleware exists
echo "[1/7] Checking TrustProxies middleware...\n";
$trustProxiesPath = $baseDir . '/app/Http/Middleware/TrustProxies.php';
if (file_exists($trustProxiesPath)) {
    $content = file_get_contents($trustProxiesPath);
    if (strpos($content, "protected \$proxies = '*'") !== false) {
        $success[] = "✓ TrustProxies middleware configured correctly";
    } else {
        $warnings[] = "⚠ TrustProxies exists but may not trust all proxies";
    }
} else {
    $errors[] = "✗ TrustProxies middleware not found";
}

// Check 2: Bootstrap app configuration
echo "[2/7] Checking bootstrap/app.php...\n";
$bootstrapPath = $baseDir . '/bootstrap/app.php';
if (file_exists($bootstrapPath)) {
    $content = file_get_contents($bootstrapPath);
    if (strpos($content, "trustProxies") !== false) {
        $success[] = "✓ Bootstrap configured with trustProxies";
    } else {
        $errors[] = "✗ Bootstrap missing trustProxies configuration";
    }
} else {
    $errors[] = "✗ bootstrap/app.php not found";
}

// Check 3: Session configuration
echo "[3/7] Checking session configuration...\n";
$sessionPath = $baseDir . '/config/session.php';
if (file_exists($sessionPath)) {
    $content = file_get_contents($sessionPath);
    if (strpos($content, "SESSION_SECURE_COOKIE") !== false) {
        $success[] = "✓ Session secure cookie configuration found";
    } else {
        $warnings[] = "⚠ Session config may not use SESSION_SECURE_COOKIE";
    }
} else {
    $errors[] = "✗ config/session.php not found";
}

// Check 4: AppServiceProvider HTTPS forcing
echo "[4/7] Checking AppServiceProvider...\n";
$appServicePath = $baseDir . '/app/Providers/AppServiceProvider.php';
if (file_exists($appServicePath)) {
    $content = file_get_contents($appServicePath);
    if (strpos($content, "URL::forceScheme") !== false || strpos($content, "forceScheme") !== false) {
        $success[] = "✓ AppServiceProvider forces HTTPS in production";
    } else {
        $warnings[] = "⚠ AppServiceProvider may not force HTTPS scheme";
    }
} else {
    $errors[] = "✗ AppServiceProvider not found";
}

// Check 5: Sessions migration exists
echo "[5/7] Checking sessions table migration...\n";
$migrationFiles = glob($baseDir . '/database/migrations/*create_sessions_table.php');
if (count($migrationFiles) > 0) {
    $success[] = "✓ Sessions table migration exists";
} else {
    $warnings[] = "⚠ Sessions table migration not found (may cause issues if using database sessions)";
}

// Check 6: .env.example has correct settings
echo "[6/7] Checking .env.example...\n";
$envExamplePath = $baseDir . '/.env.example';
if (file_exists($envExamplePath)) {
    $content = file_get_contents($envExamplePath);
    $hasSecureCookie = strpos($content, 'SESSION_SECURE_COOKIE') !== false;
    $hasHttps = strpos($content, 'https://') !== false;
    
    if ($hasSecureCookie && $hasHttps) {
        $success[] = "✓ .env.example has HTTPS settings";
    } else {
        $warnings[] = "⚠ .env.example may be missing some HTTPS settings";
    }
} else {
    $warnings[] = "⚠ .env.example not found";
}

// Check 7: Forms have CSRF protection
echo "[7/7] Checking forms for CSRF tokens...\n";
$viewFiles = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir . '/resources/views'),
    RecursiveIteratorIterator::SELF_FIRST
);

$formsWithoutCsrf = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        // Check if file has forms
        if (preg_match('/<form[^>]*method=["\']post/i', $content)) {
            // Check if it has @csrf
            if (strpos($content, '@csrf') === false) {
                $formsWithoutCsrf[] = str_replace($baseDir . '/resources/views/', '', $file->getPathname());
            }
        }
    }
}

if (count($formsWithoutCsrf) === 0) {
    $success[] = "✓ All POST forms have @csrf tokens";
} else {
    $errors[] = "✗ Forms missing @csrf: " . implode(', ', $formsWithoutCsrf);
}

// Print results
echo "\n=== RESULTS ===\n\n";

if (count($success) > 0) {
    echo "SUCCESS:\n";
    foreach ($success as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (count($warnings) > 0) {
    echo "WARNINGS:\n";
    foreach ($warnings as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (count($errors) > 0) {
    echo "ERRORS:\n";
    foreach ($errors as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
    exit(1);
}

if (count($errors) === 0 && count($warnings) === 0) {
    echo "🎉 ALL CHECKS PASSED! Your application is configured for HTTPS.\n\n";
    echo "Next steps:\n";
    echo "1. Deploy to Railway\n";
    echo "2. Set environment variables (see RAILWAY_HTTPS_FIX.md)\n";
    echo "3. Run: php artisan migrate\n";
    echo "4. Run: php artisan config:clear\n";
    echo "5. Test form submissions\n";
} else {
    echo "⚠ Configuration complete with some warnings. Check above.\n";
}

echo "\n";
