<?php
/**
 * Cookie-based Report Tracking Test Script
 * Tests the functionality of report tracking without login
 * 
 * Run in browser: http://localhost/LaporWarga1/tests/test_cookie_tracking.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/report_tracker.php';

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'>";
echo "<title>Cookie Tracking Test</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 2rem; background: #f5f5f5; }
    .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    h1 { color: #333; border-bottom: 3px solid #c41e3a; padding-bottom: 0.5rem; }
    h2 { color: #c41e3a; margin-top: 2rem; }
    .test-section { background: #f9f9f9; padding: 1rem; margin: 1rem 0; border-left: 4px solid #c41e3a; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: #666; }
    table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
    table th, table td { padding: 0.5rem; border: 1px solid #ddd; text-align: left; }
    table th { background: #c41e3a; color: white; }
    .btn { display: inline-block; padding: 0.5rem 1rem; background: #c41e3a; color: white; text-decoration: none; border-radius: 4px; margin: 0.5rem 0.5rem 0.5rem 0; }
    .btn:hover { background: #a01830; }
    code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
</style>";
echo "</head><body>";
echo "<div class='container'>";
echo "<h1>🍪 Cookie-based Report Tracking Test</h1>";

// Database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo "<p class='error'>❌ Database connection failed</p></body></html>";
    exit;
}

echo "<p class='success'>✅ Database connected</p>";

// ============================================
// TEST 1: Check current cookie status
// ============================================
echo "<div class='test-section'>";
echo "<h2>Test 1: Current Cookie Status</h2>";

$hasTracked = ReportTracker::hasTrackedReports();
$trackedIds = ReportTracker::getTrackedReports();

echo "<p><strong>Has tracked reports:</strong> " . ($hasTracked ? '<span class="success">YES</span>' : '<span class="info">NO</span>') . "</p>";
echo "<p><strong>Tracked Report IDs:</strong> ";

if (empty($trackedIds)) {
    echo "<span class='info'>None</span>";
} else {
    echo "<code>[" . implode(', ', $trackedIds) . "]</code>";
}

echo "</p>";

// Check raw cookie
if (isset($_COOKIE[ReportTracker::COOKIE_NAME])) {
    echo "<p><strong>Raw cookie value:</strong> <code>" . htmlspecialchars($_COOKIE[ReportTracker::COOKIE_NAME]) . "</code></p>";
} else {
    echo "<p><strong>Raw cookie:</strong> <span class='info'>Not set</span></p>";
}

echo "</div>";

// ============================================
// TEST 2: Fetch tracked reports from database
// ============================================
echo "<div class='test-section'>";
echo "<h2>Test 2: Fetch Tracked Reports from Database</h2>";

if ($hasTracked) {
    $reports = ReportTracker::fetchTrackedReportsFromDB($conn);
    
    if (!empty($reports)) {
        echo "<p class='success'>✅ Found " . count($reports) . " report(s) in database</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Kode Laporan</th><th>Nama</th><th>Kategori</th><th>Status</th><th>Tanggal</th></tr>";
        
        foreach ($reports as $report) {
            echo "<tr>";
            echo "<td>{$report['id']}</td>";
            echo "<td>" . htmlspecialchars($report['kode_laporan']) . "</td>";
            echo "<td>" . htmlspecialchars($report['nama_pelapor']) . "</td>";
            echo "<td>" . htmlspecialchars($report['kategori']) . "</td>";
            echo "<td>" . htmlspecialchars($report['status']) . "</td>";
            echo "<td>" . date('d/m/Y H:i', strtotime($report['tanggal_lapor'])) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p class='error'>⚠️ No reports found in database (IDs may be invalid)</p>";
    }
} else {
    echo "<p class='info'>ℹ️ No tracked reports to fetch</p>";
}

echo "</div>";

// ============================================
// TEST 3: Manual cookie manipulation
// ============================================
echo "<div class='test-section'>";
echo "<h2>Test 3: Manual Cookie Actions</h2>";

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'add' && isset($_GET['id'])) {
        $reportId = (int) $_GET['id'];
        if (ReportTracker::addReport($reportId)) {
            echo "<p class='success'>✅ Added report ID {$reportId} to cookie</p>";
            echo "<p class='info'>Refresh page to see changes</p>";
        } else {
            echo "<p class='error'>❌ Failed to add report ID</p>";
        }
    }
    
    if ($action === 'clear') {
        if (ReportTracker::clearTracking()) {
            echo "<p class='success'>✅ Cookie cleared</p>";
            echo "<p class='info'>Refresh page to see changes</p>";
        } else {
            echo "<p class='error'>❌ Failed to clear cookie</p>";
        }
    }
}

// Get sample report IDs from database
$sampleStmt = $conn->query("SELECT id, kode_laporan FROM laporan ORDER BY tanggal_lapor DESC LIMIT 5");
$sampleReports = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p><strong>Available Actions:</strong></p>";

if (!empty($sampleReports)) {
    echo "<p>Add sample report to cookie:</p>";
    foreach ($sampleReports as $sample) {
        echo "<a class='btn' href='?action=add&id={$sample['id']}'>Add ID {$sample['id']} ({$sample['kode_laporan']})</a> ";
    }
}

echo "<br>";
echo "<a class='btn' href='?action=clear' style='background: #666;'>Clear Cookie</a>";

echo "</div>";

// ============================================
// TEST 4: Security validation
// ============================================
echo "<div class='test-section'>";
echo "<h2>Test 4: Security Validation</h2>";

echo "<p>Testing cookie sanitization and validation:</p>";

// Test invalid values
$testCases = [
    'Empty array' => '[]',
    'Valid IDs' => '[1, 2, 3]',
    'Mixed valid/invalid' => '[1, -5, 0, 10, "abc"]',
    'Duplicates' => '[5, 5, 5, 10, 10]',
    'Non-array' => '"not an array"',
    'Malicious input' => '{"injection": "attempt"}'
];

echo "<table>";
echo "<tr><th>Test Case</th><th>Input</th><th>Output</th><th>Result</th></tr>";

foreach ($testCases as $name => $jsonInput) {
    // Simulate cookie value
    $_COOKIE[ReportTracker::COOKIE_NAME] = $jsonInput;
    $result = ReportTracker::getTrackedReports();
    $resultJson = json_encode($result);
    $isValid = is_array($result) && (empty($result) || ctype_digit(implode('', $result)));
    
    echo "<tr>";
    echo "<td>{$name}</td>";
    echo "<td><code>" . htmlspecialchars($jsonInput) . "</code></td>";
    echo "<td><code>" . htmlspecialchars($resultJson) . "</code></td>";
    echo "<td>" . ($isValid ? '<span class="success">✅ Valid</span>' : '<span class="error">❌ Invalid</span>') . "</td>";
    echo "</tr>";
}

// Restore original cookie
if (isset($_COOKIE[ReportTracker::COOKIE_NAME])) {
    $_COOKIE[ReportTracker::COOKIE_NAME] = $_COOKIE[ReportTracker::COOKIE_NAME];
}

echo "</table>";

echo "</div>";

// ============================================
// Integration Links
// ============================================
echo "<div class='test-section'>";
echo "<h2>Integration Test</h2>";
echo "<p>Visit these pages to test the full workflow:</p>";
echo "<a class='btn' href='../public/index.php#lapor' target='_blank'>Submit Report (Main Page)</a>";
echo "<a class='btn' href='../public/cek_status.php' target='_blank'>Check Status (Cookie View)</a>";
echo "</div>";

echo "<p style='margin-top: 2rem; color: #666; font-size: 0.9rem;'>";
echo "Test completed at: " . date('Y-m-d H:i:s');
echo "</p>";

echo "</div></body></html>";
?>
