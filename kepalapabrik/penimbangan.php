<?php
require_once '../config.php';

checkSession([ROLE_KEPALA_PABRIK]);

$user = getUserByID($_SESSION['user_id']);

// Get all penimbangan data
$query = "SELECT * FROM `penimbangan` WHERE status = 'active' ORDER BY tanggal DESC";
$result = $mysql->query($query);
$data_penimbangan = $result->fetch_all(MYSQLI_ASSOC);

// Calculate KPIs
$total_awal = 0;
$total_akhir = 0;
$total_penyusutan = 0;

foreach ($data_penimbangan as $item) {
    $total_awal += $item['berat_awal'];
    $total_akhir += $item['berat_akhir'];
    $total_penyusutan += $item['penyusutan'];
}

$rendemen = $total_awal > 0 ? (($total_akhir / $total_awal) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penimbangan - SITEA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
                <a href="penimbangan.php" class="nav-item active">
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

        <main class="main-content">
            <header class="top-bar">
                <h1>Penimbangan</h1>
                <div class="top-bar-right">
                    <span class="user-name"><?= htmlspecialchars($user['full_name'] ?? $_SESSION['username']) ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- KPI Cards -->
                <section class="kpi-section">
                    <div class="kpi-card">
                        <h3>Total Berat Awal</h3>
                        <p class="kpi-value"><?= number_format($total_awal, 2) ?> kg</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Total Produksi</h3>
                        <p class="kpi-value"><?= number_format($total_akhir, 2) ?> kg</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Rendemen</h3>
                        <p class="kpi-value"><?= number_format($rendemen, 1) ?>%</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Total Penyusutan</h3>
                        <p class="kpi-value"><?= number_format($total_penyusutan, 2) ?> kg</p>
                    </div>
                </section>

                <!-- Filter -->
                <section class="filter-section">
                    <div class="filter-group">
                        <input type="date" id="filterTanggal" placeholder="Filter Tanggal">
                        <select id="filterTahap">
                            <option value="">Semua Tahap</option>
                            <option value="Withering">Withering</option>
                            <option value="Drying">Drying</option>
                            <option value="Sorting">Sorting</option>
                            <option value="Packaging">Packaging</option>
                        </select>
                        <button class="btn-reset" onclick="resetFilter()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </section>

                <!-- Action -->
                <section class="action-section">
                    <button class="btn-primary" onclick="openModalTambah()">
                        <i class="fas fa-plus"></i> Tambah Penimbangan
                    </button>
                </section>

                <!-- Table -->
                <section class="table-section">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tahap</th>
                                    <th>Berat Awal</th>
                                    <th>Berat Akhir</th>
                                    <th>Penyusutan</th>
                                    <th>%</th>
                                    <th>Petugas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($data_penimbangan) > 0): ?>
                                    <?php foreach ($data_penimbangan as $item): ?>
                                    <tr data-id="<?= $item['id'] ?>" data-catatan="<?= htmlspecialchars($item['catatan']) ?>">
                                        <td data-date="<?= $item['tanggal'] ?>"><?= formatDateIndo($item['tanggal']) ?></td>
                                        <td><?= $item['tahap'] ?></td>
                                        <td data-awal="<?= $item['berat_awal'] ?>"><?= number_format($item['berat_awal'], 2) ?> kg</td>
                                        <td data-akhir="<?= $item['berat_akhir'] ?>"><?= number_format($item['berat_akhir'], 2) ?> kg</td>
                                        <td><?= number_format($item['penyusutan'], 2) ?> kg</td>
                                        <td><?= number_format($item['persentase_penyusutan'], 1) ?>%</td>
                                        <td><?= htmlspecialchars($item['petugas']) ?></td>
                                        <td>
                                            <button class="btn-sm btn-edit">Edit</button>
                                            <button class="btn-sm btn-delete">Hapus</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="modalForm" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Penimbangan</h2>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="formTimbang" onsubmit="submitForm(event)">
                <input type="hidden" id="formId" name="id" value="">
                
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tahap</label>
                        <select name="tahap" required>
                            <option value="">-- Pilih --</option>
                            <option value="Withering">Withering</option>
                            <option value="Drying">Drying</option>
                            <option value="Sorting">Sorting</option>
                            <option value="Packaging">Packaging</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Petugas</label>
                        <input type="text" name="petugas" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Berat Awal (kg)</label>
                        <input type="number" name="berat_awal" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Berat Akhir (kg)</label>
                        <input type="number" name="berat_akhir" step="0.01" required>
                    </div>
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

    <!-- Toast Notifications -->
    <div id="toast" class="toast"></div>

    <script>
        const API_ENDPOINT = 'process/penimbangan_proses.php';

        function openModalTambah() {
            document.getElementById('modalTitle').textContent = 'Tambah Penimbangan';
            document.getElementById('formId').value = '';
            document.getElementById('formTimbang').reset();
            document.querySelector('input[name="tanggal"]').value = new Date().toISOString().split('T')[0];
            document.getElementById('modalForm').classList.add('show');
        }

        function closeModal() {
            document.getElementById('modalForm').classList.remove('show');
        }

        function submitForm(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('formTimbang'));
            const id = document.getElementById('formId').value;
            
            const data = Object.fromEntries(formData);
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
            .catch(err => showToast('Terjadi kesalahan', 'error'));
        }

        function editData(id) {
            fetch(API_ENDPOINT + '?action=get&id=' + id)
            .then(res => res.json())
            .then(result => {
                if (result.status === 'success') {
                    const data = result.data;
                    document.getElementById('modalTitle').textContent = 'Edit Penimbangan';
                    document.getElementById('formId').value = data.id;
                    document.querySelector('input[name="tanggal"]').value = data.tanggal;
                    document.querySelector('select[name="tahap"]').value = data.tahap;
                    document.querySelector('input[name="petugas"]').value = data.petugas;
                    document.querySelector('input[name="berat_awal"]').value = data.berat_awal;
                    document.querySelector('input[name="berat_akhir"]').value = data.berat_akhir;
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
            const toast = document.getElementById('toast') || createToast();
            toast.textContent = message;
            toast.className = 'toast toast-' + status;
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 3000);
        }

        function createToast() {
            const toast = document.createElement('div');
            toast.id = 'toast';
            document.body.appendChild(toast);
            return toast;
        }

        function resetFilter() {
            location.reload();
        }

        // Add click handlers
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.closest('tr').dataset.id;
                    editData(id);
                });
            });
            
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.closest('tr').dataset.id;
                    deleteData(id);
                });
            });
        });
    </script>
</body>
</html>
