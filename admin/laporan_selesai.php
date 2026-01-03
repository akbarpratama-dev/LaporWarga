<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
		header("Location: login.php");
		exit();
}
require_once '../config/database.php';
$database = new Database();
$conn = $database->getConnection();
$stmt = $conn->prepare("SELECT * FROM laporan WHERE status='Selesai' ORDER BY tanggal_selesai DESC");
$stmt->execute();
$laporan_selesai = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Laporan Selesai - Admin</title>
	<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" rel="stylesheet">
	<link rel="stylesheet" href="../public/assets/css/style.css?v=2.1">
</head>
<body class="admin-page">
	<aside class="admin-sidebar">
		<div class="sidebar-header">
			<h2>LaporWarga</h2>
			<p>Panel Admin</p>
		</div>
		<nav class="sidebar-nav">
			<a href="dashboard.php" class="sidebar-link"><i class="ri-dashboard-line"></i> Dashboard</a>
			<a href="laporan_masuk.php" class="sidebar-link"><i class="ri-inbox-archive-line"></i> Laporan Masuk</a>
			<a href="laporan_selesai.php" class="sidebar-link active"><i class="ri-checkbox-circle-line"></i> Laporan Selesai</a>
			<a href="info_warga.php" class="sidebar-link"><i class="ri-megaphone-line"></i> Info Warga</a>
			<a href="logout.php" class="sidebar-link"><i class="ri-logout-box-line"></i> Logout</a>
		</nav>
	</aside>

	<main class="admin-content">
		<div class="admin-header">
			<h1>Laporan Terselesaikan</h1>
			<div class="admin-user"><i class="ri-user-line"></i> <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></div>
		</div>

		<div class="card">
			<div class="card-header"><h3>Daftar Laporan Selesai</h3></div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>Kode</th>
								<th>Nama</th>
								<th>Kategori</th>
								<th>Lokasi</th>
								<th>Tgl Lapor</th>
								<th>Tgl Selesai</th>
								<th>Biaya</th>
								<th>Durasi</th>
								<th>Detail</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($laporan_selesai as $laporan): ?>
								<tr>
									<td><?php echo htmlspecialchars($laporan['kode_laporan']); ?></td>
									<td><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></td>
									<td><?php echo htmlspecialchars($laporan['kategori']); ?></td>
									<td><?php echo htmlspecialchars($laporan['lokasi']); ?></td>
									<td><?php echo date('d M Y', strtotime($laporan['tanggal_lapor'])); ?></td>
									<td><?php echo $laporan['tanggal_selesai'] ? date('d M Y', strtotime($laporan['tanggal_selesai'])) : '-'; ?></td>
									<td><?php echo $laporan['biaya'] ? 'Rp ' . number_format($laporan['biaya'], 0, ',', '.') : '-'; ?></td>
									<td><?php echo $laporan['durasi'] ? htmlspecialchars($laporan['durasi']) : '-'; ?></td>
									<td><a class="btn btn-sm btn-primary" target="_blank" href="../public/detail_laporan.php?kode=<?php echo urlencode($laporan['kode_laporan']); ?>">Lihat</a></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</main>
</body>
</html>
