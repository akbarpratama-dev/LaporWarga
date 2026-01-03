<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';

$database = new Database();
$conn = $database->getConnection();

// Disable ONLY_FULL_GROUP_BY untuk session ini
$conn->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

// Total laporan per status
$total = $conn->query("SELECT COUNT(*) as total FROM laporan")->fetch()['total'];
$diterima = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE status = 'diterima'")->fetch()['total'];
$diproses = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE status = 'diproses'")->fetch()['total'];
$selesai = $conn->query("SELECT COUNT(*) as total FROM laporan WHERE status = 'selesai'")->fetch()['total'];

// Rata-rata waktu penyelesaian
$avgQuery = $conn->query("
    SELECT 
        AVG(DATEDIFF(tanggal_selesai, tanggal_lapor)) as avg_days,
        MIN(DATEDIFF(tanggal_selesai, tanggal_lapor)) as min_days,
        MAX(DATEDIFF(tanggal_selesai, tanggal_lapor)) as max_days
    FROM laporan 
    WHERE status = 'selesai' AND tanggal_selesai IS NOT NULL
")->fetch();

$avgDays = round($avgQuery['avg_days'] ?? 0);
$minDays = $avgQuery['min_days'] ?? 0;
$maxDays = $avgQuery['max_days'] ?? 0;

// Tingkat penyelesaian
$completionRate = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;
$processingRate = $total > 0 ? round((($diproses + $selesai) / $total) * 100, 1) : 0;

// Laporan per kategori
$categoryData = $conn->query("
    SELECT kategori, COUNT(*) as count 
    FROM laporan 
    GROUP BY kategori 
    ORDER BY count DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Laporan per bulan (6 bulan terakhir) - FIXED
$monthlyData = $conn->query("
    SELECT 
        DATE_FORMAT(tanggal_lapor, '%b %Y') as month_label,
        COUNT(*) as count
    FROM laporan
    WHERE tanggal_lapor >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY YEAR(tanggal_lapor), MONTH(tanggal_lapor)
    ORDER BY YEAR(tanggal_lapor) ASC, MONTH(tanggal_lapor) ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Laporan per minggu (4 minggu terakhir) - FIXED
$weeklyData = $conn->query("
    SELECT 
        CONCAT('Minggu ', (@row_number:=@row_number + 1)) as week_start,
        COUNT(*) as count
    FROM laporan
    CROSS JOIN (SELECT @row_number:=0) r
    WHERE tanggal_lapor >= DATE_SUB(NOW(), INTERVAL 4 WEEK)
    GROUP BY YEARWEEK(tanggal_lapor, 1)
    ORDER BY YEARWEEK(tanggal_lapor, 1) ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Jika data kosong, buat dummy data
if (empty($weeklyData)) {
    $weeklyData = [
        ['week_start' => 'Minggu 1', 'count' => 0],
        ['week_start' => 'Minggu 2', 'count' => 0],
        ['week_start' => 'Minggu 3', 'count' => 0],
        ['week_start' => 'Minggu 4', 'count' => 0],
    ];
}

if (empty($monthlyData)) {
    $monthlyData = [
        ['month_label' => date('M Y', strtotime('-5 months')), 'count' => 0],
        ['month_label' => date('M Y', strtotime('-4 months')), 'count' => 0],
        ['month_label' => date('M Y', strtotime('-3 months')), 'count' => 0],
        ['month_label' => date('M Y', strtotime('-2 months')), 'count' => 0],
        ['month_label' => date('M Y', strtotime('-1 month')), 'count' => 0],
        ['month_label' => date('M Y'), 'count' => 0],
    ];
}

if (empty($categoryData)) {
    $categoryData = [
        ['kategori' => 'Belum ada data', 'count' => 0],
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="/public/assets/css/style.css?v=2.1">
  <style>
    .chart-container {
      background: white;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      margin-bottom: 24px;
    }
    .chart-title {
      font-size: 18px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .chart-title i {
      color: var(--primary-color);
    }
    .bar-chart {
      display: flex;
      align-items: flex-end;
      gap: 16px;
      height: 200px;
      margin-top: 16px;
    }
    .bar-item {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
    }
    .bar {
      width: 100%;
      background: linear-gradient(to top, var(--primary-color), #3b82f6);
      border-radius: 8px 8px 0 0;
      position: relative;
      transition: transform 0.3s ease;
      min-height: 20px;
    }
    .bar:hover {
      transform: translateY(-4px);
    }
    .bar-value {
      position: absolute;
      top: -24px;
      left: 50%;
      transform: translateX(-50%);
      font-weight: 600;
      color: var(--text-dark);
      font-size: 14px;
    }
    .bar-label {
      font-size: 12px;
      color: var(--text-muted);
      text-align: center;
    }
    .metric-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-top: 16px;
    }
    .metric-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 20px;
      border-radius: 12px;
      color: white;
      position: relative;
      overflow: hidden;
    }
    .metric-card.green {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .metric-card.orange {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .metric-card.blue {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    .metric-label {
      font-size: 13px;
      opacity: 0.9;
      margin-bottom: 8px;
    }
    .metric-value {
      font-size: 32px;
      font-weight: 700;
      line-height: 1;
    }
    .metric-unit {
      font-size: 14px;
      opacity: 0.8;
      margin-left: 4px;
    }
    .metric-icon {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 48px;
      opacity: 0.2;
    }
    .donut-chart {
      display: flex;
      gap: 32px;
      align-items: center;
      margin-top: 16px;
    }
    .donut-svg {
      width: 180px;
      height: 180px;
    }
    .donut-legend {
      flex: 1;
    }
    .legend-item {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
      padding: 8px;
      border-radius: 6px;
      transition: background 0.2s;
    }
    .legend-item:hover {
      background: #f9fafb;
    }
    .legend-color {
      width: 16px;
      height: 16px;
      border-radius: 4px;
    }
    .legend-label {
      flex: 1;
      font-size: 14px;
      color: var(--text-dark);
    }
    .legend-value {
      font-weight: 600;
      color: var(--text-dark);
    }
    .progress-bar-container {
      margin-bottom: 20px;
    }
    .progress-label {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 14px;
    }
    .progress-bar {
      height: 12px;
      background: #e5e7eb;
      border-radius: 6px;
      overflow: hidden;
    }
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, var(--primary-color), #3b82f6);
      border-radius: 6px;
      transition: width 0.6s ease;
    }
    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      gap: 24px;
      margin-bottom: 24px;
    }
    @media (max-width: 1200px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="admin-page">
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <h2>LaporWarga</h2>
      <p>Panel Admin</p>
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="sidebar-link active"><i class="ri-dashboard-line"></i> Dashboard</a>
      <a href="laporan_masuk.php" class="sidebar-link"><i class="ri-inbox-archive-line"></i> Laporan Masuk</a>
      <a href="laporan_selesai.php" class="sidebar-link"><i class="ri-checkbox-circle-line"></i> Laporan Selesai</a>
      <a href="info_warga.php" class="sidebar-link"><i class="ri-megaphone-line"></i> Info Warga</a>
      <a href="logout.php" class="sidebar-link"><i class="ri-logout-box-line"></i> Logout</a>
    </nav>
  </aside>

  <main class="admin-content">
    <div class="admin-header">
      <h1>Dashboard Analytics</h1>
      <div class="admin-user"><i class="ri-user-line"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
    </div>

    <div class="stats-grid">
      <div class="stat-card stat-primary"><div class="stat-icon"><i class="ri-bar-chart-line"></i></div><div class="stat-content"><p>Total Laporan</p><div class="stat-number"><?php echo $total; ?></div></div></div>
      <div class="stat-card stat-info"><div class="stat-icon"><i class="ri-inbox-archive-line"></i></div><div class="stat-content"><p>Diterima</p><div class="stat-number"><?php echo $diterima; ?></div></div></div>
      <div class="stat-card stat-warning"><div class="stat-icon"><i class="ri-tools-line"></i></div><div class="stat-content"><p>Diproses</p><div class="stat-number"><?php echo $diproses; ?></div></div></div>
      <div class="stat-card stat-success"><div class="stat-icon"><i class="ri-checkbox-circle-line"></i></div><div class="stat-content"><p>Selesai</p><div class="stat-number"><?php echo $selesai; ?></div></div></div>
    </div>

    <div class="metric-grid">
      <div class="metric-card green">
        <div class="metric-label">Rata-rata Waktu Penyelesaian</div>
        <div class="metric-value"><?php echo $avgDays; ?><span class="metric-unit">hari</span></div>
        <i class="ri-time-line metric-icon"></i>
      </div>
      <div class="metric-card orange">
        <div class="metric-label">Tercepat / Terlama</div>
        <div class="metric-value" style="font-size: 24px;"><?php echo $minDays; ?> / <?php echo $maxDays; ?><span class="metric-unit">hari</span></div>
        <i class="ri-speed-line metric-icon"></i>
      </div>
      <div class="metric-card blue">
        <div class="metric-label">Tingkat Penyelesaian</div>
        <div class="metric-value"><?php echo $completionRate; ?><span class="metric-unit">%</span></div>
        <i class="ri-checkbox-circle-line metric-icon"></i>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="chart-container">
        <div class="chart-title">
          <i class="ri-calendar-line"></i>
          Laporan per Minggu (4 Minggu Terakhir)
        </div>
        <div class="bar-chart">
          <?php 
          $maxWeekly = max(array_column($weeklyData, 'count') ?: [1]);
          foreach ($weeklyData as $week): 
            $height = $maxWeekly > 0 ? ($week['count'] / $maxWeekly) * 100 : 0;
          ?>
          <div class="bar-item">
            <div class="bar" style="height: <?php echo $height; ?>%;">
              <span class="bar-value"><?php echo $week['count']; ?></span>
            </div>
            <div class="bar-label"><?php echo $week['week_start']; ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="chart-container">
        <div class="chart-title">
          <i class="ri-pie-chart-line"></i>
          Laporan per Kategori
        </div>
        <div class="donut-chart">
          <?php 
          $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
          $totalCategory = array_sum(array_column($categoryData, 'count'));
          $circumference = 2 * 3.14159 * 70;
          $currentOffset = 0;
          ?>
          <svg class="donut-svg" viewBox="0 0 180 180">
            <circle cx="90" cy="90" r="70" fill="none" stroke="#e5e7eb" stroke-width="28"></circle>
            <?php if ($totalCategory > 0): foreach ($categoryData as $i => $cat): 
              $percentage = ($cat['count'] / $totalCategory) * 100;
              $strokeDasharray = ($percentage / 100) * $circumference;
              $strokeDashoffset = -$currentOffset;
              $currentOffset += $strokeDasharray;
              $color = $colors[$i % count($colors)];
            ?>
            <circle 
              cx="90" 
              cy="90" 
              r="70" 
              fill="none" 
              stroke="<?php echo $color; ?>" 
              stroke-width="28"
              stroke-dasharray="<?php echo $strokeDasharray; ?> <?php echo $circumference; ?>"
              stroke-dashoffset="<?php echo $strokeDashoffset; ?>"
              transform="rotate(-90 90 90)"
              style="transition: stroke-dashoffset 0.6s ease;">
            </circle>
            <?php endforeach; endif; ?>
            <text x="90" y="85" text-anchor="middle" font-size="24" font-weight="700" fill="#1f2937"><?php echo $totalCategory; ?></text>
            <text x="90" y="105" text-anchor="middle" font-size="12" fill="#6b7280">Total</text>
          </svg>
          <div class="donut-legend">
            <?php foreach ($categoryData as $i => $cat): 
              $percentage = $totalCategory > 0 ? round(($cat['count'] / $totalCategory) * 100, 1) : 0;
              $color = $colors[$i % count($colors)];
            ?>
            <div class="legend-item">
              <div class="legend-color" style="background: <?php echo $color; ?>;"></div>
              <div class="legend-label"><?php echo htmlspecialchars($cat['kategori']); ?></div>
              <div class="legend-value"><?php echo $cat['count']; ?> (<?php echo $percentage; ?>%)</div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="chart-container">
      <div class="chart-title">
        <i class="ri-line-chart-line"></i>
        Tren Laporan 6 Bulan Terakhir
      </div>
      <div class="bar-chart">
        <?php 
        $maxMonthly = max(array_column($monthlyData, 'count') ?: [1]);
        foreach ($monthlyData as $month): 
          $height = $maxMonthly > 0 ? ($month['count'] / $maxMonthly) * 100 : 0;
        ?>
        <div class="bar-item">
          <div class="bar" style="height: <?php echo $height; ?>%;">
            <span class="bar-value"><?php echo $month['count']; ?></span>
          </div>
          <div class="bar-label"><?php echo $month['month_label']; ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="chart-container">
      <div class="chart-title">
        <i class="ri-progress-3-line"></i>
        Tingkat Respon & Penyelesaian
      </div>
      <div class="progress-bar-container">
        <div class="progress-label">
          <span>Laporan yang Ditangani (Diproses + Selesai)</span>
          <strong><?php echo $processingRate; ?>%</strong>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" style="width: <?php echo $processingRate; ?>%;"></div>
        </div>
      </div>
      <div class="progress-bar-container">
        <div class="progress-label">
          <span>Laporan yang Diselesaikan</span>
          <strong><?php echo $completionRate; ?>%</strong>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" style="width: <?php echo $completionRate; ?>%; background: linear-gradient(90deg, #10b981, #059669);"></div>
        </div>
      </div>
      <div class="progress-bar-container">
        <div class="progress-label">
          <span>Laporan Menunggu (Diterima)</span>
          <strong><?php echo $total > 0 ? round(($diterima / $total) * 100, 1) : 0; ?>%</strong>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" style="width: <?php echo $total > 0 ? round(($diterima / $total) * 100, 1) : 0; ?>%; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
        </div>
      </div>
    </div>

  </main>
</body>
</html>