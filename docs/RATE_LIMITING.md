# Rate Limiting Feature Documentation

## Overview

Fitur rate limiting mencegah spam dengan membatasi frekuensi pengiriman laporan berdasarkan nomor HP.

## Configuration

**Default Interval**: 2 hours (configurable)

Location: `process/submit_laporan.php`

```php
$rate_limit_hours = 2; // Change this value to adjust interval
```

## How It Works

### 1. Check Last Submission

```sql
SELECT tanggal_lapor
FROM laporan
WHERE no_hp = :no_hp
ORDER BY tanggal_lapor DESC
LIMIT 1
```

### 2. Calculate Time Difference

```php
$lastSubmitTime = new DateTime($lastReport['tanggal_lapor']);
$currentTime = new DateTime();
$timeDiff = $currentTime->getTimestamp() - $lastSubmitTime->getTimestamp();
```

### 3. Compare with Limit

```php
$rate_limit_seconds = $rate_limit_hours * 60 * 60;

if ($timeDiff < $rate_limit_seconds) {
    // REJECT: Too soon
    header("Location: ../public/index.php?error=rate_limit&msg=...");
    exit();
}
```

### 4. Allow or Reject

- **REJECT**: If time difference < interval → Show error message
- **ALLOW**: If no previous report OR time difference ≥ interval → Continue submission

## Error Message

### Default Message

```
"Anda sudah mengirim laporan sebelumnya. Silakan menunggu 2 jam sebelum mengirim laporan baru."
```

### Dynamic Message (with countdown)

```
"Anda sudah mengirim laporan sebelumnya. Silakan menunggu 1 jam 45 menit lagi sebelum mengirim laporan baru."
```

The system calculates remaining time and displays it to the user.

## Implementation Flow

```
User submits form
    ↓
Extract no_hp from POST data
    ↓
Query database for last report with same no_hp
    ↓
Found?
├── NO  → Allow submission (first time user)
└── YES → Calculate time difference
          ├── < 2 hours → REJECT (show error)
          └── ≥ 2 hours → ALLOW (proceed)
```

## Security Features

✅ **SQL Injection Protection**: Uses PDO prepared statements

```php
$checkStmt = $conn->prepare("SELECT tanggal_lapor FROM laporan WHERE no_hp = :no_hp ...");
$checkStmt->bindParam(':no_hp', $no_hp);
```

✅ **Server-side Enforcement**: Cannot be bypassed with client-side manipulation

✅ **Fail-safe**: If rate limit check fails (DB error), allows submission to prevent legitimate users from being blocked

✅ **Error Logging**: Logs rate limit violations for monitoring

```php
error_log("Rate limit exceeded for no_hp: {$no_hp}. Time diff: {$timeDiff}s");
```

## Configuration Options

### Change Time Interval

**2 hours (default)**

```php
$rate_limit_hours = 2;
```

**30 minutes**

```php
$rate_limit_hours = 0.5; // 30 minutes
```

**24 hours (1 day)**

```php
$rate_limit_hours = 24;
```

**1 week**

```php
$rate_limit_hours = 168; // 7 days × 24 hours
```

### Disable Rate Limiting

Set to 0 to effectively disable:

```php
$rate_limit_hours = 0; // No limit
```

Or comment out the entire rate limiting block.

## Testing

### Test Case 1: First Submission

```
Input: no_hp = "081234567890" (never submitted before)
Expected: ✅ ALLOWED
```

### Test Case 2: Immediate Re-submission

```
Input: no_hp = "081234567890" (just submitted 5 minutes ago)
Expected: ❌ REJECTED
Message: "Silakan menunggu 1 jam 55 menit lagi..."
```

### Test Case 3: After Interval Passed

```
Input: no_hp = "081234567890" (submitted 2+ hours ago)
Expected: ✅ ALLOWED
```

### Test Case 4: Different Phone Numbers

```
Input: no_hp = "081234567890" (submitted 1 minute ago)
Input: no_hp = "089876543210" (different number)
Expected: ✅ BOTH ALLOWED (rate limit is per phone number)
```

## Database Requirements

**Required Column**: `tanggal_lapor` (DATETIME)

Already exists in current schema:

```sql
CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_hp VARCHAR(15) NOT NULL,
    tanggal_lapor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ...
);
```

**Index Recommendation** (for performance):

```sql
ALTER TABLE laporan ADD INDEX idx_no_hp_tanggal (no_hp, tanggal_lapor);
```

This composite index will speed up the rate limit query.

## Performance Considerations

- **Query Impact**: One additional SELECT query per submission
- **Query Time**: ~1-5ms (with proper indexing)
- **Memory Impact**: Negligible (only fetches 1 row)
- **Scalability**: Handles millions of records efficiently with index

## Monitoring & Analytics

### Check Rate Limit Violations

```sql
-- Count rejections in last 24 hours (check error logs)
SELECT COUNT(*)
FROM error_log
WHERE message LIKE '%Rate limit exceeded%'
  AND timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR);
```

### Find Frequent Submitters

```sql
-- Users who submitted multiple times (bypassed or after waiting)
SELECT no_hp, COUNT(*) as submission_count
FROM laporan
WHERE tanggal_lapor >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY no_hp
HAVING COUNT(*) > 1
ORDER BY submission_count DESC;
```

### Average Time Between Submissions

```sql
SELECT
    no_hp,
    COUNT(*) as total_submissions,
    AVG(TIMESTAMPDIFF(HOUR, prev_time, tanggal_lapor)) as avg_hours_between
FROM (
    SELECT
        no_hp,
        tanggal_lapor,
        LAG(tanggal_lapor) OVER (PARTITION BY no_hp ORDER BY tanggal_lapor) as prev_time
    FROM laporan
) subquery
WHERE prev_time IS NOT NULL
GROUP BY no_hp
HAVING COUNT(*) > 1;
```

## Future Enhancements

1. **IP-based Rate Limiting**: Combine phone number + IP address
2. **Progressive Delays**: Increase delay for repeat offenders
3. **Admin Override**: Allow admins to reset rate limit for specific users
4. **Whitelist**: Allow certain phone numbers to bypass rate limit
5. **Analytics Dashboard**: Real-time rate limit statistics

## Troubleshooting

### User reports false rate limit error

**Check**:

1. Verify their last submission time in database
2. Check server timezone settings
3. Ensure DateTime calculations are correct

**Fix**:

```sql
-- Manually check last submission
SELECT tanggal_lapor, NOW() as current_time,
       TIMESTAMPDIFF(HOUR, tanggal_lapor, NOW()) as hours_since
FROM laporan
WHERE no_hp = '081234567890'
ORDER BY tanggal_lapor DESC
LIMIT 1;
```

### Rate limiting not working

**Checklist**:

- [ ] Code is in correct location (before file upload validation)
- [ ] `$rate_limit_hours` is set correctly (not 0)
- [ ] Database connection is working
- [ ] no_hp field is being passed correctly from form
- [ ] Error redirects are not being cached

## Code Location

**File**: `/process/submit_laporan.php`

**Lines**: After form data extraction, before file upload handling

**Section marker**:

```php
// ============================================
// RATE LIMITING: Check submission frequency
// ============================================
```

## Related Files

- `process/submit_laporan.php` - Rate limiting logic
- `public/index.php` - Error message display
- `database/laporwarga.sql` - Table schema

## Support

For questions or issues:

- Check error logs: `error_log` file
- Enable debug mode to see detailed SQL queries
- Contact: backend-team@laporwarga.com
