<?php
/**
 * Test API to debug chatbot issues
 */
session_start();

echo "=== CHATBOT API DEBUG TEST ===\n\n";

// Test 1: Check if session is set
echo "Test 1: Session User Number\n";
echo "Session set: " . (isset($_SESSION['user_number']) ? "YES" : "NO") . "\n";
if (isset($_SESSION['user_number'])) {
    echo "User number: " . $_SESSION['user_number'] . "\n";
}
echo "\n";

// Test 2: Check config file
echo "Test 2: Config File\n";
include "ai_openrouter_config.php";
echo "Config loaded: YES\n";
echo "API Key set: " . (defined('OPENROUTER_API_KEY') ? "YES" : "NO") . "\n";
if (defined('OPENROUTER_API_KEY')) {
    $key_preview = substr(OPENROUTER_API_KEY, 0, 10) . "...";
    echo "API Key (preview): " . $key_preview . "\n";
    echo "Is placeholder: " . (OPENROUTER_API_KEY === 'sk-or-your-api-key-here' ? "YES (PLACEHOLDER)" : "NO (REAL KEY)") . "\n";
}
echo "ENABLE_OPENROUTER: " . (ENABLE_OPENROUTER ? "true" : "false") . "\n";
echo "\n";

// Test 3: Check if is_openrouter_configured works
echo "Test 3: is_openrouter_configured()\n";
echo "Result: " . (is_openrouter_configured() ? "TRUE" : "FALSE") . "\n";
echo "\n";

// Test 4: Check database
echo "Test 4: Database Connection\n";
include "db.php";
if (isset($connect)) {
    echo "DB Connected: YES\n";
    echo "DB Type: " . get_class($connect) . "\n";
} else {
    echo "DB Connected: NO\n";
}
echo "\n";

// Test 5: Try to process a test message
echo "Test 5: Processing Test Message\n";
echo "Attempting to process message...\n";

if (!isset($_SESSION['user_number'])) {
    $_SESSION['user_number'] = 1; // Fake login for testing
    $_SESSION['role'] = 'student';
}

include "ai_openrouter_api.php";

// Note: This will actually run the API processing
// So we'll just show what would happen
echo "API Handler included successfully\n";
echo "\n";

echo "=== DEBUG TEST COMPLETE ===\n";
?>
