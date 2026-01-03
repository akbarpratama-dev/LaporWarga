# Vision AI - Implementation Guide for Developers

## 🏗️ Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     User Interface (Browser)                 │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  1. Photo Upload Input (required)                            │
│     ↓                                                         │
│  2. Vision AI Button (appears after upload)                  │
│     ↓                                                         │
│  3. Description Textarea (user editable)                     │
│     ↓                                                         │
│  4. Submit Button (manual trigger)                           │
│                                                               │
└─────────────────────────────────────────────────────────────┘
                            ↓ AJAX POST
┌─────────────────────────────────────────────────────────────┐
│              Backend API (api/vision_deskripsi.php)          │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  1. Validate file (type, size, MIME)                         │
│  2. Base64 encode image                                      │
│  3. Call OpenAI Vision API                                   │
│  4. Extract description                                      │
│  5. Sanitize output                                          │
│  6. Return JSON response                                     │
│                                                               │
└─────────────────────────────────────────────────────────────┘
                            ↓ HTTPS
┌─────────────────────────────────────────────────────────────┐
│                   OpenAI Vision API                          │
│                   (gpt-4o-mini model)                        │
└─────────────────────────────────────────────────────────────┘
```

## 📂 File Structure

```
LaporWarga1/
│
├── config/
│   ├── vision.example.php       # Template (committed to Git)
│   └── vision.php               # Actual config (gitignored)
│
├── api/
│   └── vision_deskripsi.php     # Backend endpoint
│
├── public/
│   ├── index.php                # Form with Vision AI integration
│   │
│   └── assets/
│       ├── css/
│       │   └── style.css        # Vision AI styles added
│       │
│       └── js/
│           └── vision.js        # Frontend logic
│
├── docs/
│   ├── VISION_AI.md             # Full documentation
│   ├── VISION_AI_QUICKSTART.md  # Quick setup guide
│   └── VISION_AI_SUMMARY.md     # This summary
│
└── tests/
    └── test_vision_ai.html      # Interactive test suite
```

## 🔌 API Endpoint Specification

### Endpoint

`POST /api/vision_deskripsi.php`

### Request

```http
POST /api/vision_deskripsi.php HTTP/1.1
Content-Type: multipart/form-data

foto: [binary image file]
```

### Success Response (200 OK)

```json
{
  "success": true,
  "description": "Terlihat jalan berlubang dengan kedalaman sekitar 20cm.",
  "message": "Deskripsi berhasil dibuat. Anda dapat mengedit sebelum mengirim laporan."
}
```

### Error Responses

**Missing Config**

```json
{
  "success": false,
  "error": "Konfigurasi Vision AI belum diatur. Silakan hubungi administrator."
}
```

**Invalid File Type**

```json
{
  "success": false,
  "error": "Tipe file tidak didukung. Gunakan JPG atau PNG."
}
```

**File Too Large**

```json
{
  "success": false,
  "error": "Ukuran file terlalu besar. Maksimal 5MB."
}
```

**API Error**

```json
{
  "success": false,
  "error": "Layanan AI tidak dapat memproses gambar. Silakan tulis deskripsi manual."
}
```

## 🎨 Frontend Implementation

### HTML Structure (in index.php)

```html
<!-- Photo Upload Field -->
<div class="form-group">
  <label for="foto">Upload Foto *</label>
  <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/jpg" required />
  <small class="form-text">Unggah foto kondisi di lapangan. Anda dapat menggunakan AI untuk membantu menyusun deskripsi laporan.</small>
  <small class="form-text">Format: JPG, PNG. Max 5MB</small>
</div>

<!-- Vision AI Button (hidden by default) -->
<div class="form-group" id="vision-ai-container" style="display: none;">
  <button type="button" id="btn-vision-ai" class="btn btn-secondary" disabled><i class="ri-magic-line"></i> Isi Deskripsi dari Foto (AI)</button>
  <small class="form-text"> <i class="ri-information-line"></i> Hasil dapat diedit sebelum dikirim </small>
</div>

<!-- Description Textarea -->
<div class="form-group">
  <label for="deskripsi">Deskripsi Masalah *</label>
  <textarea id="deskripsi" name="deskripsi" rows="5" required></textarea>
</div>
```

### JavaScript Logic (vision.js)

```javascript
// Key Functions:

1. initVisionAI()
   - Attach event listener to photo input
   - Show/hide button based on file selection
   - Validate file type and size

2. generateDescription()
   - Prevent multiple simultaneous requests
   - Confirm before replacing existing text
   - Call API via fetch()
   - Handle response and update UI

3. setButtonLoading(loading)
   - Toggle button disabled state
   - Show spinner during processing

4. showNotification(message, type)
   - Display success/error notifications
   - Auto-dismiss after 5 seconds
```

### CSS Styling (style.css)

```css
/* Key Styles: */

#vision-ai-container
  -
  Hidden
  by
  default
  (display: none)
  -
  Shown
  via
  JavaScript
  when
  photo
  selected
  #btn-vision-ai
  -
  Secondary
  button
  styling
  (red)
  -
  Disabled
  state
  with
  opacity
  -
  Loading
  state
  with
  spinner
  animation
  .vision-notification
  -
  Fixed
  position
  (top-right)
  -
  Slide-in
  animation
  -
  Success
  (green)
  /
  Error
  (red)
  variants;
```

## 🔐 Security Measures

### 1. Configuration Protection

```php
// config/vision.php is gitignored
// API key never exposed to frontend
$config = require __DIR__ . '/../config/vision.php';
```

### 2. File Validation

```php
// Type validation
$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
$mime_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']);

// Size validation
if ($file['size'] > 5242880) { // 5MB
    // Reject
}
```

### 3. Output Sanitization

```php
// HTML encoding to prevent XSS
$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
```

### 4. System Prompt Restriction

```php
// Limit AI behavior
'system_prompt' => 'Anda adalah asisten yang membantu warga...
ATURAN KETAT:
- JANGAN menebak lokasi spesifik
- JANGAN menyalahkan pihak tertentu
- JANGAN gunakan bahasa emosional'
```

## 💰 Cost Optimization

### Settings

```php
'openai_model' => 'gpt-4o-mini',  // Cheaper vision model
'temperature' => 0.2,              // Consistent, no retries needed
'max_tokens' => 100,               // Short descriptions only
```

### Trigger Control

- Manual button click (no auto-generation)
- One request per photo
- No retry loops
- User confirms before replacing text

### Estimated Cost

- gpt-4o-mini: ~$0.01 per request
- 100 reports/day: ~$1/day
- 3000 reports/month: ~$30/month

## 🧪 Testing Checklist

### Unit Tests (via test_vision_ai.html)

- [ ] Configuration exists
- [ ] File validation works (type)
- [ ] File validation works (size)
- [ ] API endpoint responds
- [ ] AI generates description
- [ ] Error handling works

### Integration Tests (via public/index.php)

- [ ] Button appears after photo upload
- [ ] Button disabled without photo
- [ ] Loading state displays correctly
- [ ] Description populates textarea
- [ ] User can edit AI result
- [ ] Form submits normally

### Security Tests

- [ ] API key not in frontend code
- [ ] config/vision.php in .gitignore
- [ ] Invalid files rejected
- [ ] Large files rejected
- [ ] Output is HTML-safe
- [ ] No auto-submission occurs

## 🐛 Troubleshooting

### Problem: Button doesn't appear

**Solution:**

- Check photo is selected
- Check vision.js is loaded
- Check browser console for errors

### Problem: API returns error

**Solution:**

- Verify config/vision.php exists
- Check API key is valid
- Check OpenAI account has credits
- Check PHP error log

### Problem: Description not generated

**Solution:**

- Check network tab in browser
- Verify file size < 5MB
- Check API response format
- Verify MIME type is correct

## 📊 Metrics to Monitor

### Usage Metrics

- Number of AI generations per day
- Success vs error rate
- Average response time
- Most common error types

### Cost Metrics

- Total API calls per month
- Total tokens used
- Cost per successful generation
- Cost per report submitted

### Quality Metrics

- User edit rate (how often AI text is modified)
- User satisfaction (optional feedback)
- Description length variance
- Error message frequency

## 🔄 Future Enhancements

### Possible Extensions

1. **Database Tracking**

   ```sql
   ALTER TABLE laporan ADD COLUMN ai_digunakan BOOLEAN DEFAULT FALSE;
   ALTER TABLE laporan ADD COLUMN deskripsi_ai TEXT;
   ```

2. **Multi-language Support**

   - Add language selection
   - Update system prompt per language

3. **A/B Testing**

   - Show Vision AI to 50% of users
   - Measure completion rates

4. **Image Preview**

   - Show thumbnail before generation
   - Allow crop/rotate before AI call

5. **Confidence Score**
   - Return AI confidence level
   - Show warning if low confidence

## 📝 Code Comments Guide

### Backend (vision_deskripsi.php)

- Config validation comments
- File validation logic
- API request structure
- Error handling cases

### Frontend (vision.js)

- Event listener setup
- State management
- API call handling
- UI update logic

### CSS (style.css)

- Section headers for organization
- Animation keyframes documented
- Responsive breakpoints noted
- Z-index usage explained

## ✅ Deployment Checklist

Before deploying to production:

- [ ] Copy vision.example.php to vision.php
- [ ] Add valid OpenAI API key
- [ ] Test with real images
- [ ] Verify .gitignore includes vision.php
- [ ] Check error logging is enabled
- [ ] Test on mobile devices
- [ ] Verify cost limits are acceptable
- [ ] Document API key location for team
- [ ] Set up monitoring for errors
- [ ] Create backup of config files

## 🎓 Academic Notes

### Why This Implementation is Academically Appropriate

1. **Transparency**: User sees all AI output
2. **Control**: User can edit or reject AI suggestions
3. **Safety**: No automated decisions
4. **Learning**: Demonstrates responsible AI integration
5. **Ethics**: AI assists, doesn't replace human judgment

### Assignment Context

Perfect for demonstrating:

- API integration skills
- Security best practices
- User-centered design
- Error handling
- Documentation skills
- Responsible AI usage

## 📞 Support

### Documentation Resources

- Full docs: `docs/VISION_AI.md`
- Quick start: `docs/VISION_AI_QUICKSTART.md`
- Test suite: `tests/test_vision_ai.html`

### External Resources

- [OpenAI Vision API Docs](https://platform.openai.com/docs/guides/vision)
- [PHP cURL Manual](https://www.php.net/manual/en/book.curl.php)
- [Fetch API MDN](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)

---

**Version**: 1.0.0  
**Last Updated**: December 25, 2025  
**Maintainer**: LaporWarga Development Team
