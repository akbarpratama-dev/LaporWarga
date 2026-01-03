# 🤖 AI Chatbot - Quick Start Guide

## What is This?

AI-powered assistant to help citizens use LaporWarga website. Think of it as an **interactive FAQ** that answers common questions.

---

## 🚀 5-Minute Setup

### Step 1: Get OpenAI API Key

1. Go to: https://platform.openai.com/api-keys
2. Create account (if needed)
3. Click "Create new secret key"
4. Copy the key (starts with `sk-proj-...`)

### Step 2: Configure

1. Copy config file:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/LaporWarga1
cp config/chatbot.example.php config/chatbot.php
```

2. Edit `config/chatbot.php`:

```php
'openai_api_key' => 'sk-proj-YOUR_KEY_HERE',  // ← Paste your key
```

3. Add to `.gitignore`:

```bash
echo "config/chatbot.php" >> .gitignore
```

### Step 3: Test

1. Open: `http://localhost/LaporWarga1/public/index.php`
2. Click red button (bottom-right)
3. Type: "Bagaimana cara melapor?"
4. Press Enter

✅ **Working** if you get a helpful response!

---

## 🎯 What It Does

### ✅ Answers These Questions

- "Bagaimana cara melapor?"
- "Bagaimana cara cek status?"
- "Apa arti status Diproses?"
- "Dimana menu Info Warga?"
- "Berapa lama laporan diproses?"

### ❌ Refuses These Questions

- "Siapa presiden Indonesia?" (out of scope)
- "Buatkan laporan untuk saya" (can't submit)
- "Hapus laporan saya" (can't modify data)

**Response**:

> "Maaf, saya hanya dapat membantu informasi seputar penggunaan website LaporWarga."

---

## 🎨 UI Overview

### Floating Button

- **Location**: Bottom-right corner
- **Color**: Red (secondary color)
- **Tooltip**: "Butuh bantuan?"

### Chat Panel

- **Opens**: Above floating button
- **Size**: 380×500px (mobile responsive)
- **Header**: "Asisten LaporWarga"

### Features

- Quick suggestion buttons
- Typing indicator
- Auto-scroll
- Click outside to close

---

## ⚙️ Configuration

### Change Message Limit

File: `config/chatbot.php`

```php
'rate_limit_messages' => 10,   // Max messages
'rate_limit_window' => 300,    // Per 5 minutes
```

### Change Response Length

```php
'max_tokens' => 200,  // Shorter responses
// or
'max_tokens' => 500,  // Longer responses
```

### Change Temperature (Creativity)

```php
'temperature' => 0.2,  // Focused (recommended)
// or
'temperature' => 0.7,  // More creative
```

---

## 💰 Cost

### GPT-3.5-turbo Pricing

- ~$0.0003 per message
- 100 messages = $0.03
- **Monthly estimate**: $1-5

### Set Budget Alert

1. Go to: https://platform.openai.com/account/billing/limits
2. Set monthly limit: $10
3. Get email alerts at 75% usage

---

## 🔐 Security Checklist

- [ ] API key in `config/chatbot.php` (NOT in JS)
- [ ] `config/chatbot.php` in `.gitignore`
- [ ] Never commit API key to Git
- [ ] Rate limiting enabled
- [ ] Max message length set

---

## 🧪 Test Checklist

- [ ] Floating button appears
- [ ] Panel opens on click
- [ ] Can send message
- [ ] Bot responds
- [ ] Out-of-scope refused
- [ ] Rate limit works (send 11 messages)
- [ ] Mobile responsive

---

## 🐛 Common Issues

### Chatbot button tidak muncul

**Check**:

```bash
# Verify files exist
ls public/assets/js/chatbot.js
ls api/chatbot.php
```

### "Invalid API key"

**Fix**:

1. Check key in `config/chatbot.php`
2. Verify starts with `sk-proj-`
3. No extra spaces

### "Koneksi gagal"

**Check**:

1. Internet connection
2. XAMPP running
3. PHP cURL enabled:

```bash
/Applications/XAMPP/xamppfiles/bin/php -m | grep curl
```

### No response after 30 seconds

**Possible**:

- OpenAI API slow
- Network timeout
- Check PHP error log

---

## 📊 How It Works

```
┌─────────────────────────────────────┐
│  User: "Cara melapor?"              │
└────────────────┬────────────────────┘
                 ↓
┌─────────────────────────────────────┐
│  JS: Send to /api/chatbot.php       │
└────────────────┬────────────────────┘
                 ↓
┌─────────────────────────────────────┐
│  PHP: Validate & rate limit         │
└────────────────┬────────────────────┘
                 ↓
┌─────────────────────────────────────┐
│  Send to OpenAI API                 │
│  + System prompt                    │
│  + Conversation history             │
└────────────────┬────────────────────┘
                 ↓
┌─────────────────────────────────────┐
│  OpenAI: Generate response          │
└────────────────┬────────────────────┘
                 ↓
┌─────────────────────────────────────┐
│  Return to user                     │
│  "Untuk melapor, klik menu..."      │
└─────────────────────────────────────┘
```

---

## 🎨 Customization

### Add Suggestion Button

Edit `public/index.php`:

```html
<button class="suggestion-btn" data-msg="Your question?">Button text</button>
```

### Change Welcome Message

Edit `public/index.php`:

```html
<p>Halo! 👋 Custom welcome message here</p>
```

### Change Button Color

Edit `public/assets/css/style.css`:

```css
.chatbot-btn {
  background: #1976d2; /* Blue instead of red */
}
```

---

## 📚 Files Overview

| File                          | Purpose              |
| ----------------------------- | -------------------- |
| `api/chatbot.php`             | Backend API endpoint |
| `config/chatbot.php`          | API key & settings   |
| `public/assets/js/chatbot.js` | Frontend logic       |
| `public/assets/css/style.css` | Chatbot styles       |
| `public/index.php`            | Chatbot UI           |

---

## 🚀 Next Steps

1. **Test thoroughly** - Try various questions
2. **Monitor costs** - Check OpenAI dashboard
3. **Customize prompts** - Improve responses
4. **Add to other pages** - cek_status.php, etc.
5. **Collect feedback** - Ask users if helpful

---

## 📞 Support

- [Full Documentation](CHATBOT.md)
- [OpenAI Docs](https://platform.openai.com/docs)
- [Troubleshooting](CHATBOT.md#-troubleshooting)

**Ready to chat! 🎉**
