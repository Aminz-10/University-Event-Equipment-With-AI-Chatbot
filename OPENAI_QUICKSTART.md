# 🚀 OpenAI Integration - Quick Start Guide

## In 3 Simple Steps:

### Step 1: Get OpenAI API Key
1. Visit: https://platform.openai.com/account/api-keys
2. Click: "Create new secret key"
3. Copy the key (save it somewhere safe!)

### Step 2: Add to Configuration
Open `ai_config.php` and replace:
```php
define('OPENAI_API_KEY', 'sk-your-api-key-here');
```

With your actual key:
```php
define('OPENAI_API_KEY', 'sk-proj-aBcDeFgHiJkLmNoPqRsTuVwXyZ');
```

### Step 3: Test It!
1. Log into UniEquip
2. Go to AI Assistant
3. Ask anything: "Tell me about UniEquip"

**That's it!** 🎉

---

## How It Works

**Before:** Local pattern matching
- Limited question types
- Falls back for random questions
- ~70% coverage

**Now with OpenAI:** 
- Understands any question
- Natural conversations
- Real-time context awareness
- ~95%+ coverage

---

## Cost Overview

| Feature | Cost |
|---------|------|
| 1,000 messages (GPT-3.5-turbo) | ~$0.10-0.20 |
| 1,000 messages (GPT-4) | ~$3.00-5.00 |
| Free trial credit | $5.00 |
| Monthly free tier | Some allowance |

**Example:** Small university using ~100 messages/day costs ~$3/month

---

## What Changes?

### Random Questions
**User:** "How do universities benefit from equipment systems?"
**OpenAI Response:** Detailed, contextual answer specific to UniEquip

### System Questions  
**User:** "Explain the booking workflow"
**OpenAI Response:** Clear explanation of the complete process

### Off-Topic Questions
**User:** "Tell me a joke about equipment"
**OpenAI Response:** Funny, relevant joke about the system!

---

## Troubleshooting

**Problem:** "API key not valid"
- Check key is correct and not expired
- Go to: https://platform.openai.com/account/api-keys

**Problem:** "Timeout error"
- Check internet connection
- Check OpenAI status: https://status.openai.com

**Problem:** "Rate limit exceeded"
- Wait a moment and retry
- Upgrade your OpenAI plan for higher limits

---

## Disable OpenAI (Use Local AI Only)

If you want to go back to local AI:

Open `ai_config.php`:
```php
define('ENABLE_OPENAI', false);
```

System will automatically use pattern-matching fallback!

---

## Advanced: Better Responses

For higher quality (slower):
```php
define('OPENAI_MODEL', 'gpt-4');
```

For faster responses:
```php
define('OPENAI_MODEL', 'gpt-3.5-turbo');
```

---

## Need Help?

- OpenAI Docs: https://platform.openai.com/docs
- Check logs in `htdocs/logs/ai_chatbot.log`
- Read full guide: `OPENAI_SETUP.md`

**Enjoy your enhanced AI assistant!** 🤖
