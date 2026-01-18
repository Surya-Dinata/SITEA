<?php
require_once '../../config.php';

header('Content-Type: application/json');

// Check session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak authorized']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action == 'create') {
    createPenimbangan();
} elseif ($action == 'update') {
    updatePenimbangan();
} elseif ($action == 'delete') {
    deletePenimbangan();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Action tidak dikenali']);
}

function createPenimbangan() {
    global $mysql;
    
    $tanggal = sanitize($_POST['tanggal'] ?? '');
    $tahap = sanitize($_POST['tahap'] ?? '');
    $berat_awal = floatval($_POST['berat_awal'] ?? 0);
    $berat_akhir = floatval($_POST['berat_akhir'] ?? 0);
    $petugas = sanitize($_POST['petugas'] ?? '');
    $catatan = sanitize($_POST['catatan'] ?? '');
    
    // Validate
    if (!$tanggal || !$tahap || $berat_awal <= 0 || $berat_akhir < 0) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap atau tidak valid']);
        return;
    }
    
    $query = "INSERT INTO `penimbangan` (tanggal, tahap, berat_awal, berat_akhir, petugas, catatan, status) 
              VALUES (?, ?, ?, ?, ?, ?, 'active')";
    
    $stmt = $mysql->prepare($query);
    $stmt->bind_param("ssddsss", $tanggal, $tahap, $berat_awal, $berat_akhir, $petugas, $catatan);
    
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        
        // Log audit
        $penyusutan = $berat_awal - $berat_akhir;
        $persentase = ($penyusutan / $berat_awal) * 100;
        
        $data_baru = [
            'id' => $id,
            'tanggal' => $tanggal,
            'tahap' => $tahap,
            'berat_awal' => $berat_awal,
            'berat_akhir' => $berat_akhir,
            'penyusutan' => $penyusutan,
            'persentase_penyusutan' => $persentase,
            'petugas' => $petugas,
            'catatan' => $catatan
        ];
        logAudit('penimbangan', 'INSERT', null, $data_baru);
        
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Data penimbangan berhasil ditambahkan', 'id' => $id]);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data: ' . $mysql->error]);
    }
}

function updatePenimbangan() {
    global $mysql;
    
    $id = intval($_POST['id'] ?? 0);
    $tanggal = sanitize($_POST['tanggal'] ?? '');
    $tahap = sanitize($_POST['tahap'] ?? '');
    $berat_awal = floatval($_POST['berat_awal'] ?? 0);
    $berat_akhir = floatval($_POST['berat_akhir'] ?? 0);
    $petugas = sanitize($_POST['petugas'] ?? '');
    $catatan = sanitize($_POST['catatan'] ?? '');
    
    if ($id <= 0 || !$tanggal || !$tahap || $berat_awal <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        return;
    }
    
    // Get old data for audit
    $query_old = "SELECT * FROM `penimbangan` WHERE id = ?";
    $stmt_old = $mysql->prepare($query_old);
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $result_old = $stmt_old->get_result();
    $data_lama = $result_old->fetch_assoc();
    $stmt_old->close();
    
    $query = "UPDATE `penimbangan` SET tanggal = ?, tahap = ?, berat_awal = ?, berat_akhir = ?, petugas = ?, catatan = ? WHERE id = ?";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param("ssdddssi", $tanggal, $tahap, $berat_awal, $berat_akhir, $petugas, $catatan, $id);
    
    if ($stmt->execute()) {
        $penyusutan = $berat_awal - $berat_akhir;
        $persentase = ($penyusutan / $berat_awal) * 100;
        
        $data_baru = [
            'id' => $id,
            'tanggal' => $tanggal,
            'tahap' => $tahap,
            'berat_awal' => $berat_awal,
            'berat_akhir' => $berat_akhir,
            'penyusutan' => $penyusutan,
            'persentase_penyusutan' => $persentase,
            'petugas' => $petugas,
            'catatan' => $catatan
        ];
        
        logAudit('penimbangan', 'UPDATE', $data_lama, $data_baru);
        
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Data penimbangan berhasil diperbarui']);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data']);
    }
}

function deletePenimbangan() {
    global $mysql;
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        return;
    }
    
    // Soft delete
    $query = "UPDATE `penimbangan` SET status = 'deleted' WHERE id = ?";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        logAudit('penimbangan', 'DELETE', ['id' => $id], null);
        
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Data penimbangan berhasil dihapus']);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
?>
