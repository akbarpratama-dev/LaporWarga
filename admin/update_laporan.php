<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
		header("Location: login.php");
		exit();
}
require_once '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0) {
		header("Location: laporan_masuk.php");
		exit();
}

$database = new Database();
$conn = $database->getConnection();

$stmt = $conn->prepare("SELECT * FROM laporan WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$laporan = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$laporan) {
		header("Location: laporan_masuk.php");
		exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Update Laporan - Admin</title>
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
			<h1>Update Laporan</h1>
			<div class="admin-user"><i class="ri-user-line"></i> <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></div>
		</div>

		<div class="card">
			<div class="card-header">
				<h3>Detail Laporan: <?php echo htmlspecialchars($laporan['kode_laporan']); ?></h3>
			</div>
			<div class="card-body">
				<?php if(isset($_GET['error'])): ?>
					<div class="alert alert-error">
						<?php if($_GET['error']==='update'){echo '<i class="ri-error-warning-line"></i> Gagal mengupdate laporan.';}elseif($_GET['error']==='database'){echo '<i class="ri-error-warning-line"></i> DB Error: ' . htmlspecialchars($_GET['msg'] ?? '');} ?>
					</div>
				<?php endif; ?>

				<table class="detail-table">
					<tr><td>Nama Pelapor</td><td><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></td></tr>
					<tr><td>No. HP</td><td><?php echo htmlspecialchars($laporan['no_hp']); ?></td></tr>
					<tr><td>Kategori</td><td><?php echo htmlspecialchars($laporan['kategori']); ?></td></tr>
					<tr><td>Lokasi</td><td><?php echo htmlspecialchars($laporan['lokasi']); ?></td></tr>
					<tr><td>Status Saat Ini</td><td><span class="badge badge-<?php echo $laporan['status']==='Diterima'?'info':($laporan['status']==='Diproses'?'warning':'success'); ?>"><?php echo htmlspecialchars($laporan['status']); ?></span></td></tr>
					<tr><td>Tanggal Lapor</td><td><?php echo date('d M Y', strtotime($laporan['tanggal_lapor'])); ?></td></tr>
				</table>

				<?php if(!empty($laporan['foto_blob'])): ?>
					<div style="margin-top:1rem;">
						<strong>Foto Bukti:</strong><br>
						<img src="../public/image.php?id=<?php echo $laporan['id']; ?>&type=foto" class="detail-img" alt="Foto Laporan">
					</div>
				<?php endif; ?>

				<form action="../process/update_status_process.php" method="POST" enctype="multipart/form-data" style="margin-top: 2rem;">
					<input type="hidden" name="id" value="<?php echo $laporan['id']; ?>">

					<div class="form-group">
						<label for="status">Update Status *</label>
						<select id="status" name="status" required>
							<option value="Diterima" <?php echo $laporan['status']==='Diterima'?'selected':''; ?>>Diterima</option>
							<option value="Diproses" <?php echo $laporan['status']==='Diproses'?'selected':''; ?>>Diproses</option>
							<option value="Selesai" <?php echo $laporan['status']==='Selesai'?'selected':''; ?>>Selesai</option>
						</select>
					</div>

					<div class="form-group" id="keteranganGroup">
						<label for="keterangan">Keterangan Penyelesaian</label>
						<textarea id="keterangan" name="keterangan" rows="4"><?php echo htmlspecialchars($laporan['catatan_admin'] ?? ''); ?></textarea>
					</div>

					<div class="form-group" id="biayaGroup">
						<label for="biaya">Biaya Perbaikan (Rp)</label>
						<input type="number" id="biaya" name="biaya" min="0" step="1000" value="<?php echo $laporan['biaya'] ?? ''; ?>" placeholder="Contoh: 500000">
					</div>

					<div class="form-group" id="durasiGroup">
						<label for="durasi">Durasi Pengerjaan</label>
						<input type="text" id="durasi" name="durasi" value="<?php echo htmlspecialchars($laporan['durasi'] ?? ''); ?>" placeholder="Contoh: 3 hari">
					</div>

					<div class="form-group" id="fotoAfterGroup">
						<label for="foto_after">Upload Foto Setelah Perbaikan</label>
						<input type="file" id="foto_after" name="foto_after" accept="image/*">
						<?php if(!empty($laporan['foto_after_blob'])): ?>
							<p style="margin-top:0.5rem;">Foto saat ini: <a href="../public/image.php?id=<?php echo $laporan['id']; ?>&type=foto_after" target="_blank">Lihat</a></p>
						<?php endif; ?>
					</div>

					<div style="display:flex; gap:1rem; margin-top:2rem;">
						<button type="submit" class="btn btn-primary">Simpan Update</button>
						<a href="laporan_masuk.php" class="btn btn-outline">Kembali</a>
					</div>
				</form>
			</div>
		</div>
	</main>

	<script>
		const statusSelect = document.getElementById('status');
		const keteranganGroup = document.getElementById('keteranganGroup');
		const biayaGroup = document.getElementById('biayaGroup');
		const durasiGroup = document.getElementById('durasiGroup');
		const fotoAfterGroup = document.getElementById('fotoAfterGroup');

		function toggleFields() {
			const status = statusSelect.value;
			const required = (status === 'Selesai');
			keteranganGroup.style.display = required ? 'block' : 'none';
			biayaGroup.style.display = required ? 'block' : 'none';
			durasiGroup.style.display = required ? 'block' : 'none';
			fotoAfterGroup.style.display = required ? 'block' : 'none';
			document.getElementById('keterangan').required = required;
		}

		statusSelect.addEventListener('change', toggleFields);
		toggleFields();
	</script>
</body>
</html>
