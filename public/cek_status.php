<?php
require_once '../config/database.php';
require_once '../config/report_tracker.php';

$database = new Database();
$conn = $database->getConnection();

// Cookie-based tracked reports
$trackedReports = [];
$hasTrackedReports = ReportTracker::hasTrackedReports();

if ($hasTrackedReports) {
    $trackedReports = ReportTracker::fetchTrackedReportsFromDB($conn);
}

// Manual search result
$laporan = null;
$error = null;

if(isset($_GET['kode'])) {
    $kode = trim($_GET['kode']);
    if(!empty($kode)) {
        $stmt = $conn->prepare("SELECT * FROM laporan WHERE kode_laporan = :kode");
        $stmt->bindParam(':kode', $kode);
        $stmt->execute();
        $laporan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$laporan) {
            $error = "Laporan dengan kode tersebut tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Laporan - LaporWarga</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.2">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <img src="assets/images/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
                <span class="brand-text">LaporWarga</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link"><i class="ri-home-line"></i> Beranda</a></li>
            </ul>
        </div>
    </nav>

    <main class="section" style="flex: 1;">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Cek Status Laporan</h2>
                <p class="section-subtitle">Pantau perkembangan laporan Anda</p>
            </div>

            <?php if ($hasTrackedReports && !empty($trackedReports)): ?>
            <!-- Tracked Reports Section -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3><i class="ri-file-list-3-line"></i> Laporan Anda</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">
                        Ditemukan <?php echo count($trackedReports); ?> laporan dari browser ini
                    </p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kode Laporan</th>
                                    <th>Nama</th>
                                    <th>Kategori</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trackedReports as $report): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($report['kode_laporan']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($report['nama_pelapor']); ?></td>
                                    <td><?php echo htmlspecialchars($report['kategori']); ?></td>
                                    <td><?php echo htmlspecialchars($report['lokasi']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $report['status'] === 'Diterima' ? 'info' : 
                                                ($report['status'] === 'Diproses' ? 'warning' : 'success'); 
                                        ?>">
                                            <?php echo htmlspecialchars($report['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($report['tanggal_lapor'])); ?></td>
                                    <td>
                                        <a href="detail_laporan.php?id=<?php echo $report['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="ri-eye-line"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0; text-align: center;">
                        <p style="color: #666; margin-bottom: 1rem;">
                            <i class="ri-information-line"></i> 
                            Ingin cek laporan lain atau laporan dari perangkat lain?
                        </p>
                        <button onclick="document.getElementById('manualSearchForm').style.display='block'; this.style.display='none';" 
                                class="btn btn-outline">
                            <i class="ri-search-line"></i> Cek Laporan Secara Manual
                        </button>
                    </div>
                </div>
            </div>

            <!-- Manual Search Form (Hidden by default if tracked reports exist) -->
            <div class="card" id="manualSearchForm" style="display: <?php echo $hasTrackedReports ? 'none' : 'block'; ?>;">
            <?php else: ?>
            <!-- Manual Search Form (Shown by default if no tracked reports) -->
            <div class="card">
            <?php endif; ?>
                <div class="card-body">
                    <h3 style="margin-bottom: 1rem;">
                        <i class="ri-search-line"></i> Cari Laporan Manual
                    </h3>
                    <form action="" method="GET" class="search-form">
                        <div class="form-group">
                            <label for="kode">Kode Laporan</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="text" id="kode" name="kode" placeholder="Contoh: LAP-12345678" value="<?php echo isset($_GET['kode']) ? htmlspecialchars($_GET['kode']) : ''; ?>" required style="flex: 1;">
                                <button type="submit" class="btn btn-primary"><i class="ri-search-line"></i> Cek</button>
                            </div>
                        </div>
                    </form>

                    <?php if($error): ?>
                        <div class="alert alert-error" style="margin-top: 1rem;">
                            <i class="ri-error-warning-line"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if($laporan): ?>
                        <div class="result-box" style="margin-top: 2rem; border-top: 1px solid #eee; padding-top: 1rem;">
                            <h3><i class="ri-file-list-line"></i> Detail Laporan</h3>
                            <table class="detail-table">
                                <tr>
                                    <td>Status</td>
                                    <td>
                                        <span class="badge badge-<?php echo $laporan['status']==='Diterima'?'info':($laporan['status']==='Diproses'?'warning':'success'); ?>">
                                            <?php echo htmlspecialchars($laporan['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr><td>Nama Pelapor</td><td><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></td></tr>
                                <tr><td>Kategori</td><td><?php echo htmlspecialchars($laporan['kategori']); ?></td></tr>
                                <tr><td>Tanggal</td><td><?php echo date('d M Y', strtotime($laporan['tanggal_lapor'])); ?></td></tr>
                            </table>
                            <div style="margin-top: 1rem; text-align: center;">
                                <a href="detail_laporan.php?kode=<?php echo urlencode($laporan['kode_laporan']); ?>" class="btn btn-outline"><i class="ri-eye-line"></i> Lihat Detail Lengkap</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 LaporWarga.</p>
            </div>
        </div>
    </footer>
</body>
</html>
