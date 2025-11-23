# 🔄 Migration Guide: OpenAI → OpenRouter

## Quick Summary

Your AI chatbot has been upgraded from **OpenAI (GPT-3.5-turbo)** to **OpenRouter (Grok-4.1)** with reasoning capabilities.

**Benefits:**
- 🚀 90% cost reduction (FREE tier available!)
- 🧠 Extended reasoning (AI thinks through problems)
- ⚡ Faster responses
- 🛡️ Same fallback reliability

---

## What Changed?

| Component | Before | After |
|-----------|--------|-------|
| **API Provider** | OpenAI | OpenRouter |
| **Model** | gpt-3.5-turbo | x-ai/grok-4.1-fast:free |
| **Reasoning** | None | ✅ Extended thinking |
| **Cost** | $0.0015/1K tokens | FREE (free tier) |
| **API Key Prefix** | sk-... | sk-or-... |
| **Config File** | ai_config.php | ai_openrouter_config.php |
| **API Handler** | ai_chatbot_api.php | ai_openrouter_api.php |

---

## 🔧 Migration Steps

### Step 1: Get New API Key (Required)
```
Old API Key: sk-proj-... (OpenAI)
New API Key: sk-or-...   (OpenRouter)

Visit: https://openrouter.ai/keys
- Create free account
- Click "Create new key"
- Copy the key starting with "sk-or-"
```

### Step 2: Update Configuration
**Old file:** `ai_config.php` (still exists, not used)
**New file:** `ai_openrouter_config.php` (now active)

Edit `ai_openrouter_config.php`:
```php
// Line 24 - Replace with your OpenRouter API key
define('OPENROUTER_API_KEY', 'sk-or-your-actual-key-here');

// Optional: Choose model (already set to free tier)
define('OPENROUTER_MODEL', 'x-ai/grok-4.1-fast:free');

// Optional: Enable/disable reasoning
define('ENABLE_REASONING', true);  // ← New feature!
```

### Step 3: Verify Chatbot Updated
The chatbot now points to: `ai_openrouter_api.php`

Check `chatbot.php` line ~788:
```javascript
// ✅ Correct (updated)
fetch('ai_openrouter_api.php', {

// ❌ Old (shouldn't see this)
fetch('ai_chatbot_api.php', {
```

### Step 4: Test
```
1. Login to UniEquip
2. Open "AI Assistant" from menu
3. Ask: "Tell me about the booking system"
4. Should see thoughtful response with reasoning 🧠
```

### Step 5: Check Logs
```
File: htdocs/logs/ai_openrouter.log
Should show:
- API calls made
- Token usage
- Success confirmations
```

---

## ✅ Verification Checklist

- [ ] OpenRouter API key obtained from https://openrouter.ai/keys
- [ ] `ai_openrouter_config.php` updated with new key
- [ ] `chatbot.php` pointing to `ai_openrouter_api.php`
- [ ] Chatbot tested with simple question
- [ ] Chatbot tested with complex question
- [ ] Check logs for successful API calls
- [ ] No errors in browser console (F12)

---

## 📋 File Changes Summary

### New Files (Active)
- ✅ `ai_openrouter_config.php` - Configuration
- ✅ `ai_openrouter_api.php` - API handler
- ✅ `OPENROUTER_SETUP.md` - Setup guide
- ✅ `OPENROUTER_IMPLEMENTATION.md` - Full documentation

### Old Files (Inactive)
- 📦 `ai_config.php` - Keep (backup), but not used
- 📦 `ai_chatbot_api.php` - Keep (backup), but not used
- 📦 `OPENAI_SETUP.md` - Keep (reference), but outdated
- 📦 `OPENAI_EXAMPLES.md` - Keep (reference), but outdated

### Updated Files
- ✏️ `chatbot.php` - Changed API endpoint to `ai_openrouter_api.php`

---

## 🚨 Troubleshooting

### Problem: "Unauthorized" or "Invalid API Key"
```
Solution:
1. Visit https://openrouter.ai/keys
2. Verify key format (sk-or-xxxxx)
3. Copy entire key (no extra spaces)
4. Paste into ai_openrouter_config.php line 24
5. Save file
6. Refresh browser
```

### Problem: "Connection timeout" or "Slow responses"
```
Solution 1: Increase timeout
- Edit ai_openrouter_config.php line 51
- Change AI_TIMEOUT from 45 to 60 seconds

Solution 2: Disable reasoning (if needed)
- Set ENABLE_REASONING = false for speed
- Reasoning adds 1-2 seconds per response

Solution 3: Check internet connection
- Verify you can access https://openrouter.ai
```

### Problem: Responses missing reasoning details
```
Solution:
1. Verify ENABLE_REASONING = true in config
2. Check AI_LOG_REASONING = true to see logs
3. View htdocs/logs/ai_openrouter.log for details
4. Grok model may not have reasoning for simple queries
```

### Problem: Old OpenAI responses still showing
```
Solution:
1. Clear browser cache (Ctrl+Shift+Delete)
2. Verify chatbot.php points to ai_openrouter_api.php
3. Check server logs for errors
4. Restart your browser
```

---

## 💰 Cost Comparison

### OpenAI (Old Setup)
```
Model: gpt-3.5-turbo
Cost: $0.0015 per 1K input tokens + $0.002 per 1K output tokens
Typical usage (100 msgs/day): $3-5/month
No free tier
```

### OpenRouter (New Setup)
```
Model: x-ai/grok-4.1-fast:free
Cost: COMPLETELY FREE ($0/month)
With reasoning: Still FREE!
Perfect for testing and small deployments
```

**Savings: 100% (free!)** 🎉

### Optional: Paid Models
```
If you want more advanced reasoning:
- claude-opus: $0.015 per 1K input, $0.075 per 1K output
- gpt-4o: $0.005 per 1K input, $0.015 per 1K output
Still cheaper than standalone OpenAI account!
```

---

## 🧠 Understanding Reasoning

### Before (OpenAI)
```
Q: "How should I book equipment?"
A: "Go to the dashboard and click book button."
```

### After (OpenRouter with Grok Reasoning)
```
Q: "How should I book equipment?"

[Internal Reasoning: Think about the booking workflow...
 - User needs to browse available items
 - Select items they want
 - Choose dates
 - Confirm booking
 - Wait for admin approval]

A: "To book equipment:
   1. Login and go to Dashboard
   2. Browse available equipment
   3. Select items and dates
   4. Submit booking request
   5. Wait for admin approval
   Status shows when approved!"
```

**Much more helpful!** 🧠✨

---

## 🔐 Security Notes

### Old Setup (OpenAI)
```php
// In ai_config.php
define('OPENAI_API_KEY', 'sk-proj-...');
```

### New Setup (OpenRouter)
```php
// In ai_openrouter_config.php
define('OPENROUTER_API_KEY', 'sk-or-...');
```

**Security Best Practices:**
- ✅ Never commit API keys to Git
- ✅ Store only on server (not in frontend code)
- ✅ Use environment variables in production
- ✅ Rotate keys periodically
- ✅ Monitor usage dashboard for unusual activity

---

## 🔄 Rollback (If Needed)

If you want to go back to OpenAI:

### Step 1: Revert chatbot.php
```javascript
// Change from:
fetch('ai_openrouter_api.php', {

// Back to:
fetch('ai_chatbot_api.php', {
```

### Step 2: Restore ai_config.php
```php
// Update with OpenAI key:
define('OPENAI_API_KEY', 'sk-proj-your-key');
define('ENABLE_OPENAI', true);
```

### Step 3: Test
```
1. Refresh browser
2. Test chatbot
3. Should work with old OpenAI API
```

---

## 📊 Monitoring Your Usage

### Daily Check
```bash
# View logs
tail -f htdocs/logs/ai_openrouter.log

# Should show:
[2025-11-22 14:30:00] [info] Processing question with OpenRouter (Grok-4.1 with reasoning)...
[2025-11-22 14:30:05] [info] Token usage: Prompt: 245 | Completion: 87 | Total: 332
[2025-11-22 14:30:05] [info] OpenRouter response generated successfully
```

### Weekly Review
```
OpenRouter Dashboard:
1. Visit https://openrouter.ai/account
2. Check API usage statistics
3. Verify free tier usage (or upgrade if needed)
4. Review any errors or issues
```

### Cost Tracking
```php
// With free tier: No cost! ✅
// Daily token limit can be set:
define('AI_DAILY_TOKEN_LIMIT', 100000);  // ~$0 free tier

// If paying:
// ~1000 tokens = ~$0.01-0.02 (depending on model)
```

---

## 🚀 Advanced: Choosing Different Models

### Free Models
```php
// Current: Fast and free
define('OPENROUTER_MODEL', 'x-ai/grok-4.1-fast:free');

// Alternative: More advanced reasoning
define('OPENROUTER_MODEL', 'x-ai/grok-4.1-beta:free');
```

### Paid Models (Higher Quality)
```php
// OpenAI's latest
define('OPENROUTER_MODEL', 'openai/gpt-4o');

// Anthropic's best
define('OPENROUTER_MODEL', 'anthropic/claude-opus');

// Google's latest
define('OPENROUTER_MODEL', 'google/gemini-2.0-flash:free');
```

**Note:** Cost will increase with paid models, but still often cheaper than direct API.

---

## 📞 Getting Help

### Documentation Files
1. **OPENROUTER_SETUP.md** - Complete setup guide
2. **OPENROUTER_IMPLEMENTATION.md** - Full implementation details
3. **ai_openrouter_config.php** - Configuration with comments
4. **ai_openrouter_api.php** - Code with inline documentation

### External Resources
- **OpenRouter Docs:** https://openrouter.ai/docs
- **Grok Model Card:** https://openrouter.ai/models/x-ai/grok-4.1-fast:free
- **API Status:** https://status.openrouter.ai

### Debugging
```php
// Enable in ai_openrouter_config.php:
define('AI_DEBUG_MODE', true);        // Detailed errors
define('AI_LOG_ENABLED', true);        // Save all operations
define('AI_LOG_REASONING', true);      // Save thinking traces

// Check logs:
htdocs/logs/ai_openrouter.log
```

---

## ✅ You're All Set!

### Timeline
- **Now:** Get API key from OpenRouter (5 min)
- **5 min:** Update config (1 min)
- **6 min:** Test chatbot (1 min)
- **7 min:** Done! Enjoying Grok reasoning! 🎉

### What to Expect
- ✅ Same chatbot interface
- ✅ Faster responses for database queries
- ✅ Better reasoning for complex questions
- ✅ Zero cost (free tier)
- ✅ Automatic fallback if something fails

### What Changed Under the Hood
- 🔄 API calls now go to OpenRouter instead of OpenAI
- 🧠 Grok reasoning enabled for extended thinking
- 💾 Conversation history preserves reasoning details
- 📊 Logs track token usage and API calls
- 🛡️ Same security and privacy standards

---

## 📝 Quick Checklist

```
[ ] Get OpenRouter API key (sk-or-...)
[ ] Update ai_openrouter_config.php
[ ] Test simple question ("What equipment do we have?")
[ ] Test complex question ("Why is equipment rental important?")
[ ] Check logs for token usage
[ ] Confirm costs (should be $0 with free tier!)
[ ] Share with admin team
[ ] Enjoy better AI responses! 🎉
```

---

## Questions?

1. **How much does it cost?** - FREE (x-ai/grok-4.1-fast:free)
2. **What's reasoning?** - AI thinks through answers step-by-step
3. **Is it faster?** - Database queries: yes (instant). Reasoning queries: ~1-2s
4. **Will it break anything?** - No, fallback handles failures gracefully
5. **Can I go back to OpenAI?** - Yes, revert chatbot.php and ai_config.php
6. **How do I monitor usage?** - Check htdocs/logs/ai_openrouter.log or OpenRouter dashboard

---

**Ready? Let's upgrade your AI! 🚀**
