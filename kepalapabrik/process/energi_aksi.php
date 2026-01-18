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
    createEnergi();
} elseif ($action == 'update') {
    updateEnergi();
} elseif ($action == 'delete') {
    deleteEnergi();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Action tidak dikenali']);
}

function createEnergi() {
    global $mysql;
    
    $tanggal = sanitize($_POST['tanggal'] ?? '');
    $mesin_proses = sanitize($_POST['mesin_proses'] ?? '');
    $jenis_energi = sanitize($_POST['jenis_energi'] ?? '');
    $jumlah = floatval($_POST['jumlah'] ?? 0);
    $satuan = sanitize($_POST['satuan'] ?? '');
    $biaya = floatval($_POST['biaya'] ?? 0);
    $petugas = sanitize($_POST['petugas'] ?? '');
    $catatan = sanitize($_POST['catatan'] ?? '');
    
    // Validate
    if (!$tanggal || !$mesin_proses || !$jenis_energi || $jumlah <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap atau tidak valid']);
        return;
    }
    
    $query = "INSERT INTO `energi` (tanggal, mesin_proses, jenis_energi, jumlah, satuan, biaya, petugas, catatan, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";
    
    $stmt = $mysql->prepare($query);
    $stmt->bind_param("sssddsss", $tanggal, $mesin_proses, $jenis_energi, $jumlah, $satuan, $biaya, $petugas, $catatan);
    
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        
        // Log audit
        $data_baru = [
            'id' => $id,
            'tanggal' => $tanggal,
            'mesin_proses' => $mesin_proses,
            'jenis_energi' => $jenis_energi,
            'jumlah' => $jumlah,
            'satuan' => $satuan,
            'biaya' => $biaya,
            'petugas' => $petugas,
            'catatan' => $catatan
        ];
        logAudit('energi', 'INSERT', null, $data_baru);
        
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Data energi berhasil ditambahkan', 'id' => $id]);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data: ' . $mysql->error]);
    }
}

function updateEnergi() {
    global $mysql;
    
    $id = intval($_POST['id'] ?? 0);
    $tanggal = sanitize($_POST['tanggal'] ?? '');
    $mesin_proses = sanitize($_POST['mesin_proses'] ?? '');
    $jenis_energi = sanitize($_POST['jenis_energi'] ?? '');
    $jumlah = floatval($_POST['jumlah'] ?? 0);
    $satuan = sanitize($_POST['satuan'] ?? '');
    $biaya = floatval($_POST['biaya'] ?? 0);
    $petugas = sanitize($_POST['petugas'] ?? '');
    $catatan = sanitize($_POST['catatan'] ?? '');
    
    if ($id <= 0 || !$tanggal || !$mesin_proses) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
        return;
    }
    
    // Get old data for audit
    $query_old = "SELECT * FROM `energi` WHERE id = ?";
    $stmt_old = $mysql->prepare($query_old);
    $stmt_old->bind_param("i", $id);
    $stmt_old->execute();
    $result_old = $stmt_old->get_result();
    $data_lama = $result_old->fetch_assoc();
    $stmt_old->close();
    
    $query = "UPDATE `energi` SET tanggal = ?, mesin_proses = ?, jenis_energi = ?, jumlah = ?, satuan = ?, biaya = ?, petugas = ?, catatan = ? WHERE id = ?";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param("sssddsssi", $tanggal, $mesin_proses, $jenis_energi, $jumlah, $satuan, $biaya, $petugas, $catatan, $id);
    
    if ($stmt->execute()) {
        $data_baru = [
            'id' => $id,
            'tanggal' => $tanggal,
            'mesin_proses' => $mesin_proses,
            'jenis_energi' => $jenis_energi,
            'jumlah' => $jumlah,
            'satuan' => $satuan,
            'biaya' => $biaya,
            'petugas' => $petugas,
            'catatan' => $catatan
        ];
        
        logAudit('energi', 'UPDATE', $data_lama, $data_baru);
        
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Data energi berhasil diperbarui']);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data']);
    }
}

function deleteEnergi() {
    global $mysql;
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
        return;
    }
    
    // Soft delete
    $query = "UPDATE `energi` SET status = 'deleted' WHERE id = ?";
    $stmt = $mysql->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        logAudit('energi', 'DELETE', ['id' => $id], null);
        
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Data energi berhasil dihapus']);
    } else {
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
    }
}
?>
