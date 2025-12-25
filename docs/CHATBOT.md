# AI Chatbot Documentation - LaporWarga

## Overview

AI-powered FAQ chatbot untuk membantu warga menggunakan sistem LaporWarga. Chatbot ini berfungsi sebagai **asisten interaktif**, bukan fitur inti sistem.

---

## 🎯 Purpose

**Chatbot = Interactive User Guide**

Chatbot membantu menjawab pertanyaan seputar:

- ✅ Cara melapor
- ✅ Cara cek status laporan
- ✅ Penjelasan status (Diterima, Diproses, Selesai)
- ✅ Navigasi menu website
- ✅ Kontak pemerintah

**Chatbot TIDAK dapat**:

- ❌ Submit laporan
- ❌ Ubah data
- ❌ Buat keputusan
- ❌ Gantikan admin
- ❌ Jawab pertanyaan di luar scope LaporWarga

---

## 📁 File Structure

```
LaporWarga1/
├── api/
│   └── chatbot.php                    ⭐ Backend API endpoint
├── config/
│   ├── chatbot.example.php           📄 Config template
│   └── chatbot.php                   🔒 Actual config (add your API key)
└── public/
    ├── index.php                     ✏️ Modified (chatbot UI added)
    └── assets/
        ├── css/
        │   └── style.css             ✏️ Modified (chatbot styles added)
        └── js/
            └── chatbot.js            ⭐ Frontend JavaScript
```

---

## 🚀 Installation

### Step 1: Configure OpenAI API Key

1. Copy example config:

```bash
cp config/chatbot.example.php config/chatbot.php
```

2. Edit `config/chatbot.php`:

```php
'openai_api_key' => 'sk-proj-YOUR_ACTUAL_API_KEY_HERE',
```

3. Add to `.gitignore`:

```
config/chatbot.php
```

### Step 2: Verify Files

Ensure these files exist:

- ✅ `api/chatbot.php`
- ✅ `config/chatbot.php`
- ✅ `public/assets/js/chatbot.js`
- ✅ Chatbot UI in `public/index.php`
- ✅ Chatbot CSS in `public/assets/css/style.css`

### Step 3: Test

1. Open `http://localhost/LaporWarga1/public/index.php`
2. Click floating button (bottom-right corner)
3. Try sending a message: "Bagaimana cara melapor?"

---

## 🔧 Configuration

File: `config/chatbot.php`

### OpenAI Settings

```php
'openai_api_key' => 'sk-proj-...',     // Your OpenAI API key
'openai_model' => 'gpt-3.5-turbo',    // Model to use
'temperature' => 0.2,                  // Low = more focused answers
'max_tokens' => 200,                   // Short responses
```

**Why low temperature?**

- Consistent answers
- Less creativity (we want factual info)
- Predictable behavior

### Rate Limiting

```php
'rate_limit_messages' => 10,   // Max 10 messages
'rate_limit_window' => 300,    // Per 5 minutes
```

**Protection against**:

- Spam
- API cost abuse
- Bot attacks

### Security

```php
'max_message_length' => 500,   // Max characters per message
```

### System Prompt

The system prompt defines chatbot behavior:

```php
'system_prompt' => 'Anda adalah asisten virtual untuk website LaporWarga...
TOPIK YANG BOLEH DIJAWAB:
1. Cara melapor
2. Cara cek status
...
JANGAN jawab pertanyaan di luar topik.'
```

**Modify this** to change chatbot behavior.

---

## 💻 Backend API

File: `api/chatbot.php`

### Flow

```
User Message (JS)
    ↓
POST /api/chatbot.php
    ↓
Validate Input
    ↓
Check Rate Limit
    ↓
Add to Conversation History
    ↓
Send to OpenAI API
    ↓
Return Response (JSON)
```

### Request Format

```javascript
POST /api/chatbot.php
Content-Type: application/json

{
  "message": "Bagaimana cara melapor?"
}
```

### Response Format

**Success**:

```json
{
  "success": true,
  "message": "Untuk melapor, klik menu Lapor...",
  "timestamp": "14:30"
}
```

**Error**:

```json
{
  "error": "Pesan terlalu panjang"
}
```

### Security Features

✅ **Input Validation**

```php
if (empty($userMessage)) {
    http_response_code(400);
    echo json_encode(['error' => 'Pesan tidak boleh kosong']);
}
```

✅ **Length Limit**

```php
if (strlen($userMessage) > $config['max_message_length']) {
    // Reject
}
```

✅ **Rate Limiting** (Session-based)

```php
if (count($_SESSION['chatbot_messages']) >= 10) {
    http_response_code(429);
    // Too many requests
}
```

✅ **Input Sanitization**

```php
$userMessage = htmlspecialchars($userMessage, ENT_QUOTES, 'UTF-8');
```

---

## 🎨 Frontend (JavaScript)

File: `public/assets/js/chatbot.js`

### Features

1. **Toggle Chat Panel**

   - Click floating button → Open
   - Click close button → Close
   - Click outside → Close

2. **Send Message**

   - Type and press Enter
   - Click send button
   - Click suggestion button

3. **Display Messages**

   - User messages (right, dark blue)
   - Bot messages (left, white)
   - Typing indicator

4. **Error Handling**
   - Network errors
   - API errors
   - Rate limit errors

### Functions

```javascript
// Toggle chat panel
function toggleChat() { ... }

// Add message to UI
function addMessage(text, isUser) { ... }

// Send to API
async function sendMessage(message) { ... }

// Show typing indicator
function showTyping(show) { ... }
```

---

## 🎨 UI/UX

### Floating Button

- **Position**: Bottom-right corner
- **Color**: Secondary (red)
- **Icon**: Question mark
- **Tooltip**: "Butuh bantuan?"

### Chat Panel

- **Size**: 380px × 500px
- **Position**: Above floating button
- **Animation**: Slide up from bottom
- **Colors**: Red gradient header

### Messages

**Bot Message**:

- White background
- Robot icon
- Left-aligned

**User Message**:

- Dark blue background
- User icon
- Right-aligned

### Suggestion Buttons

Quick-reply buttons for common questions:

- "Cara melapor"
- "Cek status"
- "Arti status"

---

## 🔐 Security Best Practices

### 1. API Key Protection

✅ **DO**:

- Store in `config/chatbot.php` (outside public folder)
- Add to `.gitignore`
- Never commit to Git

❌ **DON'T**:

- Hardcode in JavaScript
- Expose in frontend
- Share publicly

### 2. Rate Limiting

**Current**: 10 messages per 5 minutes per session

**Upgrade**: IP-based limiting

```php
$userIP = $_SERVER['REMOTE_ADDR'];
// Track by IP instead of session
```

### 3. Input Validation

- Max 500 characters
- Sanitize HTML
- Validate JSON

### 4. Error Messages

**Good**:

```
"Terjadi kesalahan. Silakan coba lagi."
```

**Bad** (don't expose internals):

```
"OpenAI API returned 401 Unauthorized"
```

---

## 💰 Cost Management

### OpenAI Pricing (GPT-3.5-turbo)

- **Input**: $0.0015 per 1K tokens
- **Output**: $0.002 per 1K tokens

### Estimate

Assuming:

- Average message: 100 tokens input + 100 tokens output
- Cost per message: ~$0.0003

**100 messages/day**:

- Daily: $0.03
- Monthly: ~$1

### Cost Control

1. **Set token limit**:

```php
'max_tokens' => 200,  // Shorter responses
```

2. **Rate limiting**:

```php
'rate_limit_messages' => 10,  // Limit per user
```

3. **Monitor usage**:
   - Check OpenAI dashboard
   - Set billing alerts

---

## 🧪 Testing

### Manual Test

1. **Open chat**:

   - Click floating button
   - Panel should appear

2. **Send valid question**:

   ```
   "Bagaimana cara melapor?"
   ```

   - Should get helpful response

3. **Send out-of-scope question**:

   ```
   "Siapa presiden Indonesia?"
   ```

   - Should refuse politely

4. **Test rate limit**:

   - Send 11 messages quickly
   - 11th should be blocked

5. **Test long message**:
   - Send 600 characters
   - Should be rejected

### JavaScript Console

Check for errors:

```javascript
// Should see:
"Chatbot initialized successfully";
```

### API Test (cURL)

```bash
curl -X POST http://localhost/LaporWarga1/api/chatbot.php \
  -H "Content-Type: application/json" \
  -d '{"message":"Bagaimana cara melapor?"}'
```

---

## 📊 Monitoring

### Check API Usage

**OpenAI Dashboard**:

- Go to: https://platform.openai.com/usage
- Monitor daily requests
- Check costs

### Check Errors

**PHP Error Log**:

```bash
tail -f /Applications/XAMPP/xamppfiles/logs/error_log
```

Look for:

```
Chatbot cURL error: ...
OpenAI API error: ...
```

---

## 🔄 Customization

### Change Chatbot Personality

Edit system prompt in `config/chatbot.php`:

```php
'system_prompt' => 'Anda adalah asisten yang ramah dan membantu...'
```

### Add More Suggestions

Edit `public/index.php`:

```html
<button class="suggestion-btn" data-msg="Pertanyaan baru?">Pertanyaan baru</button>
```

### Change Colors

Edit `public/assets/css/style.css`:

```css
.chatbot-btn {
  background: #1976d2; /* Change to blue */
}
```

### Change Model

Use GPT-4 (more expensive but better):

```php
'openai_model' => 'gpt-4',
'max_tokens' => 300,
```

---

## 🐛 Troubleshooting

### Chatbot tidak muncul

**Check**:

1. File `chatbot.js` loaded?
   - View page source → Check `<script src="assets/js/chatbot.js">`
2. Console errors?
   - Press F12 → Check Console tab

### API Error 401

**Problem**: Invalid API key

**Fix**:

```php
// config/chatbot.php
'openai_api_key' => 'sk-proj-CORRECT_KEY_HERE',
```

### API Error 429

**Problem**: Rate limit exceeded (OpenAI)

**Fix**:

- Check OpenAI dashboard
- Upgrade plan or wait

### "Koneksi gagal"

**Problem**: cURL error

**Check**:

1. Internet connection
2. PHP cURL extension enabled
3. Firewall blocking OpenAI API

---

## 📚 Related Documentation

- [OpenAI API Docs](https://platform.openai.com/docs/api-reference)
- [GPT-3.5-turbo Guide](https://platform.openai.com/docs/guides/chat)
- [Cookie Tracking](COOKIE_TRACKING.md)
- [Rate Limiting](RATE_LIMITING.md)

---

## 📄 License & Credits

**Developer**: Full-stack PHP Team  
**Date**: December 2025  
**AI Model**: OpenAI GPT-3.5-turbo  
**Tech Stack**: PHP Native, Vanilla JS, OpenAI API
