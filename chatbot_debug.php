<?php
/**
 * Chatbot API Debug Info
 * This file shows diagnostic information about the chatbot API
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chatbot API Debug</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        code { background: #f4f4f4; padding: 2px 4px; border-radius: 3px; font-family: monospace; }
        .code-block { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Chatbot API Debug Information</h1>
        
        <?php
        session_start();
        
        echo "<h2>Session Information</h2>";
        if (isset($_SESSION['user_number'])) {
            echo "<div class='status success'>";
            echo "✅ User is logged in<br>";
            echo "User Number: " . $_SESSION['user_number'] . "<br>";
            echo "Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'NOT SET') . "<br>";
            echo "</div>";
        } else {
            echo "<div class='status error'>";
            echo "❌ User is NOT logged in<br>";
            echo "This is required for the chatbot to work<br>";
            echo "</div>";
        }
        
        echo "<h2>Configuration Files</h2>";
        
        // Check config file
        if (file_exists('ai_openrouter_config.php')) {
            echo "<div class='status success'>✅ ai_openrouter_config.php exists</div>";
            include "ai_openrouter_config.php";
            
            echo "<h3>Configuration Values:</h3>";
            echo "API Key Set: " . (defined('OPENROUTER_API_KEY') ? "✅ YES" : "❌ NO") . "<br>";
            if (defined('OPENROUTER_API_KEY')) {
                $key = OPENROUTER_API_KEY;
                $is_placeholder = ($key === 'sk-or-your-api-key-here');
                if ($is_placeholder) {
                    echo "<div class='status error'>⚠️  API Key is STILL THE PLACEHOLDER!</div>";
                    echo "You must update line 24 in <code>ai_openrouter_config.php</code><br>";
                    echo "With your actual OpenRouter API key from https://openrouter.ai/keys<br>";
                } else {
                    echo "<div class='status success'>✅ API Key is set (not placeholder)</div>";
                    echo "Key preview: " . substr($key, 0, 15) . "...<br>";
                }
            }
            
            echo "Debug Mode: " . (AI_DEBUG_MODE ? "✅ ON" : "❌ OFF") . "<br>";
            echo "Enable OpenRouter: " . (ENABLE_OPENROUTER ? "✅ YES" : "❌ NO") . "<br>";
            echo "OpenRouter Model: " . OPENROUTER_MODEL . "<br>";
        } else {
            echo "<div class='status error'>❌ ai_openrouter_config.php NOT FOUND</div>";
        }
        
        echo "<h2>API Handler File</h2>";
        if (file_exists('ai_openrouter_api.php')) {
            echo "<div class='status success'>✅ ai_openrouter_api.php exists</div>";
        } else {
            echo "<div class='status error'>❌ ai_openrouter_api.php NOT FOUND</div>";
        }
        
        echo "<h2>Database Connection</h2>";
        if (file_exists('db.php')) {
            echo "<div class='status success'>✅ db.php exists</div>";
            include "db.php";
            if (isset($connect)) {
                echo "<div class='status success'>✅ Database connection established</div>";
                echo "Connection Type: " . get_class($connect) . "<br>";
            } else {
                echo "<div class='status error'>❌ Database connection NOT established</div>";
            }
        } else {
            echo "<div class='status error'>❌ db.php NOT FOUND</div>";
        }
        
        echo "<h2>Testing the API</h2>";
        if (isset($_SESSION['user_number'])) {
            echo "<p>Test message: <strong>How many projectors are available?</strong></p>";
            
            // Simulate API request
            $_POST['message'] = 'How many projectors are available?';
            
            echo "<p>Making API request...</p>";
            echo "<div class='code-block'>";
            echo "<strong>POST Request:</strong><br>";
            echo "URL: ai_openrouter_api.php<br>";
            echo "Method: POST<br>";
            echo "Message: " . $_POST['message'] . "<br>";
            echo "</div>";
            
            // Try to call the API
            ob_start();
            include "ai_openrouter_api.php";
            $api_response = ob_get_clean();
            
            echo "<p><strong>API Response:</strong></p>";
            echo "<div class='code-block'>";
            echo htmlspecialchars($api_response);
            echo "</div>";
            
            // Try to parse JSON
            $response_data = json_decode($api_response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                echo "<p><strong>Parsed Response:</strong></p>";
                if (isset($response_data['message'])) {
                    echo "<div class='status " . (isset($response_data['type']) && $response_data['type'] === 'error' ? 'error' : 'success') . "'>";
                    echo $response_data['message'];
                    echo "</div>";
                }
                if (isset($response_data['error'])) {
                    echo "<div class='status error'>";
                    echo "Error: " . $response_data['error'] . "<br>";
                    echo "Message: " . (isset($response_data['message']) ? $response_data['message'] : 'N/A');
                    echo "</div>";
                }
            } else {
                echo "<div class='status error'>";
                echo "Failed to parse API response as JSON<br>";
                echo "JSON Error: " . json_last_error_msg();
                echo "</div>";
            }
        } else {
            echo "<div class='status info'>";
            echo "ℹ️  You must be logged in to test the API<br>";
            echo "Please login first, then return to this page";
            echo "</div>";
        }
        ?>
        
        <h2>Recommendations</h2>
        <ul>
            <li><strong>Step 1:</strong> Make sure you're logged in as a student</li>
            <li><strong>Step 2:</strong> Check that <code>ai_openrouter_config.php</code> has your real API key (line 24)</li>
            <li><strong>Step 3:</strong> Verify database connection is working</li>
            <li><strong>Step 4:</strong> Check the API response above for specific errors</li>
        </ul>
        
        <h2>API Key Help</h2>
        <ol>
            <li>Go to: <code>https://openrouter.ai/keys</code></li>
            <li>Sign up or login</li>
            <li>Copy your API key</li>
            <li>Open: <code>ai_openrouter_config.php</code></li>
            <li>Find line 24: <code>define('OPENROUTER_API_KEY', '...');</code></li>
            <li>Replace <code>'sk-or-your-api-key-here'</code> with your actual key</li>
            <li>Save and refresh this page</li>
        </ol>
    </div>
</body>
</html>
