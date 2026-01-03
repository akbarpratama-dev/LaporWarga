<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM info_warga WHERE id = :id");
    $stmt->bindParam(':id', $_GET['delete']);
    $stmt->execute();
    header("Location: info_warga.php?deleted=1");
    exit();
}

// Get all info
$stmt = $conn->query("SELECT * FROM info_warga ORDER BY tanggal_mulai DESC");
$infos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Info Warga - Admin</title>
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

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
      <i class="ri-checkbox-circle-line"></i> Info berhasil ditambahkan!
    </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">
      <i class="ri-checkbox-circle-line"></i> Info berhasil dihapus!
    </div>
    <?php endif; ?>

    <!-- Form Tambah Info -->
    <div class="card">
      <div class="card-header">
        <h3><i class="ri-add-circle-line"></i> Tambah Info Baru</h3>
      </div>
      <div class="card-body">
        <form action="/process/upload_info_process.php" method="POST">
          <div class="form-row">
            <div class="form-group">
              <label for="kategori">Kategori *</label>
              <select id="kategori" name="kategori" required class="form-control">
                <option value="Info">Info</option>
                <option value="Pemberitahuan">Pemberitahuan</option>
              </select>
            </div>

            <div class="form-group">
              <label for="judul">Judul *</label>
              <input type="text" id="judul" name="judul" required class="form-control" placeholder="Judul informasi">
            </div>
          </div>

          <div class="form-group">
            <label for="lokasi">Lokasi *</label>
            <input type="text" id="lokasi" name="lokasi" required class="form-control" placeholder="Lokasi kegiatan/info">
          </div>

          <div class="form-group">
            <label for="deskripsi">Deskripsi *</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" required class="form-control" placeholder="Deskripsi lengkap informasi"></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="tanggal_mulai">Tanggal Mulai *</label>
              <input type="date" id="tanggal_mulai" name="tanggal_mulai" required class="form-control">
            </div>

            <div class="form-group">
              <label for="tanggal_selesai">Tanggal Selesai (Opsional)</label>
              <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control">
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i> Tambah Info
          </button>
        </form>
      </div>
    </div>

    <!-- Daftar Info -->
    <div class="card" style="margin-top: 24px;">
      <div class="card-header">
        <h3><i class="ri-list-check"></i> Daftar Info Warga</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($infos)): ?>
              <tr>
                <td colspan="6" style="text-align: center; padding: 60px;">
                  <i class="ri-megaphone-line" style="font-size: 64px; color: #e5e7eb; display: block; margin-bottom: 16px;"></i>
                  <p style="color: #9ca3af; font-size: 16px;">Belum ada info warga</p>
                </td>
              </tr>
              <?php else: ?>
                <?php foreach ($infos as $info): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($info['judul']); ?></strong></td>
                  <td>
                    <span class="badge badge-<?php echo $info['kategori'] === 'Pemberitahuan' ? 'warning' : 'info'; ?>">
                      <?php echo htmlspecialchars($info['kategori']); ?>
                    </span>
                  </td>
                  <td><?php echo htmlspecialchars($info['lokasi']); ?></td>
                  <td><?php echo date('d M Y', strtotime($info['tanggal_mulai'])); ?></td>
                  <td><?php echo $info['tanggal_selesai'] ? date('d M Y', strtotime($info['tanggal_selesai'])) : '-'; ?></td>
                  <td>
                    <a href="?delete=<?php echo $info['id']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Yakin ingin menghapus info ini?')">
                      <i class="ri-delete-bin-line"></i> Hapus
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