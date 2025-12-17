<?php
session_start();
if(!isset($_SESSION['admin_id'])) { header('Location: login.php?error=unauthorized'); exit(); }
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT id, kode_laporan, nama_pelapor, kategori, lokasi, status, tanggal_lapor FROM laporan WHERE status IN ('Diterima','Diproses') ORDER BY tanggal_lapor DESC");
$stmt->execute();
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Laporan Masuk - Admin</title>
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
			<a href="laporan_masuk.php" class="sidebar-link active"><i class="ri-inbox-archive-line"></i> Laporan Masuk</a>
			<a href="laporan_selesai.php" class="sidebar-link"><i class="ri-checkbox-circle-line"></i> Laporan Selesai</a>
			<a href="info_warga.php" class="sidebar-link"><i class="ri-megaphone-line"></i> Info Warga</a>
			<a href="logout.php" class="sidebar-link"><i class="ri-logout-box-line"></i> Logout</a>
		</nav>
	</aside>

	<main class="admin-content">
		<div class="admin-header">
			<h1>Laporan Masuk</h1>
			<div class="admin-user"><i class="ri-user-line"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
		</div>

		<div class="card">
			<div class="card-header"><h3>Daftar Laporan</h3></div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Lokasi</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
						</thead>
						<tbody>
							<?php foreach($rows as $r): ?>
							<tr>
								<td><?php echo htmlspecialchars($r['kode_laporan']); ?></td>
								<td><?php echo htmlspecialchars($r['nama_pelapor']); ?></td>
								<td><?php echo htmlspecialchars($r['kategori']); ?></td>
								<td><?php echo htmlspecialchars($r['lokasi']); ?></td>
								<td><span class="badge badge-<?php echo $r['status']==='Diterima'?'info':'warning'; ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
								<td><?php echo date('d M Y', strtotime($r['tanggal_lapor'])); ?></td>
								<td><a class="btn btn-sm btn-primary" href="update_laporan.php?id=<?php echo $r['id']; ?>">Update</a></td>
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
