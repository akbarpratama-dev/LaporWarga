<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'foto';

if ($id > 0) {
    // Tentukan kolom berdasarkan type
    if ($type === 'foto_after') {
        $blobColumn = 'foto_after_blob';
        $mimeColumn = 'foto_after_mime';
        $fileColumn = 'foto_after';
    } else {
        $blobColumn = 'foto_blob';
        $mimeColumn = 'foto_mime';
        $fileColumn = 'foto';
    }
    
    // Query untuk ambil BLOB dan file path (fallback)
    $stmt = $conn->prepare("SELECT {$blobColumn} as blob_data, {$mimeColumn} as mime_type, {$fileColumn} as filename FROM laporan WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        // Prioritas 1: Coba dari BLOB database
        if (!empty($row['blob_data'])) {
            $mimeType = $row['mime_type'] ?: 'image/jpeg';
            
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . strlen($row['blob_data']));
            header('Cache-Control: public, max-age=86400');
            
            echo $row['blob_data'];
            exit;
        }
        
        // Prioritas 2: Fallback ke file di folder uploads/
        if (!empty($row['filename'])) {
            $filepath = __DIR__ . '/../uploads/' . $row['filename'];
            
            if (file_exists($filepath)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $filepath);
                finfo_close($finfo);
                
                header('Content-Type: ' . $mimeType);
                header('Content-Length: ' . filesize($filepath));
                header('Cache-Control: public, max-age=86400');
                
                readfile($filepath);
                exit;
            }
        }
    }
}

// Placeholder jika gambar tidak ada
header('Content-Type: image/svg+xml');
echo '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
    <rect width="400" height="300" fill="#f0f0f0"/>
    <text x="50%" y="50%" text-anchor="middle" font-family="Arial" font-size="20" fill="#999">
        Gambar tidak tersedia
    </text>
</svg>';
exit;