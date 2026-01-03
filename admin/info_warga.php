<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Ambil semua info warga
$query = $conn->query("
    SELECT * FROM info_warga 
    ORDER BY tanggal_publish DESC
");
$infos = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Info Warga - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="/public/assets/css/style.css">
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
      <a href="/admin/info_warga.php" class="sidebar-link active"><i class="ri-megaphone-line"></i> Info Warga</a>
      <a href="/admin/logout.php" class="sidebar-link"><i class="ri-logout-box-line"></i> Logout</a>
    </nav>
  </aside>

  <main class="admin-content">
    <div class="admin-header">
      <h1>Info Warga</h1>
      <div class="admin-user"><i class="ri-user-line"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
    </div>

    <div style="margin-bottom: 20px;">
      <a href="/admin/upload_info.php" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Info Baru
      </a>
    </div>

    <div class="info-grid">
      <?php if (empty($infos)): ?>
        <div style="text-align: center; padding: 60px; grid-column: 1/-1;">
          <i class="ri-megaphone-line" style="font-size: 64px; color: #ccc;"></i>
          <p style="margin-top: 16px; color: #999;">Belum ada info warga yang dipublikasikan</p>
        </div>
      <?php else: ?>
        <?php foreach ($infos as $info): ?>
        <div class="info-card">
          <?php if ($info['gambar']): ?>
          <img src="/uploads/<?php echo htmlspecialchars($info['gambar']); ?>" alt="Info">
          <?php endif; ?>
          <div class="info-content">
            <h3><?php echo htmlspecialchars($info['judul']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars(substr($info['konten'], 0, 150))); ?>...</p>
            <div class="info-meta">
              <span><i class="ri-calendar-line"></i> <?php echo date('d M Y', strtotime($info['tanggal_publish'])); ?></span>
            </div>
            <div style="margin-top: 12px;">
              <a href="/admin/edit_info.php?id=<?php echo $info['id']; ?>" class="btn btn-sm btn-primary">
                <i class="ri-edit-line"></i> Edit
              </a>
              <a href="/admin/delete_info.php?id=<?php echo $info['id']; ?>" 
                 class="btn btn-sm btn-danger" 
                 onclick="return confirm('Yakin ingin menghapus info ini?')">
                <i class="ri-delete-bin-line"></i> Hapus
              </a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>