<?php
/**
 * Rate Limiting Test Script
 * Test the rate limiting feature for LaporWarga
 * 
 * Usage: php test_rate_limiting.php
 */

require_once __DIR__ . '/../config/database.php';

echo "==============================================\n";
echo "RATE LIMITING TEST SCRIPT\n";
echo "==============================================\n\n";

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("❌ Database connection failed!\n");
}

echo "✅ Database connected\n\n";

// Test configuration
$test_phone = '081234567890';
$rate_limit_hours = 2;

echo "TEST CONFIGURATION:\n";
echo "- Test Phone Number: {$test_phone}\n";
echo "- Rate Limit: {$rate_limit_hours} hours\n\n";

// ============================================
// TEST 1: Check if user has previous submission
// ============================================
echo "TEST 1: Checking previous submissions\n";
echo "----------------------------------------------\n";

$checkStmt = $conn->prepare("
    SELECT 
        tanggal_lapor,
        TIMESTAMPDIFF(SECOND, tanggal_lapor, NOW()) as seconds_since,
        TIMESTAMPDIFF(MINUTE, tanggal_lapor, NOW()) as minutes_since,
        TIMESTAMPDIFF(HOUR, tanggal_lapor, NOW()) as hours_since
    FROM laporan 
    WHERE no_hp = :no_hp 
    ORDER BY tanggal_lapor DESC 
    LIMIT 1
");
$checkStmt->bindParam(':no_hp', $test_phone);
$checkStmt->execute();

$lastReport = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($lastReport) {
    echo "✅ Found previous submission\n";
    echo "   Last submission: {$lastReport['tanggal_lapor']}\n";
    echo "   Time since: {$lastReport['hours_since']} hours, {$lastReport['minutes_since']} minutes\n";
    echo "   Seconds: {$lastReport['seconds_since']}s\n\n";
    
    // ============================================
    // TEST 2: Calculate if submission is allowed
    // ============================================
    echo "TEST 2: Rate limit calculation\n";
    echo "----------------------------------------------\n";
    
    $lastSubmitTime = new DateTime($lastReport['tanggal_lapor']);
    $currentTime = new DateTime();
    $timeDiff = $currentTime->getTimestamp() - $lastSubmitTime->getTimestamp();
    $rate_limit_seconds = $rate_limit_hours * 60 * 60;
    
    echo "Current time: " . $currentTime->format('Y-m-d H:i:s') . "\n";
    echo "Last submit:  " . $lastSubmitTime->format('Y-m-d H:i:s') . "\n";
    echo "Time diff:    {$timeDiff} seconds\n";
    echo "Required:     {$rate_limit_seconds} seconds ({$rate_limit_hours} hours)\n\n";
    
    if ($timeDiff < $rate_limit_seconds) {
        $remaining_seconds = $rate_limit_seconds - $timeDiff;
        $remaining_hours = floor($remaining_seconds / 3600);
        $remaining_minutes = floor(($remaining_seconds % 3600) / 60);
        
        $wait_time = '';
        if ($remaining_hours > 0) {
            $wait_time = $remaining_hours . ' jam ' . $remaining_minutes . ' menit';
        } else {
            $wait_time = $remaining_minutes . ' menit';
        }
        
        echo "❌ SUBMISSION BLOCKED\n";
        echo "   Reason: Too soon (within {$rate_limit_hours} hour limit)\n";
        echo "   Wait time: {$wait_time}\n";
        echo "   Message: \"Anda sudah mengirim laporan sebelumnya. Silakan menunggu {$wait_time} lagi sebelum mengirim laporan baru.\"\n\n";
    } else {
        echo "✅ SUBMISSION ALLOWED\n";
        echo "   Reason: Enough time has passed\n";
        echo "   Can submit: YES\n\n";
    }
} else {
    echo "ℹ️  No previous submission found\n";
    echo "   Status: First-time user or new phone number\n";
    echo "   Can submit: YES\n\n";
}

// ============================================
// TEST 3: Check query performance (with EXPLAIN)
// ============================================
echo "TEST 3: Query performance analysis\n";
echo "----------------------------------------------\n";

$explainStmt = $conn->prepare("
    EXPLAIN SELECT tanggal_lapor 
    FROM laporan 
    WHERE no_hp = :no_hp 
    ORDER BY tanggal_lapor DESC 
    LIMIT 1
");
$explainStmt->bindParam(':no_hp', $test_phone);
$explainStmt->execute();

$explain = $explainStmt->fetch(PDO::FETCH_ASSOC);

echo "Query type: {$explain['type']}\n";
echo "Possible keys: {$explain['possible_keys']}\n";
echo "Key used: {$explain['key']}\n";
echo "Rows examined: {$explain['rows']}\n";

if ($explain['key'] === 'idx_no_hp_tanggal') {
    echo "✅ Using optimized index (idx_no_hp_tanggal)\n";
} elseif ($explain['key']) {
    echo "⚠️  Using index: {$explain['key']} (consider adding idx_no_hp_tanggal)\n";
} else {
    echo "❌ No index used! Performance will be slow.\n";
    echo "   Run: ALTER TABLE laporan ADD INDEX idx_no_hp_tanggal (no_hp, tanggal_lapor DESC);\n";
}

echo "\n";

// ============================================
// TEST 4: Simulate multiple scenarios
// ============================================
echo "TEST 4: Scenario simulation\n";
echo "----------------------------------------------\n";

$scenarios = [
    ['phone' => '081111111111', 'description' => 'New user (never submitted)'],
    ['phone' => '082222222222', 'description' => 'User who submitted 1 hour ago'],
    ['phone' => '083333333333', 'description' => 'User who submitted 3 hours ago'],
];

foreach ($scenarios as $scenario) {
    echo "\nScenario: {$scenario['description']}\n";
    echo "Phone: {$scenario['phone']}\n";
    
    $stmt = $conn->prepare("
        SELECT tanggal_lapor,
               TIMESTAMPDIFF(HOUR, tanggal_lapor, NOW()) as hours_since
        FROM laporan 
        WHERE no_hp = :no_hp 
        ORDER BY tanggal_lapor DESC 
        LIMIT 1
    ");
    $stmt->bindParam(':no_hp', $scenario['phone']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $hours = $result['hours_since'];
        $allowed = $hours >= $rate_limit_hours;
        echo "Last submission: {$result['tanggal_lapor']} ({$hours}h ago)\n";
        echo "Status: " . ($allowed ? "✅ ALLOWED" : "❌ BLOCKED") . "\n";
    } else {
        echo "Status: ✅ ALLOWED (first time)\n";
    }
}

echo "\n";

// ============================================
// SUMMARY
// ============================================
echo "==============================================\n";
echo "TEST SUMMARY\n";
echo "==============================================\n";
echo "Rate limiting is " . (isset($explain['key']) && $explain['key'] === 'idx_no_hp_tanggal' ? "✅ OPTIMIZED" : "⚠️  WORKING (but could be optimized)") . "\n";
echo "\nNext steps:\n";
echo "1. Test with real form submission\n";
echo "2. Monitor error logs for rate limit violations\n";
echo "3. Adjust \$rate_limit_hours if needed\n";

if (!isset($explain['key']) || $explain['key'] !== 'idx_no_hp_tanggal') {
    echo "\nRECOMMENDATION:\n";
    echo "Add performance index:\n";
    echo "  ALTER TABLE laporan ADD INDEX idx_no_hp_tanggal (no_hp, tanggal_lapor DESC);\n";
}

echo "\n==============================================\n";
echo "TEST COMPLETED\n";
echo "==============================================\n";
?>
