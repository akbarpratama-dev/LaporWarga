<?php
/**
 * Report Tracker Cookie Helper Functions
 * Manages cookie-based report tracking without requiring user login
 * 
 * Cookie: laporwarga_reports
 * Format: JSON array of report IDs [12, 25, 31]
 * Expiry: 30 days
 */

class ReportTracker {
    
    const COOKIE_NAME = 'laporwarga_reports';
    const COOKIE_EXPIRY_DAYS = 30;
    
    /**
     * Add a report ID to the tracking cookie
     * @param int $reportId - The ID of the newly created report
     * @return bool - Success status
     */
    public static function addReport($reportId) {
        // Validate input
        $reportId = (int) $reportId;
        if ($reportId <= 0) {
            return false;
        }
        
        // Get existing reports from cookie
        $reports = self::getTrackedReports();
        
        // Add new report ID if not already present
        if (!in_array($reportId, $reports)) {
            $reports[] = $reportId;
        }
        
        // Save updated array back to cookie
        $expiry = time() + (self::COOKIE_EXPIRY_DAYS * 24 * 60 * 60);
        $jsonData = json_encode($reports);
        
        return setcookie(
            self::COOKIE_NAME,
            $jsonData,
            $expiry,
            '/',
            '',
            false, // HTTPS only - set true in production
            true   // HTTP only - prevents JavaScript access
        );
    }
    
    /**
     * Get all tracked report IDs from cookie
     * @return array - Array of report IDs (integers)
     */
    public static function getTrackedReports() {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return [];
        }
        
        // Decode JSON from cookie
        $data = $_COOKIE[self::COOKIE_NAME];
        $reports = json_decode($data, true);
        
        // Validate: must be array
        if (!is_array($reports)) {
            return [];
        }
        
        // Sanitize: ensure all values are integers
        $reports = array_map('intval', $reports);
        
        // Remove invalid IDs (0 or negative)
        $reports = array_filter($reports, function($id) {
            return $id > 0;
        });
        
        // Remove duplicates and re-index
        $reports = array_values(array_unique($reports));
        
        return $reports;
    }
    
    /**
     * Fetch report data for all tracked report IDs from database
     * @param PDO $conn - Database connection
     * @return array - Array of report records
     */
    public static function fetchTrackedReportsFromDB($conn) {
        $reportIds = self::getTrackedReports();
        
        if (empty($reportIds)) {
            return [];
        }
        
        try {
            // Build IN clause placeholders
            $placeholders = implode(',', array_fill(0, count($reportIds), '?'));
            
            // Query database
            $query = "SELECT id, kode_laporan, nama_pelapor, kategori, lokasi, status, 
                             tanggal_lapor, tanggal_selesai
                      FROM laporan 
                      WHERE id IN ({$placeholders})
                      ORDER BY tanggal_lapor DESC";
            
            $stmt = $conn->prepare($query);
            
            // Bind parameters
            foreach ($reportIds as $index => $id) {
                $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log('Report Tracker DB Error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if user has any tracked reports
     * @return bool
     */
    public static function hasTrackedReports() {
        $reports = self::getTrackedReports();
        return !empty($reports);
    }
    
    /**
     * Clear all tracked reports (for testing or privacy)
     * @return bool
     */
    public static function clearTracking() {
        return setcookie(
            self::COOKIE_NAME,
            '',
            time() - 3600,
            '/',
            '',
            false,
            true
        );
    }
}
?>
