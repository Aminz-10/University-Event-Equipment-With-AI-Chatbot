<?php
/**
 * UniEquip AI Configuration
 * Centralized configuration for AI Chatbot settings
 * 
 * SETUP INSTRUCTIONS:
 * 1. Get your OpenAI API key from https://platform.openai.com/account/api-keys
 * 2. Replace 'sk-your-api-key-here' with your actual key below
 * 3. Save and refresh the chatbot page
 */

// ========================================
// OPENAI CONFIGURATION
// ========================================

/**
 * OpenAI API Key
 * Get from: https://platform.openai.com/account/api-keys
 * 
 * IMPORTANT: Keep this secret! Never commit to version control.
 * For production, use environment variables instead.
 */
define('OPENAI_API_KEY', 'sk-your-api-key-here');

/**
 * OpenAI Model Selection
 * 
 * Options:
 * - 'gpt-3.5-turbo'  : Fast, cost-effective (Recommended)
 * - 'gpt-4'          : Better quality, slower, more expensive
 * - 'gpt-4-turbo'    : Balance of speed and quality
 */
define('OPENAI_MODEL', 'gpt-3.5-turbo');

/**
 * OpenAI API Endpoint
 * Standard endpoint - don't change unless using custom deployment
 */
define('OPENAI_ENDPOINT', 'https://api.openai.com/v1/chat/completions');

/**
 * Enable/Disable OpenAI Integration
 * Set to false to disable and use local AI only
 */
define('ENABLE_OPENAI', true);

// ========================================
// AI RESPONSE PARAMETERS
// ========================================

/**
 * Temperature (0 to 2)
 * - 0    : Deterministic, focused (best for factual responses)
 * - 0.7  : Balanced (default)
 * - 1.5+ : Creative, varied (best for brainstorming)
 */
define('AI_TEMPERATURE', 0.7);

/**
 * Maximum tokens in response
 * - 1 token ≈ 4 characters
 * - 500 = ~2000 characters max
 * - Adjust based on your needs
 */
define('AI_MAX_TOKENS', 500);

/**
 * API Request Timeout (seconds)
 * How long to wait for OpenAI response
 */
define('AI_TIMEOUT', 30);

// ========================================
// LOGGING & DEBUG
// ========================================

/**
 * Enable API logging
 * Logs all API calls for debugging
 */
define('AI_LOG_ENABLED', true);

/**
 * Log file path
 * Make sure this directory is writable
 */
define('AI_LOG_FILE', __DIR__ . '/logs/ai_chatbot.log');

/**
 * Debug mode
 * Shows detailed error messages (disable in production)
 */
define('AI_DEBUG_MODE', false);

// ========================================
// CACHING (Optional)
// ========================================

/**
 * Enable response caching
 * Cache similar questions to reduce API calls
 */
define('AI_CACHE_ENABLED', false);

/**
 * Cache duration (seconds)
 * How long to keep cached responses
 */
define('AI_CACHE_DURATION', 3600);

// ========================================
// RATE LIMITING
// ========================================

/**
 * Enable rate limiting
 * Prevent abuse by limiting requests per user
 */
define('AI_RATE_LIMIT_ENABLED', true);

/**
 * Requests per minute per user
 */
define('AI_RATE_LIMIT_PER_MINUTE', 10);

/**
 * Requests per hour per user
 */
define('AI_RATE_LIMIT_PER_HOUR', 100);

// ========================================
// FALLBACK BEHAVIOR
// ========================================

/**
 * Use local AI if OpenAI fails
 * System will automatically use pattern-matching AI on failure
 */
define('AI_FALLBACK_ENABLED', true);

/**
 * Log failed API calls
 */
define('AI_LOG_FAILURES', true);

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Check if OpenAI is properly configured
 */
function is_openai_configured() {
    return defined('OPENAI_API_KEY') && 
           OPENAI_API_KEY !== 'sk-your-api-key-here' && 
           !empty(OPENAI_API_KEY) &&
           ENABLE_OPENAI === true;
}

/**
 * Check if caching directory exists
 */
function ensure_log_directory() {
    if (AI_LOG_ENABLED) {
        $log_dir = dirname(AI_LOG_FILE);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
    }
}

/**
 * Log AI operations
 */
function log_ai_operation($message, $type = 'info') {
    if (!AI_LOG_ENABLED) return;
    
    ensure_log_directory();
    
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$type] $message\n";
    
    file_put_contents(AI_LOG_FILE, $log_message, FILE_APPEND);
}

/**
 * Get configuration status
 */
function get_ai_status() {
    return [
        'openai_configured' => is_openai_configured(),
        'model' => OPENAI_MODEL,
        'temperature' => AI_TEMPERATURE,
        'timeout' => AI_TIMEOUT,
        'rate_limit_enabled' => AI_RATE_LIMIT_ENABLED,
        'cache_enabled' => AI_CACHE_ENABLED,
        'fallback_enabled' => AI_FALLBACK_ENABLED,
        'debug_mode' => AI_DEBUG_MODE
    ];
}

// Initialize logging directory
ensure_log_directory();

// Log initialization
log_ai_operation('UniEquip AI initialized - OpenAI configured: ' . (is_openai_configured() ? 'YES' : 'NO'));

?>
