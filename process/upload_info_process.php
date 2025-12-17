<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php?error=unauthorized");
    exit();
}

require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $conn = $database->getConnection();

    $judul = htmlspecialchars(strip_tags($_POST['judul']));
    $deskripsi = htmlspecialchars(strip_tags($_POST['deskripsi']));
    $lokasi = htmlspecialchars(strip_tags($_POST['lokasi']));
    $kategori = isset($_POST['kategori']) ? htmlspecialchars(strip_tags($_POST['kategori'])) : 'Info';
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : null;
    try {
        // Ensure kategori column exists (auto-migrate old installs)
        $columnCheck = $conn->query("SHOW COLUMNS FROM info_warga LIKE 'kategori'");
        if ($columnCheck && $columnCheck->rowCount() === 0) {
            $conn->exec("ALTER TABLE info_warga ADD COLUMN kategori ENUM('Info','Pemberitahuan') NOT NULL DEFAULT 'Info' AFTER lokasi");
        }

        $query = "INSERT INTO info_warga (judul, deskripsi, lokasi, kategori, tanggal_mulai, tanggal_selesai) 
                  VALUES (:judul, :deskripsi, :lokasi, :kategori, :tanggal_mulai, :tanggal_selesai)";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':judul', $judul);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':lokasi', $lokasi);
        $stmt->bindParam(':kategori', $kategori);
        $stmt->bindParam(':tanggal_mulai', $tanggal_mulai);
        $stmt->bindParam(':tanggal_selesai', $tanggal_selesai);

        if($stmt->execute()) {
            header("Location: ../admin/info_warga.php?success=1");
        } else {
            header("Location: ../admin/info_warga.php?error=1");
        }
    } catch(PDOException $e) {
        error_log('Upload Info Error: ' . $e->getMessage());
        header("Location: ../admin/info_warga.php?error=database&msg=" . urlencode('Gagal menyimpan info: ' . $e->getMessage()));
    }
} else {
    header("Location: ../admin/info_warga.php");
}
?>
