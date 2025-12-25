-- Performance optimization for rate limiting feature
-- Add composite index for faster lookups

USE LaporWarga2;

-- Add index on (no_hp, tanggal_lapor) for rate limit queries
-- This will significantly speed up the "SELECT tanggal_lapor WHERE no_hp = ? ORDER BY tanggal_lapor DESC"
ALTER TABLE laporan 
ADD INDEX IF NOT EXISTS idx_no_hp_tanggal (no_hp, tanggal_lapor DESC);

-- Verification: Check if index was created
SHOW INDEX FROM laporan WHERE Key_name = 'idx_no_hp_tanggal';

-- Test query performance (should use the new index)
EXPLAIN SELECT tanggal_lapor 
FROM laporan 
WHERE no_hp = '081234567890' 
ORDER BY tanggal_lapor DESC 
LIMIT 1;
