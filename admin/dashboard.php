<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Total laporan
$totalLaporan = $conn->query("SELECT COUNT(*) as total FROM laporan")->fetch()['total'];

// Laporan per status
$statusQuery = $conn->query("
    SELECT 
        status, 
        COUNT(*) as jumlah 
    FROM laporan 
    GROUP BY status
");
$statusData = $statusQuery->fetchAll(PDO::FETCH_ASSOC);

// Laporan per bulan
$monthlyQuery = $conn->query("
    SELECT 
        DATE_FORMAT(MIN(tanggal_lapor), '%Y-%m') as bulan,
        COUNT(*) as jumlah 
    FROM laporan 
    GROUP BY DATE_FORMAT(tanggal_lapor, '%Y-%m')
    ORDER BY bulan DESC 
    LIMIT 6
");
$monthlyData = $monthlyQuery->fetchAll(PDO::FETCH_ASSOC);

// Laporan terbaru
$recentQuery = $conn->query("
    SELECT * FROM laporan 
    ORDER BY tanggal_lapor DESC 
    LIMIT 10
");
$recentReports = $recentQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LaporWarga</title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="nav-brand">
                <span>Admin LaporWarga</span>
            </a>
            <div class="nav-menu">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="laporan_selesai.php" class="nav-link">Laporan Selesai</a>
                <a href="info_warga.php" class="nav-link">Info Warga</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container" style="margin-top: 100px;">
        <h1>Dashboard Admin</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Laporan</h3>
                <p class="stat-number"><?php echo $totalLaporan; ?></p>
            </div>
            
            <?php foreach ($statusData as $stat): ?>
            <div class="stat-card">
                <h3><?php echo ucfirst($stat['status']); ?></h3>
                <p class="stat-number"><?php echo $stat['jumlah']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <h2 style="margin-top: 40px;">Laporan Terbaru</h2>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentReports as $report): ?>
                    <tr>
                        <td><?php echo $report['id']; ?></td>
                        <td><?php echo htmlspecialchars($report['nama_pelapor']); ?></td>
                        <td><?php echo htmlspecialchars($report['kategori']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $report['status']; ?>">
                                <?php echo ucfirst($report['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($report['tanggal_lapor'])); ?></td>
                        <td>
                            <a href="update_laporan.php?id=<?php echo $report['id']; ?>" class="btn btn-sm btn-primary">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>