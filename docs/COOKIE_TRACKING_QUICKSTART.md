# 🍪 Cookie Tracking - Quick Start Guide

## What is Cookie Tracking?

Cookie tracking allows citizens to **automatically see all their submitted reports** without needing to remember report codes or login. The system saves report IDs in browser cookies and displays them automatically on the status check page.

---

## 🚀 Quick Test (3 Minutes)

### Step 1: Submit a Report

1. Open `http://localhost/LaporWarga1/public/index.php`
2. Scroll to "Lapor" section
3. Fill the form and submit
4. Note the success message with report code

### Step 2: Check Your Reports

1. Open `http://localhost/LaporWarga1/public/cek_status.php`
2. **You should automatically see your report!**
3. No need to enter code or phone number

### Step 3: Submit Another Report

1. Go back and submit another report
2. Return to cek_status.php
3. **Both reports now appear automatically**

---

## 🎯 How It Works (Simple)

```
┌─────────────────────────────────────┐
│  User submits report                │
│  → Database saves report (ID = 42)  │
│  → Cookie saves: [42]               │
└─────────────────────────────────────┘
                 ↓
┌─────────────────────────────────────┐
│  User visits status page            │
│  → Read cookie: [42]                │
│  → Query: SELECT * WHERE id = 42    │
│  → Display report automatically     │
└─────────────────────────────────────┘
```

---

## 📋 Features

✅ **Zero Configuration** - Works immediately after implementation  
✅ **No Login Required** - Citizens don't need accounts  
✅ **Privacy Safe** - Only report IDs stored (no names/phones)  
✅ **Browser Specific** - Each device tracks its own reports  
✅ **30-day Memory** - Cookie lasts for 30 days  
✅ **Manual Fallback** - Can still search by code if needed

---

## 🔧 Testing Tools

### Test Page

Open: `http://localhost/LaporWarga1/tests/test_cookie_tracking.php`

**Features**:

- View current tracked reports
- Manually add/remove report IDs
- Test security validation
- Clear cookie

### Browser DevTools

1. Press `F12`
2. Go to **Application** tab
3. Look for **Cookies** → `laporwarga_reports`
4. See value: `[12,25,31]` (example)

---

## 🎨 User Experience

### Before Cookie Tracking

```
User: "What was my report code again?" 🤔
System: "Enter code manually"
User: *searches email/notes*
```

### After Cookie Tracking

```
User: Opens status page
System: "Here are your 3 reports!" ✅
User: "Perfect!" 😊
```

---

## 🔐 Security

### What's Stored

```json
Cookie: [12, 25, 31]  ← Only report IDs
```

### What's NOT Stored

❌ Names  
❌ Phone numbers  
❌ Addresses  
❌ Report content

### Protection

✅ HttpOnly flag (prevents JavaScript access)  
✅ Input validation (only integers)  
✅ SQL injection protection (prepared statements)  
✅ Tamper-proof (invalid data ignored)

---

## 📱 UI Behavior

### If Cookie Exists

```
┌─────────────────────────────────────┐
│ 📋 Laporan Anda                     │
│ Ditemukan 3 laporan dari browser    │
│                                     │
│ [Table showing all reports]         │
│                                     │
│ Want to check other reports?        │
│ [Cek Laporan Secara Manual] ← Button│
└─────────────────────────────────────┘
```

### If No Cookie

```
┌─────────────────────────────────────┐
│ 🔍 Cari Laporan Manual              │
│                                     │
│ Kode Laporan: [___________]         │
│                [Cek Button]         │
└─────────────────────────────────────┘
```

---

## 🧪 Quick Validation Checklist

- [ ] Submit report → Cookie created
- [ ] Status page → Report shown automatically
- [ ] Submit 2nd report → Cookie updated
- [ ] Status page → Both reports shown
- [ ] Click "Manual" button → Form appears
- [ ] Close browser → Reopen → Reports still there
- [ ] Wait 30 days → Cookie expires (optional test)

---

## 🛠️ Configuration

### Change Cookie Duration

File: `config/report_tracker.php`

```php
const COOKIE_EXPIRY_DAYS = 30; // Change to 7, 60, 90, etc.
```

### Enable HTTPS-only (Production)

File: `config/report_tracker.php`

```php
return setcookie(
    self::COOKIE_NAME,
    $jsonData,
    $expiry,
    '/',
    '',
    true,  // ← Change false to true
    true
);
```

---

## 📞 Common Questions

**Q: What if user clears browser data?**  
A: Cookie is deleted, but they can still use manual search.

**Q: Can users see reports from another browser?**  
A: No. Cookie is browser-specific. Use manual search for cross-device.

**Q: What's the maximum reports tracked?**  
A: Technically unlimited, but recommended ~50 for performance.

**Q: Is this GDPR compliant?**  
A: Yes. Report IDs are not personal data. No tracking across sites.

**Q: What if cookie is tampered with?**  
A: Validation removes invalid IDs. Database query won't return fake reports.

---

## 🎯 Production Checklist

Before going live:

- [ ] Test with real users
- [ ] Enable HTTPS-only cookies
- [ ] Monitor cookie size
- [ ] Add analytics (optional)
- [ ] Document in user guide
- [ ] Train support staff

---

## 🚀 Next Steps

1. **Test thoroughly** - Use test page and manual submission
2. **Monitor usage** - See how many users benefit
3. **Gather feedback** - Ask users if it's helpful
4. **Iterate** - Improve based on real-world usage

---

## 📄 Full Documentation

For technical details, see:

- [COOKIE_TRACKING.md](COOKIE_TRACKING.md) - Complete documentation
- [Rate Limiting](RATE_LIMITING.md) - Spam prevention
- [Main README](../README.md) - Full system overview

---

**Happy tracking! 🎉**
