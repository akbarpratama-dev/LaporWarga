# 🛡️ Rate Limiting Implementation

## Quick Start

### 1. Install (Migration)

```bash
# Add performance index for faster queries
mysql -u root -p LaporWarga2 < database/migration_add_rate_limit_index.sql
```

### 2. Test

```bash
# Run test script to verify it's working
cd tests
/Applications/XAMPP/xamppfiles/bin/php test_rate_limiting.php
```

### 3. Configure

Edit `process/submit_laporan.php`:

```php
$rate_limit_hours = 2; // Change this value
```

## How It Works

```
┌─────────────────────────────────────────────────────────┐
│                   USER SUBMITS FORM                     │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│         Extract no_hp from POST data                    │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│   Query: Get last submission for this no_hp            │
│   SELECT tanggal_lapor FROM laporan                     │
│   WHERE no_hp = ? ORDER BY tanggal_lapor DESC LIMIT 1   │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
              Found previous?
                     │
        ┌────────────┴────────────┐
        │                         │
       YES                       NO
        │                         │
        ▼                         ▼
   Calculate             ┌──────────────┐
   time diff             │ ALLOW        │
        │                │ (First time) │
        ▼                └──────────────┘
   < 2 hours?
        │
  ┌─────┴─────┐
  │           │
 YES         NO
  │           │
  ▼           ▼
┌─────┐   ┌──────┐
│BLOCK│   │ALLOW │
└─────┘   └──────┘
```

## Example Scenarios

### ✅ Scenario 1: First Time User

```
Phone: 081234567890
Last submission: None
Result: ALLOWED
```

### ❌ Scenario 2: Too Soon

```
Phone: 081234567890
Last submission: 5 minutes ago
Rate limit: 2 hours
Result: BLOCKED
Message: "Silakan menunggu 1 jam 55 menit lagi..."
```

### ✅ Scenario 3: After Waiting

```
Phone: 081234567890
Last submission: 2 hours 5 minutes ago
Rate limit: 2 hours
Result: ALLOWED
```

## Configuration Examples

### Standard (2 hours)

```php
$rate_limit_hours = 2;
```

### Strict (24 hours)

```php
$rate_limit_hours = 24;
```

### Lenient (30 minutes)

```php
$rate_limit_hours = 0.5;
```

### Very Strict (1 week)

```php
$rate_limit_hours = 168; // 7 × 24
```

## Error Messages

The system shows remaining wait time:

```
✅ Good UX: "Silakan menunggu 1 jam 45 menit lagi..."
❌ Bad UX: "Silakan menunggu 2 jam..." (not accurate)
```

## Security

✅ **SQL Injection Protected**

```php
$stmt = $conn->prepare("SELECT ... WHERE no_hp = :no_hp");
$stmt->bindParam(':no_hp', $no_hp);
```

✅ **Server-side Enforcement**

- Cannot be bypassed with browser tools
- No client-side JavaScript required

✅ **Fail-safe Design**

- If rate limit check fails → Allows submission
- Prevents legitimate users from being locked out

## Performance

**Query Time**: ~1-5ms (with index)

**With Index**:

```
Rows examined: 1-10
Type: ref (indexed lookup)
```

**Without Index**:

```
Rows examined: ALL (full table scan)
Type: ALL (slow!)
```

### Add Index (Recommended)

```sql
ALTER TABLE laporan
ADD INDEX idx_no_hp_tanggal (no_hp, tanggal_lapor DESC);
```

## Monitoring

### Check Violations (Error Log)

```bash
grep "Rate limit exceeded" error_log | wc -l
```

### Find Frequent Submitters

```sql
SELECT no_hp, COUNT(*) as submissions
FROM laporan
WHERE tanggal_lapor >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY no_hp
HAVING COUNT(*) > 3
ORDER BY submissions DESC;
```

## Testing

### Manual Test

1. Submit a report with phone: `081234567890`
2. Immediately try to submit another report with same phone
3. Should see: ❌ "Silakan menunggu ... lagi..."

### Automated Test

```bash
cd tests
/Applications/XAMPP/xamppfiles/bin/php test_rate_limiting.php
```

Expected output:

```
✅ Database connected
✅ Found previous submission
❌ SUBMISSION BLOCKED
   Wait time: 1 jam 45 menit
✅ Using optimized index
```

## Troubleshooting

### Issue: Rate limit not working

**Check**:

1. Code is in `process/submit_laporan.php` (after line 30)
2. `$rate_limit_hours > 0`
3. Database connection works
4. Form sends `no_hp` field

**Debug**:

```php
// Add this after rate limit check
error_log("Rate limit check: timeDiff={$timeDiff}, limit={$rate_limit_seconds}");
```

### Issue: False positives

**Check server timezone**:

```php
echo date_default_timezone_get(); // Should match your region
```

**Fix timezone**:

```php
// Add to config/database.php
date_default_timezone_set('Asia/Jakarta');
```

## Files Modified

```
process/submit_laporan.php     ← Rate limiting logic
public/index.php               ← Error message display
database/migration_*.sql       ← Performance index
docs/RATE_LIMITING.md         ← Full documentation
tests/test_rate_limiting.php  ← Test script
```

## Integration Checklist

- [x] Add rate limiting code to `submit_laporan.php`
- [x] Update error handling in `index.php`
- [x] Create database index for performance
- [x] Add error message display
- [x] Test with real submissions
- [x] Monitor error logs
- [ ] Adjust interval based on usage patterns
- [ ] Add admin dashboard for rate limit stats

## Support

Questions? Check:

- `docs/RATE_LIMITING.md` - Full documentation
- `tests/test_rate_limiting.php` - Test examples
- Error logs for debugging

---

**Status**: ✅ Production Ready
**Version**: 1.0
**Last Updated**: December 2025
