<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
	header("Location: ../admin/login.php?error=unauthorized");
	exit();
}

require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header("Location: ../admin/laporan_masuk.php");
	exit();
}

$database = new Database();
$conn = $database->getConnection();

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;
$catatan_admin = isset($_POST['keterangan']) ? $_POST['keterangan'] : null;
$biaya = isset($_POST['biaya']) && $_POST['biaya'] !== '' ? $_POST['biaya'] : null;
$durasi = isset($_POST['durasi']) ? $_POST['durasi'] : null;

if(!$id || !$status) {
	header("Location: ../admin/laporan_masuk.php?error=invalid");
	exit();
}

// Get current report data (for existing foto_after)
$stmt = $conn->prepare("SELECT foto_after FROM laporan WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$current = $stmt->fetch(PDO::FETCH_ASSOC);

$foto_after = $current ? $current['foto_after'] : null;

// Handle file upload if provided (admin foto_after - simpan sebagai BLOB)
$foto_after_blob = null;
$foto_after_mime = null;
if (isset($_FILES['foto_after']) && is_array($_FILES['foto_after']) && $_FILES['foto_after']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['foto_after']['error'] === UPLOAD_ERR_OK) {
        $maxSize = 2 * 1024 * 1024;
        $allowedExt = ['jpg', 'jpeg', 'png'];
        $fileTmp  = $_FILES['foto_after']['tmp_name'];
        $fileName = $_FILES['foto_after']['name'];
        $fileSize = (int) $_FILES['foto_after']['size'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
        $mime  = $finfo ? finfo_file($finfo, $fileTmp) : null;
        if ($finfo) finfo_close($finfo);
        $allowedMime = ['image/jpeg','image/png'];

        if (!in_array($ext, $allowedExt) || !in_array($mime, $allowedMime)) {
            error_log('Admin upload foto_after ditolak: ext/mime tidak valid. ext=' . $ext . ' mime=' . $mime);
        } elseif ($fileSize > $maxSize) {
            error_log('Admin upload foto_after gagal: ukuran > 2MB');
        } else {
            // Read into BLOB
            $foto_after_blob = file_get_contents($fileTmp);
            $foto_after_mime = $mime;
            if ($foto_after_blob === false) {
                error_log('Admin gagal membaca file untuk BLOB');
                $foto_after_blob = null;
                $foto_after_mime = null;
            }
        }
    } else {
        error_log('Admin error upload foto_after (kode): ' . $_FILES['foto_after']['error']);
    }
}try {
	// Auto-migrate: ensure timestamp columns exist
	$checkCol = $conn->query("SHOW COLUMNS FROM laporan LIKE 'diterima_at'");
	if ($checkCol && $checkCol->rowCount() === 0) {
		$conn->exec("ALTER TABLE laporan ADD COLUMN diterima_at TIMESTAMP NULL DEFAULT NULL");
		$conn->exec("ALTER TABLE laporan ADD COLUMN diproses_at TIMESTAMP NULL DEFAULT NULL");
		$conn->exec("ALTER TABLE laporan ADD COLUMN selesai_at TIMESTAMP NULL DEFAULT NULL");
	}

	if($status === 'Selesai') {
		// Only update BLOB if new file was uploaded
		if ($foto_after_blob !== null) {
			$sql = "UPDATE laporan SET status = :status, catatan_admin = :catatan_admin, biaya = :biaya, durasi = :durasi, foto_after_blob = :foto_after_blob, foto_after_mime = :foto_after_mime, tanggal_selesai = NOW(), selesai_at = NOW() WHERE id = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':status', $status);
			$stmt->bindParam(':catatan_admin', $catatan_admin);
			$stmt->bindParam(':biaya', $biaya);
			$stmt->bindParam(':durasi', $durasi);
			$stmt->bindParam(':foto_after_blob', $foto_after_blob, PDO::PARAM_LOB);
			$stmt->bindParam(':foto_after_mime', $foto_after_mime);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		} else {
			$sql = "UPDATE laporan SET status = :status, catatan_admin = :catatan_admin, biaya = :biaya, durasi = :durasi, tanggal_selesai = NOW(), selesai_at = NOW() WHERE id = :id";
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':status', $status);
			$stmt->bindParam(':catatan_admin', $catatan_admin);
			$stmt->bindParam(':biaya', $biaya);
			$stmt->bindParam(':durasi', $durasi);
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		}
	} elseif($status === 'Diproses') {
		$sql = "UPDATE laporan SET status = :status, diproses_at = NOW() WHERE id = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':status', $status);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	} else {
		$sql = "UPDATE laporan SET status = :status WHERE id = :id";
		$stmt = $conn->prepare($sql);
		$stmt->bindParam(':status', $status);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	}

	if($stmt->execute()) {
		header("Location: ../admin/laporan_masuk.php?success=updated");
	} else {
		header("Location: ../admin/update_laporan.php?id=" . $id . "&error=update");
	}
} catch(PDOException $e) {
	error_log('Update Status Error: ' . $e->getMessage());
	header("Location: ../admin/update_laporan.php?id=" . $id . "&error=database&msg=" . urlencode($e->getMessage()));
}
exit();
?>
