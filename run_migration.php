<?php
/**
 * Migration Runner - Add Status Timestamps
 * Run this file once to add timestamp columns to laporan table
 */

require_once 'config/database.php';

echo "Starting migration: Add status timestamp columns\n";
echo "==============================================\n\n";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Failed to connect to database");
    }
    
    // Check if columns already exist
    $stmt = $conn->query("SHOW COLUMNS FROM laporan LIKE 'diterima_at'");
    $exists = $stmt && $stmt->rowCount() > 0;
    
    if ($exists) {
        echo "✓ Columns already exist. No migration needed.\n";
    } else {
        echo "Adding timestamp columns...\n";
        
        // Add timestamp columns
        $conn->exec("ALTER TABLE laporan 
            ADD COLUMN diterima_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp ketika status Diterima',
            ADD COLUMN diproses_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp ketika status Diproses',
            ADD COLUMN selesai_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp ketika status Selesai'");
        
        echo "✓ Columns added successfully.\n\n";
        
        // Update existing records
        echo "Updating existing records...\n";
        
        // Set diterima_at to tanggal_lapor for all records
        $result = $conn->exec("UPDATE laporan SET diterima_at = tanggal_lapor WHERE diterima_at IS NULL");
        echo "✓ Updated $result records with diterima_at\n";
        
        // For existing "Diproses" records, set diproses_at
        $result = $conn->exec("UPDATE laporan 
            SET diproses_at = DATE_ADD(tanggal_lapor, INTERVAL 1 DAY)
            WHERE status IN ('Diproses', 'Selesai') AND diproses_at IS NULL");
        echo "✓ Updated $result records with diproses_at\n";
        
        // For existing "Selesai" records, use tanggal_selesai or estimate
        $result = $conn->exec("UPDATE laporan 
            SET selesai_at = COALESCE(tanggal_selesai, DATE_ADD(tanggal_lapor, INTERVAL 3 DAY))
            WHERE status = 'Selesai' AND selesai_at IS NULL");
        echo "✓ Updated $result records with selesai_at\n";
    }
    
    echo "\n==============================================\n";
    echo "Migration completed successfully!\n";
    echo "You can now delete this file.\n";
    
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
