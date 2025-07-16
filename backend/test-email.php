<?php

/**
 * Simple email testing script for YLA Umzug system
 * 
 * This script can be run independently to test email configuration
 * without requiring the full Laravel application setup.
 */

// Basic email configuration test
function testEmailConfiguration() {
    echo "🔧 Testing Email Configuration\n";
    echo "================================\n\n";
    
    // Check if required environment variables are set
    $requiredVars = [
        'MAIL_HOST' => 'SMTP Host',
        'MAIL_PORT' => 'SMTP Port', 
        'MAIL_USERNAME' => 'SMTP Username',
        'MAIL_FROM_ADDRESS' => 'From Address',
        'BUSINESS_EMAIL' => 'Business Email'
    ];
    
    $missing = [];
    foreach ($requiredVars as $var => $description) {
        $value = getenv($var);
        if (empty($value)) {
            $missing[] = "$var ($description)";
        } else {
            echo "✅ $description: $value\n";
        }
    }
    
    if (!empty($missing)) {
        echo "\n❌ Missing required environment variables:\n";
        foreach ($missing as $var) {
            echo "   - $var\n";
        }
        echo "\nPlease configure these in your .env file.\n";
        return false;
    }
    
    echo "\n✅ All required email configuration variables are set!\n\n";
    return true;
}

// Test SMTP connection
function testSMTPConnection() {
    echo "🌐 Testing SMTP Connection\n";
    echo "==========================\n\n";
    
    $host = getenv('MAIL_HOST');
    $port = getenv('MAIL_PORT') ?: 587;
    
    if (empty($host)) {
        echo "❌ MAIL_HOST not configured\n";
        return false;
    }
    
    echo "Testing connection to $host:$port...\n";
    
    $connection = @fsockopen($host, $port, $errno, $errstr, 10);
    
    if (!$connection) {
        echo "❌ Cannot connect to SMTP server: $errstr ($errno)\n";
        echo "   Check your MAIL_HOST and MAIL_PORT settings\n";
        return false;
    }
    
    fclose($connection);
    echo "✅ SMTP server is reachable!\n\n";
    return true;
}

// Display email template information
function showEmailTemplates() {
    echo "📧 Email Templates\n";
    echo "==================\n\n";
    
    $templates = [
        'Quote Request (Business)' => 'resources/views/emails/quote-request.blade.php',
        'Quote Request Text (Business)' => 'resources/views/emails/quote-request-text.blade.php',
        'Quote Confirmation (Customer)' => 'resources/views/emails/quote-confirmation.blade.php',
        'Quote Confirmation Text (Customer)' => 'resources/views/emails/quote-confirmation-text.blade.php'
    ];
    
    foreach ($templates as $name => $path) {
        if (file_exists($path)) {
            echo "✅ $name: $path\n";
        } else {
            echo "❌ $name: $path (missing)\n";
        }
    }
    
    echo "\n";
}

// Show next steps
function showNextSteps() {
    echo "🚀 Next Steps\n";
    echo "=============\n\n";
    
    echo "1. Configure Production Email Settings:\n";
    echo "   - Update .env with your SMTP provider settings\n";
    echo "   - Use info@yla-umzug.de as the business email\n";
    echo "   - Test with a real email provider (not mailpit)\n\n";
    
    echo "2. Test Email Delivery:\n";
    echo "   - Submit a test quote through the calculator\n";
    echo "   - Check that both business and customer emails are sent\n";
    echo "   - Verify emails don't go to spam folder\n\n";
    
    echo "3. Monitor Email Delivery:\n";
    echo "   - Check Laravel logs for email errors\n";
    echo "   - Monitor email delivery rates\n";
    echo "   - Set up email reputation monitoring\n\n";
    
    echo "4. Production Checklist:\n";
    echo "   - ✓ SMTP credentials configured\n";
    echo "   - ✓ SPF/DKIM records set up\n";
    echo "   - ✓ Email templates tested\n";
    echo "   - ✓ Both HTML and text versions work\n";
    echo "   - ✓ Mobile email clients tested\n\n";
}

// Main execution
echo "YLA Umzug Email System Test\n";
echo "===========================\n\n";

// Load environment variables if .env exists
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $value = trim($value, '"\'');
            putenv("$key=$value");
        }
    }
    echo "✅ Loaded .env configuration\n\n";
} else {
    echo "⚠️  No .env file found - using system environment variables\n\n";
}

// Run tests
$configOk = testEmailConfiguration();
$connectionOk = testSMTPConnection();
showEmailTemplates();
showNextSteps();

// Summary
echo "📊 Test Summary\n";
echo "===============\n";
echo "Configuration: " . ($configOk ? "✅ OK" : "❌ Issues") . "\n";
echo "SMTP Connection: " . ($connectionOk ? "✅ OK" : "❌ Issues") . "\n";

if ($configOk && $connectionOk) {
    echo "\n🎉 Email system appears to be ready for testing!\n";
    echo "Submit a test quote to verify end-to-end email delivery.\n";
} else {
    echo "\n⚠️  Please fix the issues above before testing email delivery.\n";
}

echo "\n";