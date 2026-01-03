# Cookie-based Report Tracking System

## Overview

Sistem pelacakan laporan berbasis cookie untuk LaporWarga yang memungkinkan warga melihat semua laporan yang pernah dikirim dari browser yang sama tanpa perlu login.

---

## 🎯 Goals Achieved

✅ **No Login Required** - Citizens can track reports without authentication  
✅ **Privacy-focused** - Only report IDs stored in cookies (no sensitive data)  
✅ **User-friendly** - Automatic display of user's reports  
✅ **Secure** - Validation and sanitization against tampering  
✅ **Browser-specific** - Each browser tracks its own submissions

---

## 📁 Files Modified/Created

### 1. `config/report_tracker.php` ⭐ NEW

**Purpose**: Cookie management utility class

**Key Functions**:

- `addReport($reportId)` - Add report ID to tracking cookie
- `getTrackedReports()` - Get array of tracked report IDs from cookie
- `fetchTrackedReportsFromDB($conn)` - Query database for tracked reports
- `hasTrackedReports()` - Check if user has any tracked reports
- `clearTracking()` - Clear tracking cookie (for testing/privacy)

**Cookie Details**:

- **Name**: `laporwarga_reports`
- **Format**: JSON array of integers `[12, 25, 31]`
- **Expiry**: 30 days
- **Flags**: HttpOnly (prevents JavaScript access)

---

### 2. `process/submit_laporan.php` ✏️ MODIFIED

**Changes**:

```php
// Added at top
require_once '../config/report_tracker.php';

// After successful INSERT
$reportId = (int) $conn->lastInsertId();

if ($reportId > 0) {
    ReportTracker::addReport($reportId);
}
```

**Flow**:

1. Insert report into database
2. Get last inserted ID
3. Save ID to cookie
4. Redirect to success page

---

### 3. `public/cek_status.php` ✏️ MODIFIED

**Major Changes**:

**Backend Logic**:

```php
require_once '../config/report_tracker.php';

// Check if user has tracked reports
$hasTrackedReports = ReportTracker::hasTrackedReports();

if ($hasTrackedReports) {
    // Fetch reports from database using cookie IDs
    $trackedReports = ReportTracker::fetchTrackedReportsFromDB($conn);
}
```

**UI Behavior**:

- **IF tracked reports exist**:
  - Show table with all tracked reports
  - Display: Kode Laporan, Nama, Kategori, Lokasi, Status, Tanggal, Aksi
  - Provide button to toggle manual search form
- **IF no tracked reports**:
  - Show manual search form by default
  - User can enter report code to search manually

---

### 4. `tests/test_cookie_tracking.php` ⭐ NEW

**Purpose**: Comprehensive test suite for cookie tracking

**Test Sections**:

1. **Current Cookie Status** - Shows tracked report IDs
2. **Database Fetch** - Displays report details from DB
3. **Manual Actions** - Add/remove reports, clear cookie
4. **Security Validation** - Tests against malicious input

**Usage**:

```
http://localhost/LaporWarga1/tests/test_cookie_tracking.php
```

---

## 🔄 User Flow

### Scenario 1: First-time Submission

```
1. User fills report form
   ↓
2. Submit → submit_laporan.php
   ↓
3. Insert into database (ID = 42)
   ↓
4. Cookie saved: [42]
   ↓
5. Redirect to success page
```

### Scenario 2: Second Submission

```
1. User submits another report
   ↓
2. Insert into database (ID = 55)
   ↓
3. Read existing cookie: [42]
   ↓
4. Add new ID: [42, 55]
   ↓
5. Update cookie
```

### Scenario 3: Check Status Page

```
User visits cek_status.php
   ↓
Check cookie: [42, 55]
   ↓
Query: SELECT * FROM laporan WHERE id IN (42, 55)
   ↓
Display table with 2 reports
```

---

## 🔐 Security Features

### 1. Input Validation

```php
// Only integers allowed
$reports = array_map('intval', $reports);

// Remove invalid IDs (0 or negative)
$reports = array_filter($reports, function($id) {
    return $id > 0;
});
```

### 2. SQL Injection Protection

```php
// Prepared statements with bound parameters
$stmt = $conn->prepare("SELECT * FROM laporan WHERE id IN ({$placeholders})");

foreach ($reportIds as $index => $id) {
    $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
}
```

### 3. Array Sanitization

```php
// Ensure cookie value is valid array
if (!is_array($reports)) {
    return [];
}

// Remove duplicates
$reports = array_unique($reports);
```

### 4. HttpOnly Cookie

```php
setcookie(
    'laporwarga_reports',
    $jsonData,
    $expiry,
    '/',
    '',
    false,  // Set to true in HTTPS production
    true    // HttpOnly - prevents XSS
);
```

---

## 🎨 UI/UX Improvements

### Automatic Report Display

- No manual input needed if cookie exists
- Shows comprehensive table with status badges
- Color-coded status: Diterima (blue), Diproses (yellow), Selesai (green)

### Manual Search Option

- Hidden by default if tracked reports exist
- One-click toggle button
- Maintains original manual search functionality

### Responsive Design

- Table is scrollable on mobile
- Proper badge styling
- Action buttons remain accessible

---

## 📊 Database Query

### Fetch Tracked Reports

```sql
SELECT id, kode_laporan, nama_pelapor, kategori, lokasi, status,
       tanggal_lapor, tanggal_selesai
FROM laporan
WHERE id IN (?, ?, ?)  -- Prepared statement placeholders
ORDER BY tanggal_lapor DESC
```

**Performance**:

- Uses primary key (id) for lookup - very fast
- ORDER BY sorts newest first
- Limit determined by cookie size (reasonable: ~50 reports max)

---

## 🧪 Testing Checklist

### Manual Test Steps:

1. **Clear existing cookie**:

   - Use test page or browser DevTools
   - Verify cookie is removed

2. **Submit first report**:

   - Fill form at `public/index.php#lapor`
   - Check success message
   - Verify cookie created (DevTools → Application → Cookies)

3. **Check status page**:

   - Visit `public/cek_status.php`
   - Should see 1 report automatically

4. **Submit second report**:

   - Submit another report
   - Cookie should update

5. **Check status again**:

   - Should see 2 reports

6. **Manual search**:

   - Click "Cek Laporan Secara Manual"
   - Form should appear
   - Search by code should work

7. **Security test**:
   - Visit `tests/test_cookie_tracking.php`
   - Review security validation results

---

## 🛠️ Configuration

### Cookie Expiry

Change in `config/report_tracker.php`:

```php
const COOKIE_EXPIRY_DAYS = 30; // Change to desired days
```

### HTTPS Production

```php
setcookie(
    self::COOKIE_NAME,
    $jsonData,
    $expiry,
    '/',
    '',
    true,  // ← Set to TRUE for HTTPS-only
    true
);
```

---

## 📝 Code Examples

### Add Report to Cookie

```php
require_once '../config/report_tracker.php';

// After INSERT
$reportId = $conn->lastInsertId();
ReportTracker::addReport($reportId);
```

### Check if User Has Tracked Reports

```php
if (ReportTracker::hasTrackedReports()) {
    echo "You have previous reports!";
}
```

### Fetch Tracked Reports

```php
$reports = ReportTracker::fetchTrackedReportsFromDB($conn);

foreach ($reports as $report) {
    echo $report['kode_laporan'] . ": " . $report['status'];
}
```

### Clear Tracking

```php
ReportTracker::clearTracking();
```

---

## 🔍 Troubleshooting

### Cookie Not Saving

**Problem**: Cookie not created after submission

**Solutions**:

- Check `setcookie()` is called before any output
- Verify no whitespace before `<?php`
- Check browser privacy settings
- Ensure headers not already sent

### Reports Not Showing

**Problem**: cek_status.php shows empty

**Check**:

1. Cookie exists: `var_dump($_COOKIE['laporwarga_reports']);`
2. Valid JSON: Check format `[1,2,3]`
3. IDs exist in database: Query manually
4. Database connection: Check for errors

### Invalid Cookie Data

**Problem**: Cookie contains malformed data

**Fix**:

- Clear cookie: `ReportTracker::clearTracking();`
- Or manually delete in browser DevTools
- Resubmit report to recreate

---

## 🚀 Future Enhancements

### Potential Improvements:

1. **Phone Number Verification**:

   - Match cookie reports with phone number
   - Extra security layer

2. **Export Reports**:

   - Download tracked reports as PDF
   - Email report summary

3. **Push Notifications**:

   - Alert when tracked report status changes
   - Browser notification API

4. **Analytics**:
   - Track how many users use cookie feature
   - Average reports per user

---

## 📄 License & Credits

**Developer**: Backend PHP Team  
**Date**: December 2025  
**Version**: 1.0.0  
**Tech Stack**: PHP Native, PDO, MySQL

---

## 📚 Related Documentation

- [Rate Limiting Documentation](RATE_LIMITING.md)
- [Database Schema](../database/laporwarga.sql)
- [Main README](../README.md)
