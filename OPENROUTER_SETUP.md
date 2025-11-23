# 🤖 OpenRouter Integration with Grok Extended Reasoning

## Overview

This implementation replaces OpenAI integration with OpenRouter's **Grok-4.1** model, which provides:
- ✨ Extended reasoning capabilities (thinks through problems)
- 🚀 FREE tier available (x-ai/grok-4.1-fast:free)
- 💰 Lower costs than GPT-4
- 🧠 Reasoning preservation across conversation turns

## Quick Setup (3 Steps)

### 1️⃣ Get OpenRouter API Key

```
Visit: https://openrouter.ai/keys
- Sign up for free account
- Click "Create new key"
- Copy the key (starts with sk-or-...)
```

### 2️⃣ Configure the System

Edit `ai_openrouter_config.php`:

```php
// Line 18 - Replace with your actual key
define('OPENROUTER_API_KEY', 'sk-or-your-api-key-here');
```

Change to:

```php
define('OPENROUTER_API_KEY', 'sk-or-xxxxxxxxxxxx');
```

### 3️⃣ Update Chatbot API Reference

Edit `chatbot.php` - Find this line (around line 150):

```javascript
url: 'ai_chatbot_api.php',
```

Change to:

```javascript
url: 'ai_openrouter_api.php',
```

### ✅ Done! Test It

1. Login to UniEquip
2. Open AI Assistant
3. Ask: "Explain how equipment rental works"
4. You should see reasoning in action!

---

## How TypeScript Implementation Works in PHP

### Original TypeScript Code
```typescript
let response = await fetch("https://openrouter.ai/api/v1/chat/completions", {
  method: "POST",
  headers: {
    "Authorization": `Bearer ${<OPENROUTER_API_KEY>}`,
    "Content-Type": "application/json"
  },
  body: JSON.stringify({
    "model": "x-ai/grok-4.1-fast:free",
    "messages": [...],
    "reasoning": {"enabled": true}
  })
});
```

### PHP Equivalent
```php
private function callOpenRouterAPI($data) {
    $ch = curl_init(OPENROUTER_ENDPOINT);
    
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENROUTER_API_KEY,
        'HTTP-Referer: ' . $_SERVER['HTTP_HOST'],
        'X-Title: UniEquip'
    ];
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => AI_TIMEOUT,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);
    
    $response = curl_exec($ch);
    // ... error handling and JSON parsing
    return json_decode($response, true);
}
```

---

## Reasoning Details Preservation

### TypeScript Approach
```typescript
// Extract and preserve reasoning
const result = await response.json();
response = result.choices[0].message;

const messages = [
  { role: 'user', content: "First question?" },
  {
    role: 'assistant',
    content: response.content,
    reasoning_details: response.reasoning_details  // ← PRESERVED
  },
  { role: 'user', content: "Follow-up question?" }
];

// Send back in next request
const response2 = await fetch(..., {
  body: JSON.stringify({
    model: "x-ai/grok-4.1-fast:free",
    messages: messages  // ← Includes preserved reasoning
  })
});
```

### PHP Implementation
```php
private function queryOpenRouterWithReasoning($user_question) {
    // Initialize messages with system context
    $messages = [
        ['role' => 'system', 'content' => $system_context]
    ];
    
    // Add conversation history with preserved reasoning
    if (AI_PRESERVE_REASONING && count($this->conversation_history) > 0) {
        foreach ($this->conversation_history as $msg) {
            if ($msg['role'] === 'assistant' && isset($msg['reasoning_details'])) {
                $messages[] = [
                    'role' => 'assistant',
                    'content' => $msg['content'],
                    'reasoning_details' => $msg['reasoning_details']  // ← PRESERVED
                ];
            } else {
                $messages[] = $msg;
            }
        }
    }
    
    // Add current question
    $messages[] = [
        'role' => 'user',
        'content' => $user_question
    ];
    
    // Send to OpenRouter
    $request_data = [
        'model' => OPENROUTER_MODEL,
        'messages' => $messages,
        'reasoning' => [
            'enabled' => true,
            'type' => REASONING_CONFIG
        ]
    ];
    
    $response = $this->callOpenRouterAPI($request_data);
    
    // Extract and preserve for next turn
    $message = $response['choices'][0]['message'];
    $reasoning_details = $message['reasoning_details'] ?? null;
    
    // Save to conversation history
    $this->addToHistory('assistant', $ai_response, $reasoning_details);
}
```

---

## Configuration Reference

### Available Models

| Model | Speed | Quality | Cost | Reasoning |
|-------|-------|---------|------|-----------|
| **x-ai/grok-4.1-fast:free** | ⚡ Fast | Good | FREE | ✅ Yes |
| x-ai/grok-4.1-beta:free | ⚡ Fast | Excellent | FREE | ✅ Yes |
| openai/gpt-4o | 🔶 Medium | Excellent | $$ | ❌ No |
| anthropic/claude-opus | 🐢 Slow | Best | $$ | ✅ Yes |

**Recommended:** `x-ai/grok-4.1-fast:free` (FREE with excellent reasoning!)

### Key Settings

```php
// In ai_openrouter_config.php:

// Enable/disable reasoning
define('ENABLE_REASONING', true);

// Max tokens for reasoning (internal thinking)
define('AI_MAX_REASONING_TOKENS', 1000);

// Preserve reasoning_details across messages
define('AI_PRESERVE_REASONING', true);

// Maximum conversation history to maintain
define('AI_HISTORY_LENGTH', 5);

// Temperature (0-2): Lower = focused, Higher = creative
define('AI_TEMPERATURE', 0.7);

// Response length limit
define('AI_MAX_TOKENS', 500);

// API timeout (seconds) - reasoning may be slower
define('AI_TIMEOUT', 45);
```

---

## Usage Examples

### Example 1: Simple Question
```
User: "What equipment do we have?"
↓
AI reasons through system data
↓
Response: "We have 45 items across 8 categories..."
```

### Example 2: Complex Question with Reasoning
```
User: "Why would a student need a projector for their booking?"
↓
AI:
  Internal Reasoning: "Think about academic use cases...
    - presentations, seminars, project showcases..."
  Response: "Students typically need projectors for...
    ✓ Class presentations
    ✓ Group projects
    ✓ Thesis defense..."
```

### Example 3: Follow-up with Preserved Reasoning
```
Message 1:
  Q: "Explain our booking system"
  A: [Extended reasoning about workflow] "Our system works like..."

Message 2:
  Q: "Can I book multiple items at once?"
  A: [Uses previous reasoning as context] "Yes! Since our system allows..."
     (Reasoning_details from Message 1 preserved ↑)
```

---

## Error Handling

### If API Key is Invalid
```
❌ System logs: "OpenRouter returned invalid authentication"
↓
✅ Automatically falls back to local AI pattern matching
↓
User sees: "Unable to process... [helpful fallback response]"
```

### If API is Down
```
❌ System logs: "cURL Error: Connection refused"
↓
✅ Automatic fallback to local AI
↓
User never experiences outage
```

### Debug Mode
```php
// In ai_openrouter_config.php:
define('AI_DEBUG_MODE', true);  // Shows detailed errors
define('AI_LOG_REASONING', true);  // Saves reasoning traces
```

Check logs: `htdocs/logs/ai_openrouter.log`

---

## Cost Comparison

### OpenRouter Pricing (as of Nov 2025)

**Free Tier:**
- Model: x-ai/grok-4.1-fast:free
- Cost: **$0** (completely free!)
- Reasoning: ✅ Yes
- Limitations: Fair use policy

**Paid Tier:**
- Model: x-ai/grok-4.1-beta:free
- Cost: $0.00 per 1M input tokens | $0.02 per 1M output tokens
- Reasoning: ✅ Yes
- Example: 100 messages/day ≈ $2-3/month

**vs OpenAI GPT-4o:**
- Input: $0.005 per 1K tokens
- Output: $0.015 per 1K tokens  
- Example: 100 messages/day ≈ $30+/month

**Savings:** 90% cost reduction with Grok!

---

## Files Modified/Created

### New Files
- ✅ `ai_openrouter_config.php` - Configuration (replace ai_config.php)
- ✅ `ai_openrouter_api.php` - API handler (replace ai_chatbot_api.php)

### Files to Update
- 📝 `chatbot.php` - Change API endpoint from `ai_chatbot_api.php` to `ai_openrouter_api.php`

### Files to Keep
- ✅ `user_dashboard.php` - No changes needed
- ✅ `admin_dashboard.php` - No changes needed
- ✅ `database.php` - No changes needed

---

## Troubleshooting

### "Authorization failed" error?
```
✓ Check API key format (should start with sk-or-)
✓ Visit https://openrouter.ai/keys to verify
✓ Make sure key is copied completely (no extra spaces)
✓ Restart browser and try again
```

### "Connection timeout" error?
```
✓ Check internet connection
✓ Verify OpenRouter API is up: https://status.openrouter.ai
✓ Increase AI_TIMEOUT in ai_openrouter_config.php (try 60 seconds)
✓ Try disabling reasoning temporarily (set ENABLE_REASONING = false)
```

### Reasoning not showing?
```
✓ Make sure ENABLE_REASONING = true in config
✓ Check AI_LOG_REASONING = true to see logs
✓ Verify model supports reasoning (Grok does, OpenAI GPT-4o doesn't)
✓ Check htdocs/logs/ai_openrouter.log for details
```

### High token usage?
```
✓ Reduce AI_HISTORY_LENGTH (fewer messages to preserve)
✓ Reduce AI_MAX_TOKENS (shorter responses)
✓ Disable reasoning for simple queries
✓ Use local AI for equipment database queries (automatic)
```

---

## Advanced Usage

### Manual Reasoning Control

For specific queries, you can adjust reasoning:

```php
// In ai_openrouter_api.php, modify queryOpenRouterWithReasoning():

// Disable reasoning for quick database queries
if ($this->use_openrouter && $complexity_score < 3) {
    $request_data['reasoning'] = ['enabled' => false];
} else {
    $request_data['reasoning'] = ['enabled' => true];
}
```

### Custom System Prompts

Edit `buildSystemContext()` in `ai_openrouter_api.php` to customize AI behavior:

```php
private function buildSystemContext() {
    $context = "You are UniEquip AI Assistant...
               CUSTOM INSTRUCTION: Always think before responding.
               Think about edge cases and provide thorough answers.";
    return $context;
}
```

### Monitoring & Analytics

View logs:
```bash
# Terminal command
tail -f htdocs/logs/ai_openrouter.log
```

Log includes:
- API calls made
- Token usage
- Response times
- Reasoning traces (if enabled)
- Errors and fallbacks

---

## Next Steps

1. ✅ Get OpenRouter API key (free!)
2. ✅ Update configuration
3. ✅ Update chatbot.php endpoint
4. ✅ Test in chatbot
5. ✅ Monitor logs
6. ✅ Enjoy reasoning-powered AI! 🧠

---

## Support Resources

- **OpenRouter Docs:** https://openrouter.ai/docs
- **Grok Model Card:** https://openrouter.ai/models/x-ai/grok-4.1-fast:free
- **API Status:** https://status.openrouter.ai
- **Community:** https://openrouter.ai/discussions

---

**Questions? Check the logs!** 📋
```
htdocs/logs/ai_openrouter.log
```

Set `AI_DEBUG_MODE = true` to see detailed error messages.
