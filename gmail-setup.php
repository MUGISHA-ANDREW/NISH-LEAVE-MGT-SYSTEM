<?php

/**
 * Gmail API OAuth2 Token Generator
 * 
 * This script helps you generate a refresh token for Gmail API
 * Run: php gmail-setup.php
 */

echo "===========================================\n";
echo "   GMAIL API SETUP HELPER\n";
echo "===========================================\n\n";

echo "This helper will guide you through setting up Gmail API.\n\n";

echo "STEP 1: Create Google Cloud Project\n";
echo "-----------------------------------\n";
echo "1. Go to: https://console.cloud.google.com/\n";
echo "2. Create a new project (e.g., 'Nish Leave Management')\n";
echo "3. Wait for project creation to complete\n\n";

echo "STEP 2: Enable Gmail API\n";
echo "-------------------------\n";
echo "1. In your project, go to 'APIs & Services' > 'Library'\n";
echo "2. Search for 'Gmail API'\n";
echo "3. Click 'Enable'\n\n";

echo "STEP 3: Create OAuth Credentials\n";
echo "---------------------------------\n";
echo "1. Go to 'APIs & Services' > 'Credentials'\n";
echo "2. Click 'Create Credentials' > 'OAuth client ID'\n";
echo "3. Configure consent screen (if prompted):\n";
echo "   - User Type: External\n";
echo "   - App name: Nish Leave Management\n";
echo "   - User support email: your email\n";
echo "   - Developer contact: your email\n";
echo "   - Save and Continue (skip scopes, test users)\n";
echo "4. Create OAuth Client ID:\n";
echo "   - Application type: Web application\n";
echo "   - Name: Nish Leave Management\n";
echo "   - Authorized redirect URIs: http://localhost\n";
echo "5. Download the JSON file or copy Client ID and Client Secret\n\n";

echo "STEP 4: Get Refresh Token\n";
echo "--------------------------\n";
echo "Enter your Client ID: ";
$clientId = trim(fgets(STDIN));

if (empty($clientId)) {
    echo "❌ Client ID cannot be empty!\n";
    exit(1);
}

echo "Enter your Client Secret: ";
$clientSecret = trim(fgets(STDIN));

if (empty($clientSecret)) {
    echo "❌ Client Secret cannot be empty!\n";
    exit(1);
}

// Generate authorization URL
$redirectUri = 'http://localhost';
$scope = 'https://mail.google.com/';

$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => $scope,
    'access_type' => 'offline',
    'prompt' => 'consent'
]);

echo "\n===========================================\n";
echo "   AUTHORIZATION REQUIRED\n";
echo "===========================================\n\n";

echo "1. Open this URL in your browser:\n\n";
echo $authUrl . "\n\n";

echo "2. Sign in with your Gmail account: andrewmugisha699@gmail.com\n";
echo "3. Click 'Allow' to grant permissions\n";
echo "4. You'll be redirected to localhost with a code in the URL\n";
echo "5. Copy the 'code' parameter from the URL\n\n";

echo "Example URL after redirect:\n";
echo "http://localhost/?code=4/0AbcD3FgH1JKL...\n";
echo "                       ^^^^^^^^^^^^^^^^^ (copy this part)\n\n";

echo "Enter the authorization code: ";
$authCode = trim(fgets(STDIN));

if (empty($authCode)) {
    echo "❌ Authorization code cannot be empty!\n";
    exit(1);
}

// Exchange code for tokens
echo "\n🔄 Exchanging authorization code for tokens...\n";

$tokenUrl = 'https://oauth2.googleapis.com/token';
$postData = [
    'code' => $authCode,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri,
    'grant_type' => 'authorization_code'
];

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Failed to get tokens!\n";
    echo "Response: $response\n";
    exit(1);
}

$tokens = json_decode($response, true);

if (!isset($tokens['refresh_token'])) {
    echo "❌ No refresh token received!\n";
    echo "Response: $response\n";
    echo "\nTIP: Make sure you included 'prompt=consent' in the auth URL\n";
    exit(1);
}

echo "\n✅ SUCCESS! Tokens received!\n\n";

echo "===========================================\n";
echo "   ADD THESE TO YOUR .ENV FILE\n";
echo "===========================================\n\n";

echo "MAIL_MAILER=gmail\n";
echo "GMAIL_CLIENT_ID=\"$clientId\"\n";
echo "GMAIL_CLIENT_SECRET=\"$clientSecret\"\n";
echo "GMAIL_REFRESH_TOKEN=\"{$tokens['refresh_token']}\"\n";
echo "MAIL_FROM_ADDRESS=\"andrewmugisha699@gmail.com\"\n";
echo "MAIL_FROM_NAME=\"Nish Auto Limited\"\n\n";

echo "===========================================\n";
echo "   ADD THESE TO RAILWAY VARIABLES\n";
echo "===========================================\n\n";

echo "Variable Name: MAIL_MAILER  \n";
echo "Value: gmail\n\n";

echo "Variable Name: GMAIL_CLIENT_ID\n";
echo "Value: $clientId\n\n";

echo "Variable Name: GMAIL_CLIENT_SECRET\n";
echo "Value: $clientSecret\n\n";

echo "Variable Name: GMAIL_REFRESH_TOKEN\n";
echo "Value: {$tokens['refresh_token']}\n\n";

echo "Variable Name: MAIL_FROM_ADDRESS\n";
echo "Value: andrewmugisha699@gmail.com\n\n";

echo "Variable Name: MAIL_FROM_NAME\n";
echo "Value: Nish Auto Limited\n\n";

echo "===========================================\n\n";

echo "✅ Setup complete! After adding these variables:\n";
echo "  1. Local: Update .env and run: php artisan config:clear\n";
echo "  2. Railway: Add variables and wait for redeploy\n";
echo "  3. Test: php test-email-quick.php\n\n";
