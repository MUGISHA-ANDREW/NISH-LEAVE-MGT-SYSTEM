<?php

/**
 * Simple Email Test Script
 * Visit: https://your-app.railway.app/test-email-send.php?email=your@email.com
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        input, button { padding: 10px; margin: 10px 0; font-size: 16px; }
        button { background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>📧 Email Test Tool</h1>
    
    <div class="info">
        <strong>Configuration:</strong><br>
        Mailer: <?php echo config('mail.default'); ?><br>
        Host: <?php echo config('mail.mailers.smtp.host'); ?><br>
        Port: <?php echo config('mail.mailers.smtp.port'); ?><br>
        Encryption: <?php echo config('mail.mailers.smtp.encryption'); ?><br>
        From: <?php echo config('mail.from.address'); ?>
    </div>

    <?php
    if (isset($_GET['email']) && isset($_GET['send'])) {
        $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            echo '<div class="error">❌ Invalid email address</div>';
        } else {
            echo '<div class="info">Sending test email to: ' . htmlspecialchars($email) . '</div>';
            
            try {
                Log::info('Test email attempt to: ' . $email);
                Log::info('Mail config: ' . json_encode([
                    'default' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                ]));
                
                Mail::raw('This is a test email from your Nish Leave Management System. Your email configuration is working!', function ($message) use ($email) {
                    $message->to($email)
                            ->subject('✓ Test Email - Success!');
                });
                
                if (count(Mail::failures()) > 0) {
                    echo '<div class="error">❌ Failed to send email<br>Failures: ' . print_r(Mail::failures(), true) . '</div>';
                } else {
                    echo '<div class="success">✅ Email sent successfully!<br>Check your inbox (and spam folder): ' . htmlspecialchars($email) . '</div>';
                    Log::info('Test email sent successfully to: ' . $email);
                }
            } catch (\Exception $e) {
                echo '<div class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                Log::error('Test email failed: ' . $e->getMessage());
            }
        }
    }
    ?>

    <form method="GET">
        <label for="email">Enter email address to test:</label><br>
        <input type="email" name="email" id="email" style="width: 300px;" 
               value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" required>
        <br>
        <button type="submit" name="send" value="1">Send Test Email</button>
    </form>

    <hr>
    <p><a href="/verify-mail-config.php">← View Full Configuration</a></p>
</body>
</html>
