-- Migration: Add status timestamp columns to laporan table
-- This migration adds columns to track when each status change occurs

USE LaporWarga2;

-- Add timestamp columns for status tracking
ALTER TABLE laporan 
ADD COLUMN IF NOT EXISTS diterima_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp ketika status Diterima',
ADD COLUMN IF NOT EXISTS diproses_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp ketika status Diproses',
ADD COLUMN IF NOT EXISTS selesai_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp ketika status Selesai';

-- Update existing records: set diterima_at to tanggal_lapor for all records
UPDATE laporan 
SET diterima_at = tanggal_lapor 
WHERE diterima_at IS NULL;

-- For existing "Diproses" records, set diproses_at to current timestamp (estimation)
UPDATE laporan 
SET diproses_at = DATE_ADD(tanggal_lapor, INTERVAL 1 DAY)
WHERE status IN ('Diproses', 'Selesai') AND diproses_at IS NULL;

-- For existing "Selesai" records, use tanggal_selesai or estimate
UPDATE laporan 
SET selesai_at = COALESCE(tanggal_selesai, DATE_ADD(tanggal_lapor, INTERVAL 3 DAY))
WHERE status = 'Selesai' AND selesai_at IS NULL;
