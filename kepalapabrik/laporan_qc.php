<?php
require_once '../config.php';

checkSession([ROLE_KEPALA_PABRIK]);

$user = getUserByID($_SESSION['user_id']);

// Get date range
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get QC data
$query = "SELECT * FROM `qc_laporan` WHERE tanggal BETWEEN ? AND ? AND status = 'active' ORDER BY tanggal DESC";
$stmt = $mysql->prepare($query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$data_qc = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate KPIs
$total_lolos = 0;
$total_gagal = 0;
$total_review = 0;

foreach ($data_qc as $item) {
    if ($item['status_hasil'] == 'Lolos') {
        $total_lolos++;
    } elseif ($item['status_hasil'] == 'Gagal') {
        $total_gagal++;
    } else {
        $total_review++;
    }
}

$total_batch = $total_lolos + $total_gagal + $total_review;
$persentase_lolos = $total_batch > 0 ? (($total_lolos / $total_batch) * 100) : 0;

// Chart data - by tahap
$tahap_stats = [];
foreach ($data_qc as $item) {
    $key = $item['tahap_proses'];
    if (!isset($tahap_stats[$key])) {
        $tahap_stats[$key] = ['lolos' => 0, 'gagal' => 0, 'review' => 0];
    }
    if ($item['status_hasil'] == 'Lolos') {
        $tahap_stats[$key]['lolos']++;
    } elseif ($item['status_hasil'] == 'Gagal') {
        $tahap_stats[$key]['gagal']++;
    } else {
        $tahap_stats[$key]['review']++;
    }
}

$chart_tahap = json_encode(array_keys($tahap_stats));
$chart_lolos = json_encode(array_column($tahap_stats, 'lolos'));
$chart_gagal = json_encode(array_column($tahap_stats, 'gagal'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan QC - SITEA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="logo-icon-small">☕</div>
                <h2>SITEA</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="beranda.php" class="nav-item">
                    <i class="fas fa-home"></i> Beranda
                </a>
                <a href="bahan_masuk.php" class="nav-item">
                    <i class="fas fa-leaf"></i> Bahan Masuk
                </a>
                <a href="energi.php" class="nav-item">
                    <i class="fas fa-bolt"></i> Penggunaan Energi
                </a>
                <a href="penimbangan.php" class="nav-item">
                    <i class="fas fa-scale-balanced"></i> Penimbangan
                </a>
                <div class="nav-divider"></div>
                <a href="laporan_kp.php" class="nav-item">
                    <i class="fas fa-file-pdf"></i> Laporan KP
                </a>
                <a href="laporan_qc.php" class="nav-item active">
                    <i class="fas fa-file-pdf"></i> Laporan QC
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <p><?= htmlspecialchars($_SESSION['username']) ?></p>
                    <small><?= htmlspecialchars($_SESSION['jabatan']) ?></small>
                </div>
                <a href="../logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1>Laporan Quality Control</h1>
                <div class="top-bar-right">
                    <span class="user-name"><?= htmlspecialchars($user['full_name'] ?? $_SESSION['username']) ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Date Filter -->
                <section class="filter-section">
                    <form method="GET" class="filter-group">
                        <input type="date" name="start_date" value="<?= $start_date ?>">
                        <input type="date" name="end_date" value="<?= $end_date ?>">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </form>
                </section>

                <!-- KPI Cards -->
                <section class="kpi-section">
                    <div class="kpi-card">
                        <h3>Total Batch</h3>
                        <p class="kpi-value"><?= $total_batch ?></p>
                    </div>
                    <div class="kpi-card">
                        <h3>Batch Lolos</h3>
                        <p class="kpi-value"><?= $total_lolos ?></p>
                    </div>
                    <div class="kpi-card">
                        <h3>Batch Gagal</h3>
                        <p class="kpi-value"><?= $total_gagal ?></p>
                    </div>
                    <div class="kpi-card">
                        <h3>Persentase Lolos</h3>
                        <p class="kpi-value"><?= number_format($persentase_lolos, 1) ?>%</p>
                    </div>
                </section>

                <!-- Charts -->
                <section class="charts-section">
                    <div class="chart-card">
                        <h3>Status QC by Tahap</h3>
                        <canvas id="chartStatusByTahap"></canvas>
                    </div>

                    <div class="chart-card">
                        <h3>Distribusi Status</h3>
                        <canvas id="chartDistribusiStatus"></canvas>
                    </div>
                </section>

                <!-- Table -->
                <section class="table-section">
                    <h3>Detail QC Report</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tahap</th>
                                    <th>Parameter</th>
                                    <th>Hasil</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                    <th>Petugas QC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($data_qc) > 0): ?>
                                    <?php foreach ($data_qc as $item): ?>
                                    <tr>
                                        <td><?= formatDateIndo($item['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($item['tahap_proses']) ?></td>
                                        <td><?= htmlspecialchars($item['parameter']) ?></td>
                                        <td><?= htmlspecialchars($item['hasil']) ?></td>
                                        <td><?= number_format($item['nilai_numerik'], 2) ?></td>
                                        <td>
                                            <span class="badge status-<?= strtolower(str_replace(' ', '-', $item['status_hasil'])) ?>">
                                                <?= $item['status_hasil'] ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($item['petugas_qc']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        // Chart 1: Status by Tahap
        const ctx1 = document.getElementById('chartStatusByTahap').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= $chart_tahap ?>,
                datasets: [
                    {
                        label: 'Lolos',
                        data: <?= $chart_lolos ?>,
                        backgroundColor: '#4caf50'
                    },
                    {
                        label: 'Gagal',
                        data: <?= $chart_gagal ?>,
                        backgroundColor: '#f44336'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Chart 2: Pie Chart
        const ctx2 = document.getElementById('chartDistribusiStatus').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Lolos', 'Gagal', 'Perlu Review'],
                datasets: [{
                    data: [<?= $total_lolos ?>, <?= $total_gagal ?>, <?= $total_review ?>],
                    backgroundColor: ['#4caf50', '#f44336', '#ff9800']
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>
