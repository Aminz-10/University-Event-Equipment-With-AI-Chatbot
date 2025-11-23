<?php
/**
 * UniEquip OpenRouter Configuration
 * Configuration for OpenRouter API with Grok-4.1 model
 * 
 * SETUP INSTRUCTIONS:
 * 1. Get your OpenRouter API key from https://openrouter.ai/keys
 * 2. Replace 'sk-or-your-api-key-here' with your actual key below
 * 3. Save and refresh the chatbot page
 */

// ========================================
// OPENROUTER CONFIGURATION
// ========================================

/**
 * OpenRouter API Key
 * Get from: https://openrouter.ai/keys
 * 
 * IMPORTANT: Keep this secret! Never commit to version control.
 * For production, use environment variables instead.
 */
define('OPENROUTER_API_KEY', 'sk-or-your-api-key-here');

/**
 * OpenRouter Model Selection
 * 
 * Options:
 * - 'x-ai/grok-4.1-fast:free'     : Free, fast, reasoning enabled (Recommended)
 * - 'x-ai/grok-4.1-beta:free'     : Free, advanced reasoning
 * - 'openai/gpt-4o'                : Paid, high quality
 * - 'anthropic/claude-opus'        : Paid, best quality
 */
define('OPENROUTER_MODEL', 'x-ai/grok-4.1-fast:free');

/**
 * OpenRouter API Endpoint
 * Standard endpoint - don't change unless using custom deployment
 */
define('OPENROUTER_ENDPOINT', 'https://openrouter.ai/api/v1/chat/completions');

/**
 * Enable/Disable OpenRouter Integration
 * Set to false to disable and use local AI only
 */
define('ENABLE_OPENROUTER', true);

/**
 * Enable Reasoning/Extended Thinking
 * Uses extended thinking for complex reasoning tasks
 * Note: Some models support this, others don't
 */
define('ENABLE_REASONING', true);

/**
 * Reasoning Type
 * 'enabled' for basic reasoning, or specific reasoning mode if supported
 */
define('REASONING_CONFIG', 'enabled');

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
 * - Higher = longer responses, more tokens consumed
 */
define('AI_MAX_TOKENS', 500);

/**
 * Max reasoning tokens (for models supporting extended thinking)
 * - Used for internal reasoning before response
 * - Separate from max_tokens
 */
define('AI_MAX_REASONING_TOKENS', 1000);

/**
 * API Request Timeout (seconds)
 * How long to wait for OpenRouter response
 * Extended thinking may take longer, so higher timeout recommended
 */
define('AI_TIMEOUT', 45);

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
define('AI_LOG_FILE', __DIR__ . '/logs/ai_openrouter.log');

/**
 * Debug mode
 * Shows detailed error messages (disable in production)
 */
define('AI_DEBUG_MODE', true);

/**
 * Log reasoning details
 * Save extended thinking traces for analysis
 */
define('AI_LOG_REASONING', false);

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

/**
 * Token usage limits (for cost control)
 * Maximum tokens allowed per day across all users
 */
define('AI_DAILY_TOKEN_LIMIT', 100000);

// ========================================
// FALLBACK BEHAVIOR
// ========================================

/**
 * Use local AI if OpenRouter fails
 * System will automatically use pattern-matching AI on failure
 */
define('AI_FALLBACK_ENABLED', true);

/**
 * Log failed API calls
 */
define('AI_LOG_FAILURES', true);

// ========================================
// CONVERSATION MEMORY
// ========================================

/**
 * Keep conversation history for reasoning
 * Preserves reasoning_details between messages
 */
define('AI_PRESERVE_REASONING', true);

/**
 * Max conversation history length
 * How many messages to keep for context
 */
define('AI_HISTORY_LENGTH', 5);

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Check if OpenRouter is properly configured
 */
function is_openrouter_configured() {
    return defined('OPENROUTER_API_KEY') && 
           OPENROUTER_API_KEY !== 'sk-or-your-api-key-here' && 
           !empty(OPENROUTER_API_KEY) &&
           ENABLE_OPENROUTER === true;
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
 * Log reasoning details (for debugging extended thinking)
 */
function log_reasoning_details($reasoning_details, $question) {
    if (!AI_LOG_REASONING) return;
    
    ensure_log_directory();
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "\n" . str_repeat("=", 80) . "\n";
    $log_entry .= "[$timestamp] REASONING TRACE\n";
    $log_entry .= "Question: $question\n";
    $log_entry .= "Reasoning Details:\n" . print_r($reasoning_details, true) . "\n";
    
    file_put_contents(AI_LOG_FILE, $log_entry, FILE_APPEND);
}

/**
 * Get configuration status
 */
function get_ai_status() {
    return [
        'openrouter_configured' => is_openrouter_configured(),
        'model' => OPENROUTER_MODEL,
        'temperature' => AI_TEMPERATURE,
        'timeout' => AI_TIMEOUT,
        'reasoning_enabled' => ENABLE_REASONING,
        'rate_limit_enabled' => AI_RATE_LIMIT_ENABLED,
        'cache_enabled' => AI_CACHE_ENABLED,
        'fallback_enabled' => AI_FALLBACK_ENABLED,
        'debug_mode' => AI_DEBUG_MODE,
        'preserve_reasoning' => AI_PRESERVE_REASONING
    ];
}

/**
 * Format token usage for logging/monitoring
 */
function format_token_info($usage_data) {
    $info = [];
    if (isset($usage_data['prompt_tokens'])) {
        $info[] = "Prompt: " . $usage_data['prompt_tokens'];
    }
    if (isset($usage_data['completion_tokens'])) {
        $info[] = "Completion: " . $usage_data['completion_tokens'];
    }
    if (isset($usage_data['total_tokens'])) {
        $info[] = "Total: " . $usage_data['total_tokens'];
    }
    return implode(" | ", $info);
}

// Initialize logging directory
ensure_log_directory();

// Log initialization
log_ai_operation('UniEquip AI initialized - OpenRouter configured: ' . (is_openrouter_configured() ? 'YES' : 'NO'));
log_ai_operation('Model: ' . OPENROUTER_MODEL . ' | Reasoning: ' . (ENABLE_REASONING ? 'YES' : 'NO'));

?>
