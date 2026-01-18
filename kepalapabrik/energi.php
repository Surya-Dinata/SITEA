<?php
require_once '../config.php';

checkSession([ROLE_KEPALA_PABRIK]);

$user = getUserByID($_SESSION['user_id']);

// Get all energi data
$query = "SELECT * FROM `energi` WHERE status = 'active' ORDER BY tanggal DESC, created_at DESC";
$result = $mysql->query($query);
$data_energi = $result->fetch_all(MYSQLI_ASSOC);

// Calculate KPIs
$total_listrik = 0;
$total_gas = 0;
$total_biaya = 0;

foreach ($data_energi as $item) {
    if ($item['jenis_energi'] == 'Listrik') {
        $total_listrik += $item['jumlah'];
    } elseif ($item['jenis_energi'] == 'Gas') {
        $total_gas += $item['jumlah'];
    }
    $total_biaya += $item['biaya'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penggunaan Energi - SITEA</title>
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
                <a href="energi.php" class="nav-item active">
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

        <main class="main-content">
            <header class="top-bar">
                <h1>Penggunaan Energi</h1>
                <div class="top-bar-right">
                    <span class="user-name"><?= htmlspecialchars($user['full_name'] ?? $_SESSION['username']) ?></span>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- KPI Cards -->
                <section class="kpi-section">
                    <div class="kpi-card">
                        <h3>Total Listrik</h3>
                        <p class="kpi-value"><?= number_format($total_listrik, 2) ?> kWh</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Total Gas</h3>
                        <p class="kpi-value"><?= number_format($total_gas, 2) ?> m³</p>
                    </div>
                    <div class="kpi-card">
                        <h3>Total Biaya</h3>
                        <p class="kpi-value"><?= formatRupiah($total_biaya) ?></p>
                    </div>
                </section>

                <!-- Filter -->
                <section class="filter-section">
                    <div class="filter-group">
                        <input type="date" id="filterTanggal" placeholder="Filter Tanggal">
                        <select id="filterJenis">
                            <option value="">Semua Jenis</option>
                            <option value="Listrik">Listrik</option>
                            <option value="Gas">Gas</option>
                            <option value="Air">Air</option>
                        </select>
                        <button class="btn-reset" onclick="resetFilter()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </section>

                <!-- Action -->
                <section class="action-section">
                    <button class="btn-primary" onclick="openModalTambah()">
                        <i class="fas fa-plus"></i> Tambah Energi
                    </button>
                </section>

                <!-- Table -->
                <section class="table-section">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Mesin/Proses</th>
                                    <th>Jenis Energi</th>
                                    <th>Jumlah</th>
                                    <th>Biaya</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($data_energi) > 0): ?>
                                    <?php foreach ($data_energi as $item): ?>
                                    <tr data-id="<?= $item['id'] ?>">
                                        <td data-date="<?= $item['tanggal'] ?>"><?= formatDateIndo($item['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($item['mesin_proses']) ?></td>
                                        <td><?= $item['jenis_energi'] ?></td>
                                        <td data-jumlah="<?= $item['jumlah'] ?>" data-satuan="<?= htmlspecialchars($item['satuan']) ?>"><?= number_format($item['jumlah'], 2) ?> <?= $item['satuan'] ?></td>
                                        <td data-biaya="<?= $item['biaya'] ?>"><?= formatRupiah($item['biaya']) ?></td>
                                        <td>
                                            <button class="btn-sm btn-edit">Edit</button>
                                            <button class="btn-sm btn-delete">Hapus</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
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
                <h2 id="modalTitle">Tambah Energi</h2>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="formEnergi" onsubmit="submitForm(event)">
                <input type="hidden" id="formId" name="id" value="">
                
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Mesin/Proses</label>
                        <input type="text" name="mesin_proses" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Energi</label>
                        <select name="jenis_energi" required>
                            <option value="">-- Pilih --</option>
                            <option value="Listrik">Listrik</option>
                            <option value="Gas">Gas</option>
                            <option value="Air">Air</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Satuan</label>
                        <input type="text" name="satuan" placeholder="kWh, m³, dll" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Biaya (Rp)</label>
                    <input type="number" name="biaya" step="0.01" required>
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
        const API_ENDPOINT = 'process/energi_proses.php';

        function openModalTambah() {
            document.getElementById('modalTitle').textContent = 'Tambah Energi';
            document.getElementById('formId').value = '';
            document.getElementById('formEnergi').reset();
            document.querySelector('input[name="tanggal"]').value = new Date().toISOString().split('T')[0];
            document.getElementById('modalForm').classList.add('show');
        }

        function closeModal() {
            document.getElementById('modalForm').classList.remove('show');
        }

        function submitForm(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('formEnergi'));
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
                    document.getElementById('modalTitle').textContent = 'Edit Energi';
                    document.getElementById('formId').value = data.id;
                    document.querySelector('input[name="tanggal"]').value = data.tanggal;
                    document.querySelector('input[name="mesin_proses"]').value = data.mesin_proses;
                    document.querySelector('select[name="jenis_energi"]').value = data.jenis_energi;
                    document.querySelector('input[name="jumlah"]').value = data.jumlah;
                    document.querySelector('input[name="satuan"]').value = data.satuan;
                    document.querySelector('input[name="biaya"]').value = data.biaya;
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
