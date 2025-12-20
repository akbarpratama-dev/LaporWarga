<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../public/index.php#lapor");
    exit();
}

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    error_log("Database connection failed in submit_laporan.php");
    header("Location: ../public/index.php?error=database&msg=" . urlencode("Koneksi database gagal"));
    exit();
}

// Generate kode laporan unik
$kode_laporan = 'LAP-' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

// Ambil data dari form
$nama_pelapor = trim($_POST['nama_pelapor']);
$no_hp = trim($_POST['no_hp']);
$kategori = trim($_POST['kategori']);
$lokasi = trim($_POST['lokasi']);
$deskripsi = trim($_POST['deskripsi']);
$tanggal_lapor = date('Y-m-d H:i:s');
$status = 'Diterima';

// Handle upload foto (simpan sebagai BLOB di database)
$foto_blob = null;
$foto_mime = null;
if (isset($_FILES['foto']) && is_array($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $maxSize = 2 * 1024 * 1024; // 2MB
        $allowedExt = ['jpg', 'jpeg', 'png'];
        $fileTmp  = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = (int) $_FILES['foto']['size'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // MIME check
        $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
        $mime  = $finfo ? finfo_file($finfo, $fileTmp) : null;
        if ($finfo) finfo_close($finfo);
        $allowedMime = ['image/jpeg','image/png'];

        if (!in_array($ext, $allowedExt) || !in_array($mime, $allowedMime)) {
            error_log('Upload foto gagal: ekstensi/mime tidak diperbolehkan. Ext=' . $ext . ' mime=' . $mime);
        } elseif ($fileSize > $maxSize) {
            error_log('Upload foto gagal: ukuran > 2MB');
        } else {
            // Read file content into BLOB
            $foto_blob = file_get_contents($fileTmp);
            $foto_mime = $mime;
            if ($foto_blob === false) {
                error_log('Gagal membaca file untuk BLOB');
                $foto_blob = null;
                $foto_mime = null;
            }
        }
    } else {
        error_log('Kesalahan upload foto (kode): ' . $_FILES['foto']['error']);
    }
}

try {
    // Auto-migrate: ensure BLOB columns exist
    $checkCol = $conn->query("SHOW COLUMNS FROM laporan LIKE 'foto_blob'");
    if ($checkCol && $checkCol->rowCount() === 0) {
        $conn->exec("ALTER TABLE laporan ADD COLUMN foto_blob MEDIUMBLOB NULL AFTER foto");
        $conn->exec("ALTER TABLE laporan ADD COLUMN foto_mime VARCHAR(50) NULL AFTER foto_blob");
    }
    $checkCol2 = $conn->query("SHOW COLUMNS FROM laporan LIKE 'foto_after_blob'");
    if ($checkCol2 && $checkCol2->rowCount() === 0) {
        $conn->exec("ALTER TABLE laporan ADD COLUMN foto_after_blob MEDIUMBLOB NULL AFTER foto_after");
        $conn->exec("ALTER TABLE laporan ADD COLUMN foto_after_mime VARCHAR(50) NULL AFTER foto_after_blob");
    }
    // Auto-migrate: ensure timestamp columns exist
    $checkCol3 = $conn->query("SHOW COLUMNS FROM laporan LIKE 'diterima_at'");
    if ($checkCol3 && $checkCol3->rowCount() === 0) {
        $conn->exec("ALTER TABLE laporan ADD COLUMN diterima_at TIMESTAMP NULL DEFAULT NULL");
        $conn->exec("ALTER TABLE laporan ADD COLUMN diproses_at TIMESTAMP NULL DEFAULT NULL");
        $conn->exec("ALTER TABLE laporan ADD COLUMN selesai_at TIMESTAMP NULL DEFAULT NULL");
    }

    $query = "INSERT INTO laporan (kode_laporan, nama_pelapor, no_hp, kategori, lokasi, deskripsi, foto_blob, foto_mime, tanggal_lapor, status, diterima_at) 
              VALUES (:kode_laporan, :nama_pelapor, :no_hp, :kategori, :lokasi, :deskripsi, :foto_blob, :foto_mime, :tanggal_lapor, :status, :diterima_at)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':kode_laporan', $kode_laporan);
    $stmt->bindParam(':nama_pelapor', $nama_pelapor);
    $stmt->bindParam(':no_hp', $no_hp);
    $stmt->bindParam(':kategori', $kategori);
    $stmt->bindParam(':lokasi', $lokasi);
    $stmt->bindParam(':deskripsi', $deskripsi);
    $stmt->bindParam(':foto_blob', $foto_blob, PDO::PARAM_LOB);
    $stmt->bindParam(':foto_mime', $foto_mime);
    $stmt->bindParam(':tanggal_lapor', $tanggal_lapor);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':diterima_at', $tanggal_lapor);
    
    if ($stmt->execute()) {
        // Redirect ke halaman sukses dengan kode laporan
        header("Location: ../public/index.php?success=1&kode=" . urlencode($kode_laporan) . "#lapor");
        exit();
    } else {
        error_log("Failed to execute insert statement");
        header("Location: ../public/index.php?error=submit#lapor");
        exit();
    }
} catch (PDOException $e) {
    error_log("Submit Laporan Error: " . $e->getMessage());
    header("Location: ../public/index.php?error=database&msg=" . urlencode($e->getMessage()) . "#lapor");
    exit();
}
?>
