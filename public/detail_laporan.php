<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

$kode = isset($_GET['kode']) ? $_GET['kode'] : null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if($kode){
    $stmt = $conn->prepare("SELECT * FROM laporan WHERE kode_laporan = :kode");
    $stmt->bindParam(':kode', $kode);
} elseif($id) {
    $stmt = $conn->prepare("SELECT * FROM laporan WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
} else {
    header("Location: index.php");
    exit();
}

$stmt->execute();
$laporan = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$laporan){
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Laporan <?php echo htmlspecialchars($laporan['kode_laporan']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=2.2">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <nav class="navbar">
    <div class="container">
       <div class="nav-brand">
                <img src="assets/images/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
                <span class="brand-text">LaporWarga</span>
            </div>
      <div class="navbar-menu"><a href="index.php" class="btn btn-outline"><i class="ri-arrow-left-line"></i> Kembali</a></div>
    </div>
  </nav>

  <main class="detail-container">
    <div class="container">
      <div class="detail-header">
        <h1>Detail Laporan</h1>
        <span class="badge badge-<?php echo $laporan['status']==='Diterima'?'info':($laporan['status']==='Diproses'?'warning':'success'); ?>"><?php echo htmlspecialchars($laporan['status']); ?></span>
      </div>

      <div class="detail-grid">
        <div>
          <div class="detail-section">
            <h3>Informasi Laporan</h3>
            <table class="detail-table">
              <tr><td>Kode Laporan</td><td><?php echo htmlspecialchars($laporan['kode_laporan']); ?></td></tr>
              <tr><td>Nama Pelapor</td><td><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></td></tr>
              <tr><td>No. HP</td><td><?php echo htmlspecialchars($laporan['no_hp']); ?></td></tr>
              <tr><td>Kategori</td><td><?php echo htmlspecialchars($laporan['kategori']); ?></td></tr>
              <tr><td>Lokasi</td><td><?php echo htmlspecialchars($laporan['lokasi']); ?></td></tr>
              <tr><td>Tanggal Lapor</td><td><?php echo date('d M Y', strtotime($laporan['tanggal_lapor'])); ?></td></tr>
            </table>
          </div>

          <div class="detail-section">
            <h3>Deskripsi</h3>
            <p><?php echo nl2br(htmlspecialchars($laporan['deskripsi'])); ?></p>
            <?php if(!empty($laporan['foto_blob'])): ?>
              <h4 style="margin-top:1.5rem;">Foto Bukti</h4>
              <img src="image.php?id=<?php echo $laporan['id']; ?>&type=foto" alt="Foto Laporan" class="detail-img">
            <?php endif; ?>
          </div>

          <?php if($laporan['status']==='Selesai' && (!empty($laporan['catatan_admin']) || !empty($laporan['foto_after']))): ?>
          <div class="detail-section">
            <h3>Hasil Penyelesaian</h3>
            <?php if(!empty($laporan['catatan_admin'])): ?>
              <p><?php echo nl2br(htmlspecialchars($laporan['catatan_admin'])); ?></p>
            <?php endif; ?>

            <?php if(!empty($laporan['biaya']) || !empty($laporan['durasi'])): ?>
              <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-top:1rem;padding:1rem;background:#f8fafc;border-radius:0.5rem;">
                <?php if(!empty($laporan['biaya'])): ?>
                  <div>
                    <p style="color:var(--text-muted);font-size:0.875rem;margin-bottom:0.25rem;">Biaya Perbaikan</p>
                    <p style="font-size:1.125rem;font-weight:600;color:var(--primary-color);">Rp <?php echo number_format($laporan['biaya'],0,',','.'); ?></p>
                  </div>
                <?php endif; ?>
                <?php if(!empty($laporan['durasi'])): ?>
                  <div>
                    <p style="color:var(--text-muted);font-size:0.875rem;margin-bottom:0.25rem;">Durasi Pengerjaan</p>
                    <p style="font-size:1.125rem;font-weight:600;color:var(--primary-color);"><?php echo htmlspecialchars($laporan['durasi']); ?></p>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if(!empty($laporan['foto_after_blob'])): ?>
              <h4 style="margin-top:1.5rem;">Foto Setelah Perbaikan</h4>
              <img src="image.php?id=<?php echo $laporan['id']; ?>&type=foto_after" alt="Foto Setelah" class="detail-img">
            <?php endif; ?>

            <?php if(!empty($laporan['tanggal_selesai'])): ?>
            <p style="margin-top:1rem;color:var(--text-muted);"><strong>Tanggal Selesai:</strong> <?php echo date('d M Y', strtotime($laporan['tanggal_selesai'])); ?></p>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div>

        <aside>
          <div class="detail-section">
            <h3>Status Proses</h3>
            <div class="timeline">
              <div class="timeline-item <?php echo in_array($laporan['status'],['Diterima','Diproses','Selesai'])?'active':''; ?>">
                <div class="timeline-dot"></div>
                <div class="timeline-content"><strong>Diterima</strong><small>Tiket dibuat</small></div>
              </div>
              <div class="timeline-item <?php echo in_array($laporan['status'],['Diproses','Selesai'])?'active':''; ?>">
                <div class="timeline-dot"></div>
                <div class="timeline-content"><strong>Diproses</strong><small>Dalam pengerjaan</small></div>
              </div>
              <div class="timeline-item <?php echo $laporan['status']==='Selesai'?'active':''; ?>">
                <div class="timeline-dot"></div>
                <div class="timeline-content"><strong>Selesai</strong><small>Pekerjaan selesai</small></div>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer-bottom">
        <p>&copy; 2025 LaporWarga. Tugas Akhir Semester 3 Informatika.</p>
      </div>
    </div>
  </footer>
</body>
</html>