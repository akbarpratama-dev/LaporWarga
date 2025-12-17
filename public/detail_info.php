<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if(!$id) {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM info_warga WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$info = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$info){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Info - <?php echo htmlspecialchars($info['judul']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=2.2">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <nav class="navbar">
    <div class="container">
      <div class="nav-brand">
                <img src="assets/images/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
                <span class="brand-text">LaporWarga</span>
            </div>
      <div class="navbar-menu"><a href="index.php#info-warga" class="btn btn-outline"><i class="ri-arrow-left-line"></i> Kembali</a></div>
    </div>
  </nav>

  <main class="detail-container">
    <div class="container">
      <div class="detail-header">
        <h1><?php echo htmlspecialchars($info['judul']); ?></h1>
        <span class="badge <?php echo ($info['kategori'] ?? 'Info') === 'Pemberitahuan' ? 'badge-warning' : 'badge-info'; ?>">
            <?php echo htmlspecialchars($info['kategori'] ?? 'Info'); ?>
        </span>
      </div>

      <div class="detail-grid">
        <div>
          <div class="detail-section">
            <h3>Deskripsi Lengkap</h3>
            <p style="white-space: pre-line; line-height: 1.8;"><?php echo htmlspecialchars($info['deskripsi']); ?></p>
          </div>
        </div>

        <div>
          <div class="detail-section">
            <h3>Informasi Kegiatan</h3>
            <table class="detail-table">
              <tr><td>Lokasi</td><td><?php echo htmlspecialchars($info['lokasi']); ?></td></tr>
              <tr><td>Tanggal Mulai</td><td><?php echo date('d M Y', strtotime($info['tanggal_mulai'])); ?></td></tr>
              <?php if($info['tanggal_selesai']): ?>
              <tr><td>Tanggal Selesai</td><td><?php echo date('d M Y', strtotime($info['tanggal_selesai'])); ?></td></tr>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="container">
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> LaporWarga. All rights reserved.</p>
        </div>
    </div>
  </footer>
</body>
</html>
