<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body p {
            margin: 0 0 16px;
            color: #555;
        }
        .reset-button {
            display: inline-block;
            padding: 14px 32px;
            margin: 24px 0;
            background-color: #667eea;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .reset-button:hover {
            background-color: #5568d3;
        }
        .link-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 16px;
            margin: 20px 0;
            word-break: break-all;
        }
        .link-box p {
            margin: 0;
            font-size: 13px;
            color: #666;
        }
        .link-box a {
            color: #667eea;
            text-decoration: none;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .email-footer p {
            margin: 8px 0;
            font-size: 13px;
            color: #6c757d;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 16px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Password Reset Request</h1>
        </div>
        
        <div class="email-body">
            <p>Hello,</p>
            
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <div style="text-align: center;">
                <a href="{{ $link }}" class="reset-button">Reset Password</a>
            </div>
            
            <p>This password reset link will expire in 60 minutes.</p>
            
            <div class="link-box">
                <p><strong>If you're having trouble clicking the button, copy and paste this URL into your web browser:</strong></p>
                <p><a href="{{ $link }}">{{ $link }}</a></p>
            </div>
            
            <div class="warning-box">
                <p><strong>⚠️ Security Notice:</strong> If you did not request a password reset, please ignore this email. Your password will remain unchanged.</p>
            </div>
        </div>
        
        <div class="email-footer">
            <p><strong>Leave Management System</strong></p>
            <p>&copy; {{ date('Y') }} All rights reserved.</p>
            <p style="margin-top: 16px; font-size: 12px;">
                This is an automated message, please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
