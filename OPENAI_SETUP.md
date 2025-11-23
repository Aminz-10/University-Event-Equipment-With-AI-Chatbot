# UniEquip OpenAI Integration Setup Guide

## Overview
This guide explains how to set up OpenAI API integration with UniEquip's AI Chatbot for enhanced natural language processing and intelligent responses.

## Benefits of OpenAI Integration

✅ **Natural Language Understanding** - Better comprehension of varied question formats
✅ **Intelligent Responses** - Context-aware answers with system knowledge
✅ **Conversational AI** - Feels like chatting with a real assistant
✅ **Reduced Fallback Cases** - Fewer "I don't understand" responses
✅ **Better User Experience** - More helpful and engaging conversations
✅ **Scalable** - Handles any type of question

## Prerequisites

1. **OpenAI Account** - Sign up at https://platform.openai.com
2. **API Key** - Generate from your OpenAI dashboard
3. **PHP with cURL** - Required for API calls
4. **Active Internet Connection** - For API communication

## Installation Steps

### Step 1: Get Your OpenAI API Key

1. Go to https://platform.openai.com/account/api-keys
2. Click "Create new secret key"
3. Copy the key (you won't be able to see it again!)
4. Keep it safe and secure

### Step 2: Configure API Key in UniEquip

1. Open `ai_chatbot_api.php`
2. Find this line (around line 11):
```php
define('OPENAI_API_KEY', 'sk-your-api-key-here');
```

3. Replace with your actual API key:
```php
define('OPENAI_API_KEY', 'sk-xxxxxxxxxxxxxxxxxxxx');
```

### Step 3: Choose AI Model

The chatbot supports two models:

#### Option A: GPT-3.5 Turbo (Recommended)
```php
define('OPENAI_MODEL', 'gpt-3.5-turbo');
```
- Faster responses
- Lower cost (~$0.0005 per 1K tokens)
- Sufficient for most use cases
- ~500ms response time

#### Option B: GPT-4 (Premium)
```php
define('OPENAI_MODEL', 'gpt-4');
```
- Highest quality responses
- Slower (~2-5 seconds)
- Higher cost (~$0.03 per 1K tokens)
- Better reasoning abilities

### Step 4: Test the Integration

1. Log into UniEquip as a student or admin
2. Go to "AI Assistant" or "Chatbot"
3. Send a test message: "Tell me about UniEquip"
4. Check if you get a natural language response

### Step 5: Monitor API Usage

1. Visit https://platform.openai.com/account/usage/overview
2. Track your usage and costs
3. Set up usage limits if needed:
   - Go to Billing → Usage limits
   - Set a hard limit to prevent unexpected charges

## Configuration Details

### Model Parameters

```php
'temperature' => 0.7         // 0-2: Lower = more focused, Higher = more creative
'max_tokens' => 500          // Maximum response length (1 token ≈ 4 chars)
'top_p' => 1.0              // Controls diversity
'frequency_penalty' => 0.0   // How much to penalize repetition
'presence_penalty' => 0.0    // How much to encourage new topics
```

### Recommended Settings for Equipment Queries

```php
// More focused, consistent responses
'temperature' => 0.5,
'max_tokens' => 400,

// More creative, varied responses
'temperature' => 0.8,
'max_tokens' => 600,
```

## API Pricing

### Estimated Costs

| Model | Input (per 1M tokens) | Output (per 1M tokens) | Est. Cost per 1000 msgs |
|-------|----------------------|----------------------|------------------------|
| GPT-3.5-turbo | $0.50 | $1.50 | ~$0.10-0.20 |
| GPT-4 | $30.00 | $60.00 | ~$3.00-5.00 |

## Fallback Behavior

If OpenAI API is not configured or fails:
- ✓ Equipment queries still work (database-backed)
- ✓ System falls back to local AI response handler
- ✓ No service disruption
- ✓ User sees helpful alternative responses

## Troubleshooting

### Issue: "API key not valid"
**Solution:** Check your API key is correct and not expired

### Issue: "Rate limit exceeded"
**Solution:** Implement request queuing or upgrade your plan

### Issue: "Connection timeout"
**Solution:** Check internet connection and OpenAI server status

### Issue: "OpenAI API not responding"
**Solution:** System automatically falls back to local responses

## Error Logging

Errors are logged to your server logs. Check:
```
tail -f /var/log/php-errors.log
```

Or enable logging in `php.ini`:
```
error_log = /var/log/php-errors.log
```

## Security Best Practices

⚠️ **NEVER:**
- Commit your API key to version control
- Share your API key publicly
- Embed it in frontend code

✅ **DO:**
- Store in server-side configuration
- Use environment variables (advanced):
```php
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY'));
```
- Rotate keys regularly
- Monitor usage for suspicious activity

## Alternative: Using Environment Variables

For enhanced security:

1. Add to your `.env` file:
```
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxxxxxx
```

2. Update `ai_chatbot_api.php`:
```php
// Load .env if using composer
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

define('OPENAI_API_KEY', $_ENV['OPENAI_API_KEY'] ?? 'sk-your-api-key-here');
```

## Advanced: Streaming Responses

For long responses, implement streaming:

```php
'stream' => true  // Enable streaming for real-time responses
```

## Support & Resources

- **OpenAI Docs:** https://platform.openai.com/docs
- **API Reference:** https://platform.openai.com/docs/api-reference
- **Community Forum:** https://community.openai.com
- **Status Page:** https://status.openai.com

## Disabling OpenAI (Use Local AI Only)

If you want to use only local pattern-matching AI:

1. Change the API key to an invalid value:
```php
define('OPENAI_API_KEY', 'disabled');
```

2. The system will automatically use fallback responses
3. All database queries still work normally

## Performance Tips

1. **Cache Responses** - Store common questions' answers
2. **Rate Limiting** - Limit API calls per user
3. **Timeout Handling** - Set aggressive timeouts
4. **Batch Queries** - Group similar questions
5. **Monitor Costs** - Set daily/monthly budgets

---

**Questions?** Check the error logs or test the connection manually!
