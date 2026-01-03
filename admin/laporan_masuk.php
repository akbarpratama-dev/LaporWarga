<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$stmt = $conn->prepare("
    SELECT * FROM laporan 
    WHERE status IN ('diterima', 'diproses') 
    ORDER BY tanggal_lapor DESC
");
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Masuk - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="/public/assets/css/style.css?v=2.1">
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

    <div class="card">
      <div class="card-header">
        <h3><i class="ri-inbox-archive-line"></i> Daftar Laporan Masuk</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Kode</th>
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
                <td colspan="7" style="text-align: center; padding: 60px;">
                  <i class="ri-inbox-line" style="font-size: 64px; color: #e5e7eb; display: block; margin-bottom: 16px;"></i>
                  <p style="color: #9ca3af; font-size: 16px;">Belum ada laporan masuk</p>
                </td>
              </tr>
              <?php else: ?>
                <?php foreach ($reports as $report): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($report['kode_laporan']); ?></strong></td>
                  <td><?php echo htmlspecialchars($report['nama_pelapor']); ?></td>
                  <td><span class="badge badge-secondary"><?php echo htmlspecialchars($report['kategori']); ?></span></td>
                  <td><?php echo htmlspecialchars(substr($report['lokasi'], 0, 50)); ?><?php echo strlen($report['lokasi']) > 50 ? '...' : ''; ?></td>
                  <td>
                    <span class="badge badge-<?php echo $report['status'] === 'diterima' ? 'info' : 'warning'; ?>">
                      <?php echo ucfirst($report['status']); ?>
                    </span>
                  </td>
                  <td><?php echo date('d M Y, H:i', strtotime($report['tanggal_lapor'])); ?></td>
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
      </div>
    </div>
  </main>
</body>
</html>