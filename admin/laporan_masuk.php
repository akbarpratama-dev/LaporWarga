<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Ambil laporan dengan status diterima dan diproses
$query = $conn->query("
    SELECT * FROM laporan 
    WHERE status IN ('diterima', 'diproses')
    ORDER BY tanggal_lapor DESC
");
$reports = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Masuk - Admin</title>
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
      <a href="/admin/laporan_masuk.php" class="sidebar-link active"><i class="ri-inbox-archive-line"></i> Laporan Masuk</a>
      <a href="/admin/laporan_selesai.php" class="sidebar-link"><i class="ri-checkbox-circle-line"></i> Laporan Selesai</a>
      <a href="/admin/info_warga.php" class="sidebar-link"><i class="ri-megaphone-line"></i> Info Warga</a>
      <a href="/admin/logout.php" class="sidebar-link"><i class="ri-logout-box-line"></i> Logout</a>
    </nav>
  </aside>

  <main class="admin-content">
    <div class="admin-header">
      <h1>Laporan Masuk</h1>
      <div class="admin-user"><i class="ri-user-line"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Pelapor</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Status</th>
            <th>Tanggal Lapor</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reports)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 40px;">
              <i class="ri-inbox-line" style="font-size: 48px; color: #ccc;"></i>
              <p style="margin-top: 16px; color: #999;">Belum ada laporan masuk</p>
            </td>
          </tr>
          <?php else: ?>
            <?php foreach ($reports as $report): ?>
            <tr>
              <td><?php echo $report['id']; ?></td>
              <td><?php echo htmlspecialchars($report['nama_pelapor']); ?></td>
              <td><?php echo htmlspecialchars($report['kategori']); ?></td>
              <td><?php echo htmlspecialchars($report['lokasi']); ?></td>
              <td>
                <span class="badge badge-<?php echo $report['status']; ?>">
                  <?php echo ucfirst($report['status']); ?>
                </span>
              </td>
              <td><?php echo date('d/m/Y H:i', strtotime($report['tanggal_lapor'])); ?></td>
              <td>
                <a href="/admin/update_laporan.php?id=<?php echo $report['id']; ?>" class="btn btn-sm btn-primary">
                  <i class="ri-edit-line"></i> Kelola
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>