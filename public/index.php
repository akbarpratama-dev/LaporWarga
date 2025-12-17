<?php
require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Get info warga for carousel
$query = "SELECT * FROM info_warga ORDER BY created_at DESC LIMIT 6";
$stmt = $conn->prepare($query);
$stmt->execute();
$info_warga = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get public reports
$query = "SELECT id, kode_laporan, kategori, lokasi, status, tanggal_lapor FROM laporan WHERE status IN ('Diterima', 'Diproses') ORDER BY tanggal_lapor DESC LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();
$laporan_publik = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get completed reports
$query = "SELECT * FROM laporan WHERE status = 'Selesai' ORDER BY tanggal_selesai DESC LIMIT 6";
$stmt = $conn->prepare($query);
$stmt->execute();
$laporan_selesai = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaporWarga - Sistem Pelaporan Warga</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.2">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <img src="assets/images/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'">
                <span class="brand-text">LaporWarga</span>
            </div>
            <button class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="#beranda" class="nav-link active">Beranda</a></li>
                <li><a href="#info-warga" class="nav-link">Info Warga</a></li>
                <li><a href="#laporan-publik" class="nav-link">Laporan Publik</a></li>
                <li><a href="#laporan-selesai" class="nav-link">Laporan Terselesaikan</a></li>
                <li><a href="cek_status.php" class="nav-link">Cek Status</a></li>
                <li><a href="#lapor" class="btn btn-primary">Laporkan Masalah</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
        <div class="container hero-grid">
            <div class="hero-left">
                <h1 class="hero-title fade-in-up">LaporWarga</h1>
                <p class="hero-description fade-in-up" style="animation-delay: 0.2s;">
                    Laporkan permasalahan lingkungan dan pantau tindak lanjutnya secara transparan.
                </p>
                <div class="hero-actions fade-in-up" style="animation-delay: 0.4s;">
                    <a href="#lapor" class="btn btn-secondary btn-large">Laporkan Masalah</a>
                    <a href="cek_status.php" class="btn btn-outline btn-large"><i class="ri-search-line"></i> Cek Status Laporan</a>
                </div>
            </div>
            <div class="hero-right">
                <img src="assets/images/asset 1.png" alt="Ilustrasi lingkungan" class="hero-image" onerror="this.style.display='none'">
            </div>
        </div>
    </section>

    <!-- Proses Laporan (Flow) -->
    <section id="flow-laporan" class="process-flow">
        <div class="container">
            <div class="process-grid">
                <div class="process-item">
                    <div class="process-icon"><i class="ri-edit-2-line"></i></div>
                    <h4 class="process-title">Tulis Laporan</h4>
                    <p class="process-desc">Laporkan keluhan atau aspirasi Anda dengan jelas dan lengkap</p>
                </div>
                <div class="process-item">
                    <div class="process-icon"><i class="ri-shield-check-line"></i></div>
                    <h4 class="process-title">Proses Verifikasi</h4>
                    <p class="process-desc">Dalam 3 hari, laporan akan diverifikasi dan diteruskan</p>
                </div>
                <div class="process-item">
                    <div class="process-icon"><i class="ri-chat-settings-line"></i></div>
                    <h4 class="process-title">Proses Tindak Lanjut</h4>
                    <p class="process-desc">Dalam 5 hari, instansi menindaklanjuti dan membalas laporan</p>
                </div>
                <div class="process-item">
                    <div class="process-icon"><i class="ri-message-2-line"></i></div>
                    <h4 class="process-title">Beri Tanggapan</h4>
                    <p class="process-desc">Anda dapat menanggapi balasan instansi dalam 10 hari</p>
                </div>
                <div class="process-item">
                    <div class="process-icon"><i class="ri-check-double-line"></i></div>
                    <h4 class="process-title">Selesai</h4>
                    <p class="process-desc">Laporan ditindaklanjuti hingga tuntas</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Warga Section -->
    <section id="info-warga" class="section">
        <div class="container">
            <div class="section-header scroll-animate">
                <h2 class="section-title">Informasi Kegiatan Warga</h2>
                <p class="section-subtitle">Kegiatan terkini di lingkungan kita</p>
            </div>
            
            <div class="carousel-container scroll-animate" style="animation-delay: 0.2s;">
                <button class="carousel-btn carousel-prev" id="carouselPrev">&lt;</button>
                <div class="carousel-wrapper">
                    <div class="carousel" id="infoCarousel">
                        <?php foreach($info_warga as $info): ?>
                        <div class="carousel-card">
                            <div class="card">
                                <div class="info-badge <?php echo ($info['kategori'] ?? 'Info') === 'Pemberitahuan' ? 'info-badge--alert' : 'info-badge--info'; ?>">
                                    <?php echo htmlspecialchars($info['kategori'] ?? 'Info'); ?>
                                </div>
                                <div class="card-header">
                                    <h3><?php echo htmlspecialchars($info['judul']); ?></h3>
                                </div>
                                <div class="card-body">
                                    <p class="info-lokasi"><i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($info['lokasi']); ?></p>
                                    <p class="info-deskripsi"><?php echo htmlspecialchars($info['deskripsi']); ?></p>
                                    <?php if(strlen($info['deskripsi']) > 50): ?>
                                    <a href="detail_info.php?id=<?php echo $info['id']; ?>" class="info-detail-link">Lihat Selengkapnya</a>
                                    <?php endif; ?>
                                    <div class="info-tanggal">
                                        <span><i class="ri-calendar-line"></i> <?php echo date('d M Y', strtotime($info['tanggal_mulai'])); ?>
                                       </span>
                                        <?php if($info['tanggal_selesai']): ?>
                                        <span><i class="ri-arrow-right-line"></i> <?php echo date('d M Y', strtotime($info['tanggal_selesai'])); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="carousel-btn carousel-next" id="carouselNext">&gt;</button>
            </div>
        </div>
    </section>

    <!-- Form Laporan Section -->
    <section id="lapor" class="section section-alt">
        <div class="container">
            <div class="section-header scroll-animate">
                <h2 class="section-title">Buat Laporan</h2>
                <p class="section-subtitle">Laporkan masalah di lingkungan Anda</p>
            </div>

            <div class="form-container">
                <?php if(isset($_GET['success']) && isset($_GET['kode'])): ?>
                <div class="alert alert-success" id="successAlert" style="margin-bottom: 2rem; padding: 1.5rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 0.5rem; text-align: center;">
                    <h3 style="margin: 0 0 1rem 0; color: #155724;"><i class="ri-checkbox-circle-line"></i> Laporan Berhasil Dikirim!</h3>
                    <p style="margin: 0 0 0.5rem 0; font-size: 1rem; color: #155724;">Kode laporan Anda:</p>
                    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin: 1rem 0;">
                        <input type="text" id="kodeLaporan" value="<?php echo htmlspecialchars($_GET['kode']); ?>" readonly style="font-size: 1.25rem; font-weight: bold; padding: 0.75rem 1rem; text-align: center; border: 2px solid #28a745; border-radius: 0.375rem; background: white; color: #155724; width: 250px;">
                        <button onclick="copyKode()" class="btn btn-success" style="padding: 0.75rem 1.5rem;"><i class="ri-file-copy-line"></i> Salin</button>
                    </div>
                    <p style="margin: 1rem 0 0 0; font-size: 0.9rem; color: #155724;">
                        <i class="ri-information-line"></i> Simpan kode ini untuk cek status laporan Anda. 
                        <span id="countdown" style="font-weight: bold;">Halaman akan refresh dalam <span id="timer">20</span> detik</span>
                    </p>
                </div>
                <?php elseif(isset($_GET['error'])): ?>
                <div class="alert alert-error" style="margin-bottom: 2rem;">
                    <i class="ri-error-warning-line"></i> 
                    <?php 
                    if($_GET['error'] === 'submit') echo 'Gagal mengirim laporan. Silakan coba lagi.';
                    elseif($_GET['error'] === 'database') echo 'Error database: ' . htmlspecialchars($_GET['msg'] ?? '');
                    else echo 'Terjadi kesalahan. Silakan coba lagi.';
                    ?>
                </div>
                <?php endif; ?>

                <form id="formLaporan" action="../process/submit_laporan.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_pelapor">Nama Lengkap *</label>
                        <input type="text" id="nama_pelapor" name="nama_pelapor" required>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">Nomor HP/WhatsApp *</label>
                        <input type="tel" id="no_hp" name="no_hp" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori Masalah *</label>
                        <select id="kategori" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Jalan Rusak">Jalan Rusak</option>
                            <option value="Lampu Jalan">Lampu Jalan Mati</option>
                            <option value="Saluran Air">Saluran Air Tersumbat</option>
                            <option value="Sampah">Pengelolaan Sampah</option>
                            <option value="Pohon">Pohon Tumbang</option>
                            <option value="Fasilitas Umum">Fasilitas Umum Rusak</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lokasi">Lokasi Kejadian *</label>
                        <input type="text" id="lokasi" name="lokasi" placeholder="Jl. Contoh RT 01 RW 02" required>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Masalah *</label>
                        <textarea id="deskripsi" name="deskripsi" rows="5" required placeholder="Jelaskan masalah secara detail..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="foto">Upload Foto (Opsional)</label>
                        <input type="file" id="foto" name="foto" accept="image/*">
                        <small class="form-text">Format: JPG, PNG, JPEG. Max 2MB</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Kirim Laporan</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Laporan Publik Section -->
    <section id="laporan-publik" class="section">
        <div class="container">
            <div class="section-header scroll-animate">
                <h2 class="section-title">Laporan Publik</h2>
                <p class="section-subtitle">Daftar laporan dari warga</p>
            </div>

            <div class="table-responsive scroll-animate" style="animation-delay: 0.2s;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($laporan_publik as $laporan): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($laporan['kode_laporan']); ?></td>
                            <td><?php echo htmlspecialchars($laporan['kategori']); ?></td>
                            <td><?php echo htmlspecialchars($laporan['lokasi']); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $laporan['status'] == 'Diterima' ? 'info' : 
                                        ($laporan['status'] == 'Diproses' ? 'warning' : 'success'); 
                                ?>">
                                    <?php echo $laporan['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($laporan['tanggal_lapor'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Laporan Selesai Section -->
    <section id="laporan-selesai" class="section section-alt">
        <div class="container">
            <div class="section-header scroll-animate">
                <h2 class="section-title">Laporan Terselesaikan</h2>
                <p class="section-subtitle">Bukti penanganan masalah warga</p>
            </div>

            <div class="grid">
                <?php foreach($laporan_selesai as $selesai): ?>
                <div class="card">
                    <?php if($selesai['foto_after_blob']): ?>
                    <img src="image.php?id=<?php echo $selesai['id']; ?>&type=foto_after" 
                         alt="Foto" class="card-img">
                    <?php endif; ?>
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($selesai['kategori']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($selesai['lokasi']); ?></p>
                        <div class="card-meta">
                            <span><i class="ri-checkbox-circle-line"></i> Selesai: <?php echo date('d M Y', strtotime($selesai['tanggal_selesai'])); ?></span>
                            <?php if($selesai['biaya']): ?>
                            <span><i class="ri-money-dollar-circle-line"></i> Biaya: Rp <?php echo number_format($selesai['biaya'], 0, ',', '.'); ?></span>
                            <?php endif; ?>
                            <?php if($selesai['durasi']): ?>
                            <span><i class="ri-timer-line"></i> Durasi: <?php echo htmlspecialchars($selesai['durasi']); ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="detail_laporan.php?kode=<?php echo urlencode($selesai['kode_laporan']); ?>" class="btn btn-sm btn-outline">Lihat Detail</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>LaporWarga</h3>
                    <p>Platform pelaporan masalah lingkungan yang memudahkan warga untuk berkontribusi membangun lingkungan lebih baik.</p>
                </div>
                <div class="footer-col">
                    <h4>Navigasi</h4>
                    <ul class="footer-links">
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#info-warga">Info Warga</a></li>
                        <li><a href="#laporan-publik">Laporan Publik</a></li>
                        <li><a href="cek_status.php">Cek Status</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Kontak</h4>
                    <ul class="footer-contact">
                        <li><i class="ri-phone-line"></i> (021) 1234-5678</li>
                        <li><i class="ri-mail-line"></i> admin@laporwarga.id</li>
                        <li><i class="ri-map-pin-line"></i> Kelurahan Setempat</li>
                        <li><i class="ri-time-line"></i> Senin - Jumat: 08:00 - 16:00</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 LaporWarga. Tugas Akhir Semester 3 Informatika.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
    function copyKode() {
        const kodeInput = document.getElementById('kodeLaporan');
        kodeInput.select();
        kodeInput.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="ri-check-line"></i> Tersalin!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    }

    // Auto redirect after 20 seconds
    <?php if(isset($_GET['success']) && isset($_GET['kode'])): ?>
    let timeLeft = 20;
    const timerElement = document.getElementById('timer');
    const countdown = setInterval(() => {
        timeLeft--;
        timerElement.textContent = timeLeft;
        if (timeLeft <= 0) {
            clearInterval(countdown);
            window.location.href = 'index.php';
        }
    }, 1000);
    <?php endif; ?>
    </script>
</body>
</html>
