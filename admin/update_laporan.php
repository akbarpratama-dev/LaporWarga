<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$id = $_GET['id'] ?? 0;

if ($id <= 0) {
    header("Location: laporan_masuk.php");
    exit();
}

// Get laporan data
$stmt = $conn->prepare("SELECT * FROM laporan WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    header("Location: laporan_masuk.php?error=notfound");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $catatan_admin = $_POST['catatan_admin'] ?? '';
    $biaya = !empty($_POST['biaya']) ? $_POST['biaya'] : null;
    
    $tanggal_selesai = null;
    if ($status === 'selesai') {
        $tanggal_selesai = date('Y-m-d H:i:s');
    }
    
    // Handle foto after upload
    $foto_after = $report['foto_after'];
    if (isset($_FILES['foto_after']) && $_FILES['foto_after']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['foto_after']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newFilename = 'after_' . time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = '../uploads/' . $newFilename;
            
            if (move_uploaded_file($_FILES['foto_after']['tmp_name'], $uploadPath)) {
                // Delete old foto_after if exists
                if ($foto_after && file_exists('../uploads/' . $foto_after)) {
                    unlink('../uploads/' . $foto_after);
                }
                $foto_after = $newFilename;
            }
        }
    }
    
    try {
        $stmt = $conn->prepare("
            UPDATE laporan 
            SET status = :status, 
                catatan_admin = :catatan_admin, 
                biaya = :biaya, 
                foto_after = :foto_after,
                tanggal_selesai = :tanggal_selesai
            WHERE id = :id
        ");
        
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':catatan_admin', $catatan_admin);
        $stmt->bindParam(':biaya', $biaya);
        $stmt->bindParam(':foto_after', $foto_after);
        $stmt->bindParam(':tanggal_selesai', $tanggal_selesai);
        $stmt->bindParam(':id', $id);
        
        $stmt->execute();
        
        header("Location: update_laporan.php?id=$id&success=1");
        exit();
    } catch (PDOException $e) {
        error_log("Error update laporan: " . $e->getMessage());
        $error = "Gagal mengupdate laporan";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Laporan - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="/public/assets/css/style.css?v=2.1">
  <style>
    .detail-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 24px;
    }
    .detail-item {
      padding: 16px;
      background: #f9fafb;
      border-radius: 8px;
    }
    .detail-label {
      font-size: 13px;
      color: #6b7280;
      margin-bottom: 4px;
    }
    .detail-value {
      font-size: 15px;
      color: #111827;
      font-weight: 500;
    }
    .photo-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 24px;
    }
    .photo-box {
      border: 2px dashed #e5e7eb;
      border-radius: 12px;
      padding: 16px;
      text-align: center;
    }
    .photo-box img {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
      margin-top: 12px;
    }
    .photo-box h4 {
      margin-bottom: 8px;
      color: #374151;
    }
  </style>
</head>
<body class="admin-page">
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <h2>LaporWarga</h2>
      <p>Panel Admin</p>
    </div>
    <nav class="sidebar-nav">
      <a href="/admin/dashboard.php" class="sidebar-link"><i class="ri-dashboard-line"></i> Dashboard</a>
      <a href="/admin/laporan_masuk.php" class="sidebar-link"><i class="ri-inbox-archive-line"></i> Laporan Masuk</a>
      <a href="/admin/laporan_selesai.php" class="sidebar-link"><i class="ri-checkbox-circle-line"></i> Laporan Selesai</a>
      <a href="/admin/info_warga.php" class="sidebar-link"><i class="ri-megaphone-line"></i> Info Warga</a>
      <a href="/admin/logout.php" class="sidebar-link"><i class="ri-logout-box-line"></i> Logout</a>
    </nav>
  </aside>

  <main class="admin-content">
    <div class="admin-header">
      <h1>Detail Laporan #<?php echo htmlspecialchars($report['kode_laporan']); ?></h1>
      <div class="admin-user"><i class="ri-user-line"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
      <i class="ri-checkbox-circle-line"></i> Laporan berhasil diupdate!
    </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
    <div class="alert alert-error">
      <i class="ri-error-warning-line"></i> <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <!-- Detail Laporan -->
    <div class="card">
      <div class="card-header">
        <h3><i class="ri-file-list-line"></i> Informasi Laporan</h3>
      </div>
      <div class="card-body">
        <div class="detail-grid">
          <div class="detail-item">
            <div class="detail-label">Kode Laporan</div>
            <div class="detail-value"><?php echo htmlspecialchars($report['kode_laporan']); ?></div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Status</div>
            <div class="detail-value">
              <span class="badge badge-<?php echo $report['status']; ?>">
                <?php echo ucfirst($report['status']); ?>
              </span>
            </div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Nama Pelapor</div>
            <div class="detail-value"><?php echo htmlspecialchars($report['nama_pelapor']); ?></div>
          </div>
          <div class="detail-item">
            <div class="detail-label">No. Telepon</div>
            <div class="detail-value"><?php echo htmlspecialchars($report['no_telepon']); ?></div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Kategori</div>
            <div class="detail-value"><?php echo htmlspecialchars($report['kategori']); ?></div>
          </div>
          <div class="detail-item">
            <div class="detail-label">Tanggal Lapor</div>
            <div class="detail-value"><?php echo date('d M Y, H:i', strtotime($report['tanggal_lapor'])); ?></div>
          </div>
          <div class="detail-item" style="grid-column: 1 / -1;">
            <div class="detail-label">Lokasi</div>
            <div class="detail-value"><?php echo htmlspecialchars($report['lokasi']); ?></div>
          </div>
          <div class="detail-item" style="grid-column: 1 / -1;">
            <div class="detail-label">Deskripsi</div>
            <div class="detail-value"><?php echo nl2br(htmlspecialchars($report['deskripsi'])); ?></div>
          </div>
        </div>

        <!-- Foto Before/After -->
        <div class="photo-grid">
          <div class="photo-box">
            <h4><i class="ri-image-line"></i> Foto Sebelum</h4>
            <?php if ($report['foto']): ?>
              <img src="/public/image.php?id=<?php echo $report['id']; ?>&type=foto" alt="Foto Before">
            <?php else: ?>
              <p style="color: #9ca3af; margin-top: 20px;">Tidak ada foto</p>
            <?php endif; ?>
          </div>
          <div class="photo-box">
            <h4><i class="ri-image-line"></i> Foto Sesudah</h4>
            <?php if ($report['foto_after']): ?>
              <img src="/public/image.php?id=<?php echo $report['id']; ?>&type=foto_after" alt="Foto After">
            <?php else: ?>
              <p style="color: #9ca3af; margin-top: 20px;">Belum ada foto sesudah</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Update Status -->
    <div class="card" style="margin-top: 24px;">
      <div class="card-header">
        <h3><i class="ri-edit-line"></i> Update Status Laporan</h3>
      </div>
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required class="form-control">
              <option value="diterima" <?php echo $report['status'] === 'diterima' ? 'selected' : ''; ?>>Diterima</option>
              <option value="diproses" <?php echo $report['status'] === 'diproses' ? 'selected' : ''; ?>>Diproses</option>
              <option value="selesai" <?php echo $report['status'] === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
            </select>
          </div>

          <div class="form-group">
            <label for="catatan_admin">Catatan Admin</label>
            <textarea id="catatan_admin" name="catatan_admin" rows="4" class="form-control" placeholder="Tambahkan catatan untuk pelapor"><?php echo htmlspecialchars($report['catatan_admin'] ?? ''); ?></textarea>
          </div>

          <div class="form-group">
            <label for="biaya">Biaya Perbaikan (Rp)</label>
            <input type="number" id="biaya" name="biaya" class="form-control" placeholder="0" value="<?php echo $report['biaya'] ?? ''; ?>">
          </div>

          <div class="form-group">
            <label for="foto_after">Upload Foto Sesudah Perbaikan</label>
            <input type="file" id="foto_after" name="foto_after" accept="image/*" class="form-control">
            <small style="color: #6b7280; margin-top: 4px; display: block;">Format: JPG, PNG, GIF (Max 10MB)</small>
          </div>

          <div style="display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary">
              <i class="ri-save-line"></i> Update Laporan
            </button>
            <a href="/admin/laporan_masuk.php" class="btn btn-secondary">
              <i class="ri-arrow-left-line"></i> Kembali
            </a>
          </div>
        </form>
      </div>
    </div>
  </main>
</body>
</html>