<?php
require_once '../config.php';

// Check session & role
checkSession([ROLE_KEPALA_PABRIK]);

// Get user info
$user = getUserByID($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - SITEA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="logo-icon-small">☕</div>
                <h2>SITEA</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="beranda.php" class="nav-item active">
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>Beranda - Kepala Pabrik</h1>
                <div class="top-bar-right">
                    <span class="user-name"><?= htmlspecialchars($user['full_name'] ?? $_SESSION['username']) ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- KPI Cards -->
                <section class="kpi-section">
                    <div class="kpi-card">
                        <div class="kpi-header">
                            <h3>Total Bahan Masuk</h3>
                            <i class="fas fa-leaf"></i>
                        </div>
                        <p class="kpi-value">0 kg</p>
                        <small>Hari ini</small>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-header">
                            <h3>Penggunaan Energi</h3>
                            <i class="fas fa-bolt"></i>
                        </div>
                        <p class="kpi-value">Rp 0</p>
                        <small>Hari ini</small>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-header">
                            <h3>Total Produksi</h3>
                            <i class="fas fa-cube"></i>
                        </div>
                        <p class="kpi-value">0 kg</p>
                        <small>Hari ini</small>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-header">
                            <h3>Status QC</h3>
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <p class="kpi-value">0 Lolos</p>
                        <small>Dari 0 batch</small>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-header">
                            <h3>Rendemen</h3>
                            <i class="fas fa-percentage"></i>
                        </div>
                        <p class="kpi-value">0%</p>
                        <small>Efisiensi pabrik</small>
                    </div>
                </section>

                <!-- Charts Section -->
                <section class="charts-section">
                    <div class="chart-card">
                        <h3>Tren 7 Hari</h3>
                        <div class="chart-placeholder">
                            <p>📊 Grafik Garis - Fluktuasi Bahan Baku</p>
                        </div>
                    </div>

                    <div class="chart-card">
                        <h3>Masuk vs Produksi</h3>
                        <div class="chart-placeholder">
                            <p>📊 Grafik Batang - Perbandingan</p>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
