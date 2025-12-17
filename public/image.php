<?php
/**
 * Image serving script - retrieves images from database BLOB
 * Usage: image.php?id=123&type=foto (or foto_after)
 */
require_once '../config/database.php';

// Get parameters
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) && in_array($_GET['type'], ['foto', 'foto_after']) ? $_GET['type'] : 'foto';

if ($id <= 0) {
    http_response_code(400);
    exit('Invalid ID');
}

$database = new Database();
$conn = $database->getConnection();

// Determine column names
$blobCol = ($type === 'foto_after') ? 'foto_after_blob' : 'foto_blob';
$mimeCol = ($type === 'foto_after') ? 'foto_after_mime' : 'foto_mime';

try {
    $query = "SELECT {$blobCol} as image_data, {$mimeCol} as mime_type FROM laporan WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row && $row['image_data']) {
        // Set proper headers
        header('Content-Type: ' . ($row['mime_type'] ?: 'image/jpeg'));
        header('Content-Length: ' . strlen($row['image_data']));
        header('Cache-Control: public, max-age=86400'); // Cache 1 day
        
        // Output image
        echo $row['image_data'];
        exit();
    } else {
        // No image found - return 404
        http_response_code(404);
        exit('Image not found');
    }
} catch (PDOException $e) {
    error_log('Image fetch error: ' . $e->getMessage());
    http_response_code(500);
    exit('Database error');
}
?>
