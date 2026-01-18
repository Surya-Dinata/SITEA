<?php
require_once '../config.php';

// Check session & role
checkSession([ROLE_KEPALA_PABRIK]);

// Get user info
$user = getUserByID($_SESSION['user_id']);

// Get all bahan masuk
$query = "SELECT * FROM `bahan_masuk` WHERE status = 'active' ORDER BY tanggal DESC, created_at DESC";
$result = $mysql->query($query);
$data_bahan = $result->fetch_all(MYSQLI_ASSOC);

// Calculate KPIs
$total_berat = 0;
$kondisi_baik = 0;
$total_data = count($data_bahan);

foreach ($data_bahan as $item) {
    $total_berat += $item['berat_awal'];
    if ($item['kondisi'] == 'Baik') {
        $kondisi_baik++;
    }
}

$persentase_baik = $total_data > 0 ? round(($kondisi_baik / $total_data) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bahan Masuk - SITEA</title>
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
                <a href="beranda.php" class="nav-item">
                    <i class="fas fa-home"></i> Beranda
                </a>
                <a href="bahan_masuk.php" class="nav-item active">
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
                <h1>Bahan Masuk</h1>
                <div class="top-bar-right">
                    <span class="user-name"><?= htmlspecialchars($user['full_name'] ?? $_SESSION['username']) ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- KPI Cards -->
                <section class="kpi-section">
                    <div class="kpi-card">
                        <h3>Total Data</h3>
                        <p class="kpi-value"><?= $total_data ?></p>
                    </div>
                    <div class="kpi-card">
                        <h3>Total Berat</h3>
                        <p class="kpi-value"><?= number_format($total_berat, 0) ?> kg</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Kondisi Baik</h3>
                        <p class="kpi-value"><?= $persentase_baik ?>%</p>
                    </div>
                </section>

                <!-- Search & Filter -->
                <section class="filter-section">
                    <div class="filter-group">
                        <input type="date" id="filterTanggal" placeholder="Filter Tanggal">
                        <input type="text" id="filterKebun" placeholder="Filter Kebun">
                        <select id="filterKondisi">
                            <option value="">Semua Kondisi</option>
                            <option value="Baik">Baik</option>
                            <option value="Cukup">Cukup</option>
                            <option value="Kurang Baik">Kurang Baik</option>
                        </select>
                        <button class="btn-reset" onclick="resetFilter()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </section>

                <!-- Tombol Tambah -->
                <section class="action-section">
                    <button class="btn-primary" onclick="openModalTambah()">
                        <i class="fas fa-plus"></i> Tambah Bahan Masuk
                    </button>
                </section>

                <!-- Tabel Data -->
                <section class="table-section">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Petugas</th>
                                    <th>Kebun</th>
                                    <th>Jenis</th>
                                    <th>Berat (kg)</th>
                                    <th>Kondisi</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <?php if ($total_data > 0): ?>
                                    <?php foreach ($data_bahan as $item): ?>
                                    <tr>
                                        <td><?= formatDateIndo($item['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($item['petugas']) ?></td>
                                        <td><?= htmlspecialchars($item['kebun']) ?></td>
                                        <td><?= htmlspecialchars($item['jenis_bahan']) ?></td>
                                        <td><?= number_format($item['berat_awal'], 2) ?></td>
                                        <td><span class="badge kondisi-<?= strtolower($item['kondisi']) ?>"><?= $item['kondisi'] ?></span></td>
                                        <td><?= htmlspecialchars($item['catatan']) ?></td>
                                        <td>
                                            <button class="btn-sm btn-edit" onclick="editData(<?= $item['id'] ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-sm btn-delete" onclick="deleteData(<?= $item['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal Tambah/Edit -->
    <div id="modalForm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Bahan Masuk</h2>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="formData" onsubmit="submitForm(event)">
                <input type="hidden" id="formId" name="id" value="">
                
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Petugas</label>
                        <input type="text" name="petugas" required>
                    </div>
                    <div class="form-group">
                        <label>Kebun</label>
                        <input type="text" name="kebun" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Jenis Bahan</label>
                        <input type="text" name="jenis_bahan">
                    </div>
                    <div class="form-group">
                        <label>Berat Awal (kg)</label>
                        <input type="number" name="berat_awal" step="0.01" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Kondisi</label>
                    <select name="kondisi" required>
                        <option value="">-- Pilih Kondisi --</option>
                        <option value="Baik">Baik</option>
                        <option value="Cukup">Cukup</option>
                        <option value="Kurang Baik">Kurang Baik</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="catatan" rows="3"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Message Toast -->
    <div id="toast" class="toast"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        const API_ENDPOINT = 'process/bahan_masuk_proses.php';

        function openModalTambah() {
            document.getElementById('modalTitle').textContent = 'Tambah Bahan Masuk';
            document.getElementById('formId').value = '';
            document.getElementById('formData').reset();
            document.getElementById('formData').querySelector('input[name="tanggal"]').value = new Date().toISOString().split('T')[0];
            document.getElementById('modalForm').classList.add('show');
        }

        function closeModal() {
            document.getElementById('modalForm').classList.remove('show');
        }

        function submitForm(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('formData'));
            const id = document.getElementById('formId').value;
            
            const data = Object.fromEntries(formData);
            
            // Validasi client-side
            if (!data.tanggal || !data.tanggal.trim()) {
                showToast('Tanggal harus diisi', 'error');
                return;
            }
            if (!data.petugas || !data.petugas.trim()) {
                showToast('Petugas harus diisi', 'error');
                return;
            }
            if (!data.kebun || !data.kebun.trim()) {
                showToast('Kebun harus diisi', 'error');
                return;
            }
            if (!data.berat_awal || parseFloat(data.berat_awal) <= 0) {
                showToast('Berat Awal harus diisi dan lebih dari 0', 'error');
                return;
            }
            if (!data.kondisi || !data.kondisi.trim()) {
                showToast('Kondisi harus dipilih', 'error');
                return;
            }
            
            // Set action and id if editing
            data.action = id ? 'update' : 'create';
            if (id) {
                data.id = id;
            }
            
            fetch(API_ENDPOINT, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(result => {
                showToast(result.message, result.status);
                if (result.status === 'success') {
                    closeModal();
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                showToast('Terjadi kesalahan: ' + err.message, 'error');
            });
        }

        function editData(id) {
            // Fetch data untuk edit
            fetch(API_ENDPOINT + '?action=get&id=' + id)
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    const data = result.data;
                    document.getElementById('modalTitle').textContent = 'Edit Bahan Masuk';
                    document.getElementById('formId').value = data.id;
                    document.querySelector('input[name="tanggal"]').value = data.tanggal;
                    document.querySelector('input[name="petugas"]').value = data.petugas;
                    document.querySelector('input[name="kebun"]').value = data.kebun;
                    document.querySelector('input[name="jenis_bahan"]').value = data.jenis_bahan;
                    document.querySelector('input[name="berat_awal"]').value = data.berat_awal;
                    document.querySelector('select[name="kondisi"]').value = data.kondisi;
                    document.querySelector('textarea[name="catatan"]').value = data.catatan;
                    document.getElementById('modalForm').classList.add('show');
                }
            });
        }

        function deleteData(id) {
            if (!confirm('Yakin ingin menghapus data ini?')) return;
            
            fetch(API_ENDPOINT, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id })
            })
            .then(res => res.json())
            .then(result => {
                showToast(result.message, result.status);
                if (result.status === 'success') {
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }

        function showToast(message, status) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast toast-' + status;
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        function resetFilter() {
            document.getElementById('filterTanggal').value = '';
            document.getElementById('filterKebun').value = '';
            document.getElementById('filterKondisi').value = '';
            location.reload();
        }

        // Persiapan form saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalInput = document.querySelector('input[name="tanggal"]');
            if (tanggalInput && !tanggalInput.value) {
                tanggalInput.value = new Date().toISOString().split('T')[0];
            }
        });
    </script>
</body>
</html>
