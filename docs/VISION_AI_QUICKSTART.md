# Vision AI - Quick Start Guide

## 🚀 Setup in 3 Steps

### Step 1: Copy Configuration

```bash
cp config/vision.example.php config/vision.php
```

### Step 2: Add API Key

Edit `config/vision.php`:

```php
'openai_api_key' => 'sk-proj-YOUR_ACTUAL_API_KEY_HERE',
```

### Step 3: Test

1. Go to http://localhost/LaporWarga1/public/
2. Scroll to "Laporkan Masalah" form
3. Upload a photo (JPG/PNG, max 5MB)
4. Click "Isi Deskripsi dari Foto (AI)" button
5. AI will generate description
6. Edit if needed and submit report

## ✅ That's it!

For full documentation, see [VISION_AI.md](VISION_AI.md)

## 🔧 Troubleshooting

**Button not appearing?**

- Make sure you upload a photo first
- Check browser console for errors

**API Error?**

- Verify API key is correct
- Check OpenAI account has credits
- See error in browser network tab

**File upload error?**

- Use JPG or PNG only
- File must be under 5MB
- Try a different image

## 💡 Tips

- Vision AI is **optional** - you can skip it
- You can **edit** AI results before submitting
- The AI generates **1-2 sentences only**
- Results are **neutral and factual**

## 📖 Features

✅ Optional AI helper  
✅ User can edit results  
✅ Manual trigger (click button)  
✅ No auto-submit  
✅ Safe and controlled

## 🔒 Security

- API key is gitignored
- Only triggered manually
- Output is sanitized
- File validation enforced
- No auto-submission

---

**Need Help?** Read the full [Vision AI Documentation](VISION_AI.md)
