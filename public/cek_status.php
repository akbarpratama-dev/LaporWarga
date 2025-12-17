<?php
require_once '../config/database.php';

$laporan = null;
$error = null;

if(isset($_GET['kode'])) {
    $kode = trim($_GET['kode']);
    if(!empty($kode)) {
        $database = new Database();
        $conn = $database->getConnection();
        
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

            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-body">
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
