<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Handle delete
if(isset($_GET['delete'])) {
    $query = "DELETE FROM info_warga WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $_GET['delete']);
    $stmt->execute();
    header("Location: info_warga.php?deleted=1");
    exit();
}

// Get all info warga
$query = "SELECT * FROM info_warga ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$info_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Warga - LaporWarga</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/style.css?v=2.2">
</head>
<body class="admin-page">
    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h2>LaporWarga</h2>
            <p>Admin Panel</p>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="sidebar-link">
                <i class="ri-dashboard-line"></i> Dashboard
            </a>
            <a href="laporan_masuk.php" class="sidebar-link">
                <i class="ri-inbox-archive-line"></i> Laporan Masuk
            </a>
            <a href="laporan_selesai.php" class="sidebar-link">
                <i class="ri-checkbox-circle-line"></i> Laporan Selesai
            </a>
            <a href="info_warga.php" class="sidebar-link active">
                <i class="ri-megaphone-line"></i> Info Warga
            </a>
            <a href="../public/index.php" class="sidebar-link" target="_blank">
                <i class="ri-global-line"></i> Lihat Website
            </a>
            <a href="logout.php" class="sidebar-link">
                <i class="ri-logout-box-line"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Admin Content -->
    <div class="admin-content">
        <div class="admin-header">
            <h1>Info Warga</h1>
            <div class="admin-user">
                <i class="ri-user-line"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
            </div>
        </div>

        <div class="admin-main">
            <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                Info berhasil ditambahkan!
            </div>
            <?php endif; ?>
            <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">
                Info berhasil dihapus!
            </div>
            <?php endif; ?>

            <!-- Add New Form -->
            <div class="card">
                <div class="card-header">
                    <h3>Tambah Info Baru</h3>
                </div>
                <div class="card-body">
                    <form action="../process/upload_info_process.php" method="POST">
                        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="kategori">Kategori *</label>
                                <select id="kategori" name="kategori" required>
                                    <option value="Info">Info</option>
                                    <option value="Pemberitahuan">Pemberitahuan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="judul">Judul *</label>
                                <input type="text" id="judul" name="judul" required>
                            </div>

                            <div class="form-group">
                                <label for="lokasi">Lokasi *</label>
                                <input type="text" id="lokasi" name="lokasi" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi *</label>
                            <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea>
                        </div>

                        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label for="tanggal_mulai">Tanggal Mulai *</label>
                                <input type="date" id="tanggal_mulai" name="tanggal_mulai" required>
                            </div>

                            <div class="form-group">
                                <label for="tanggal_selesai">Tanggal Selesai (Opsional)</label>
                                <input type="date" id="tanggal_selesai" name="tanggal_selesai">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah Info</button>
                    </form>
                </div>
            </div>

            <!-- List Info -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h3>Daftar Info Warga</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Lokasi</th>
                                    <th>Kategori</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($info_list) > 0): ?>
                                    <?php foreach($info_list as $info): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($info['judul']); ?></td>
                                        <td><?php echo htmlspecialchars($info['lokasi']); ?></td>
                                        <td>
                                            <span class="badge <?php echo ($info['kategori'] ?? 'Info') === 'Pemberitahuan' ? 'badge-warning' : 'badge-info'; ?>">
                                                <?php echo htmlspecialchars($info['kategori'] ?? 'Info'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($info['tanggal_mulai'])); ?></td>
                                        <td><?php echo $info['tanggal_selesai'] ? date('d M Y', strtotime($info['tanggal_selesai'])) : '-'; ?></td>
                                        <td>
                                            <a href="?delete=<?php echo $info['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus info ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">Belum ada info warga</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
