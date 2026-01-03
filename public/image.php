
<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'foto';

if ($id > 0) {
    $column = ($type === 'foto_after') ? 'foto_after' : 'foto';
    
    $stmt = $conn->prepare("SELECT {$column} as foto FROM laporan WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row && $row['foto']) {
        $filepath = __DIR__ . '/../uploads/' . $row['foto'];
        
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

// Placeholder jika gambar tidak ada
header('Content-Type: image/svg+xml');
echo '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
    <rect width="400" height="300" fill="#f0f0f0"/>
    <text x="50%" y="50%" text-anchor="middle" font-family="Arial" font-size="20" fill="#999">
        Gambar tidak tersedia
    </text>
</svg>';
exit;