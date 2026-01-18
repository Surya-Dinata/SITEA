<?php
require_once '../config.php';

checkSession([ROLE_KEPALA_PABRIK]);

$user = getUserByID($_SESSION['user_id']);

// Get date range filter
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Get laporan data
$query = "SELECT * FROM `laporan_kp` WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal DESC";
$stmt = $mysql->prepare($query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$data_laporan = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Calculate aggregates
$total_bahan_masuk = 0;
$total_produksi = 0;
$total_penyusutan = 0;
$total_biaya_energi = 0;
$rata_rendemen = 0;
$rata_lolos = 0;

foreach ($data_laporan as $item) {
    $total_bahan_masuk += $item['total_bahan_masuk'];
    $total_produksi += $item['total_produksi'];
    $total_penyusutan += $item['total_penyusutan'];
    $total_biaya_energi += $item['biaya_total_energi'];
    $rata_rendemen += $item['rendemen_persen'];
    $rata_lolos += $item['persentase_lolos'];
}

$count = count($data_laporan);
if ($count > 0) {
    $rata_rendemen = $rata_rendemen / $count;
    $rata_lolos = $rata_lolos / $count;
}

// Prepare chart data
$chart_tanggal = [];
$chart_bahan = [];
$chart_produksi = [];

foreach ($data_laporan as $item) {
    $chart_tanggal[] = date('d-m', strtotime($item['tanggal']));
    $chart_bahan[] = (float)$item['total_bahan_masuk'];
    $chart_produksi[] = (float)$item['total_produksi'];
}

$chart_tanggal = json_encode(array_reverse($chart_tanggal));
$chart_bahan = json_encode(array_reverse($chart_bahan));
$chart_produksi = json_encode(array_reverse($chart_produksi));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kepala Pabrik - SITEA</title>
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
                <a href="laporan_kp.php" class="nav-item active">
                    <i class="fas fa-file-pdf"></i> Laporan KP
                </a>
                <a href="laporan_qc.php" class="nav-item">
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
                <h1>Laporan Kepala Pabrik</h1>
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

                <!-- Summary KPI -->
                <section class="kpi-section">
                    <div class="kpi-card">
                        <h3>Total Bahan Masuk</h3>
                        <p class="kpi-value"><?= number_format($total_bahan_masuk, 0) ?> kg</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Total Produksi</h3>
                        <p class="kpi-value"><?= number_format($total_produksi, 0) ?> kg</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Rata-rata Rendemen</h3>
                        <p class="kpi-value"><?= number_format($rata_rendemen, 1) ?>%</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Biaya Energi</h3>
                        <p class="kpi-value"><?= formatRupiah($total_biaya_energi) ?></p>
                    </div>
                    <div class="kpi-card">
                        <h3>Rata-rata Lolos QC</h3>
                        <p class="kpi-value"><?= number_format($rata_lolos, 1) ?>%</p>
                    </div>
                </section>

                <!-- Charts -->
                <section class="charts-section">
                    <div class="chart-card">
                        <h3>Masuk vs Produksi</h3>
                        <canvas id="chartMasukProduksi"></canvas>
                    </div>

                    <div class="chart-card">
                        <h3>Rendemen Harian</h3>
                        <canvas id="chartRendemen"></canvas>
                    </div>
                </section>

                <!-- Table -->
                <section class="table-section">
                    <h3>Detail Laporan Harian</h3>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Bahan Masuk</th>
                                    <th>Produksi</th>
                                    <th>Rendemen</th>
                                    <th>Biaya Energi</th>
                                    <th>QC Lolos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($count > 0): ?>
                                    <?php foreach ($data_laporan as $item): ?>
                                    <tr>
                                        <td><?= formatDateIndo($item['tanggal']) ?></td>
                                        <td><?= number_format($item['total_bahan_masuk'], 0) ?> kg</td>
                                        <td><?= number_format($item['total_produksi'], 0) ?> kg</td>
                                        <td><?= number_format($item['rendemen_persen'], 1) ?>%</td>
                                        <td><?= formatRupiah($item['biaya_total_energi']) ?></td>
                                        <td><?= $item['batch_qc_lolos'] ?>/<?= $item['batch_qc_total'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Export -->
                <section class="action-section">
                    <button class="btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak
                    </button>
                    <button class="btn-primary" onclick="exportPDF()">
                        <i class="fas fa-download"></i> Export PDF
                    </button>
                </section>
            </div>
        </main>
    </div>

    <script>
        // Chart 1: Masuk vs Produksi
        const ctx1 = document.getElementById('chartMasukProduksi').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= $chart_tanggal ?>,
                datasets: [
                    {
                        label: 'Bahan Masuk (kg)',
                        data: <?= $chart_bahan ?>,
                        backgroundColor: '#4caf50',
                        borderRadius: 5
                    },
                    {
                        label: 'Produksi (kg)',
                        data: <?= $chart_produksi ?>,
                        backgroundColor: '#45a049',
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Chart 2: Rendemen Harian (Line Chart)
        const rendemenData = <?= json_encode(array_reverse(array_column($data_laporan, 'rendemen_persen'))) ?>;
        const ctx2 = document.getElementById('chartRendemen').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: <?= $chart_tanggal ?>,
                datasets: [{
                    label: 'Rendemen (%)',
                    data: rendemenData,
                    borderColor: '#2196f3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true, max: 100 } }
            }
        });

        function exportPDF() {
            alert('Export PDF - akan menggunakan library html2pdf');
        }
    </script>
</body>
</html>
