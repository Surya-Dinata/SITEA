<?php
/**
 * SITEA - Bahan Masuk CRUD Operations
 * Process: CREATE, READ, UPDATE, DELETE
 */

require_once '../../config.php';

// Set JSON header
header('Content-Type: application/json');

// Check session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$response = ['status' => 'error', 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $response = createBahanMasuk($data);
            break;
        case 'update':
            $response = updateBahanMasuk($data);
            break;
        case 'delete':
            $response = deleteBahanMasuk($data);
            break;
        default:
            $response = ['status' => 'error', 'message' => 'Invalid action'];
    }
}

echo json_encode($response);

// ============================================================================
// FUNCTIONS
// ============================================================================

function createBahanMasuk($data) {
    global $mysql;
    
    // Validate required fields
    if (empty($data['tanggal']) || empty($data['kebun']) || empty($data['berat_awal'])) {
        return ['status' => 'error', 'message' => 'Tanggal, Kebun, dan Berat harus diisi'];
    }
    
    // Prepare statement
    $stmt = $mysql->prepare(
        "INSERT INTO `bahan_masuk` (tanggal, petugas, kebun, jenis_bahan, berat_awal, kondisi, catatan) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    
    $stmt->bind_param(
        'ssssdsss',
        $data['tanggal'],
        $data['petugas'],
        $data['kebun'],
        $data['jenis_bahan'],
        $data['berat_awal'],
        $data['kondisi'],
        $data['catatan']
    );
    
    if ($stmt->execute()) {
        logAudit('bahan_masuk', 'INSERT', null, $data);
        return ['status' => 'success', 'message' => 'Bahan masuk berhasil ditambahkan'];
    } else {
        return ['status' => 'error', 'message' => 'Gagal menambahkan: ' . $stmt->error];
    }
}

function updateBahanMasuk($data) {
    global $mysql;
    
    if (empty($data['id'])) {
        return ['status' => 'error', 'message' => 'ID harus diisi'];
    }
    
    // Get old data for audit
    $stmt_old = $mysql->prepare("SELECT * FROM `bahan_masuk` WHERE id = ?");
    $stmt_old->bind_param("i", $data['id']);
    $stmt_old->execute();
    $old_data = $stmt_old->get_result()->fetch_assoc();
    $stmt_old->close();
    
    // Update
    $stmt = $mysql->prepare(
        "UPDATE `bahan_masuk` SET tanggal=?, petugas=?, kebun=?, jenis_bahan=?, berat_awal=?, kondisi=?, catatan=? WHERE id=?"
    );
    
    $stmt->bind_param(
        'ssssdssi',
        $data['tanggal'],
        $data['petugas'],
        $data['kebun'],
        $data['jenis_bahan'],
        $data['berat_awal'],
        $data['kondisi'],
        $data['catatan'],
        $data['id']
    );
    
    if ($stmt->execute()) {
        logAudit('bahan_masuk', 'UPDATE', $old_data, $data);
        return ['status' => 'success', 'message' => 'Bahan masuk berhasil diperbarui'];
    } else {
        return ['status' => 'error', 'message' => 'Gagal memperbarui: ' . $stmt->error];
    }
}

function deleteBahanMasuk($data) {
    global $mysql;
    
    if (empty($data['id'])) {
        return ['status' => 'error', 'message' => 'ID harus diisi'];
    }
    
    // Get data for audit
    $stmt_old = $mysql->prepare("SELECT * FROM `bahan_masuk` WHERE id = ?");
    $stmt_old->bind_param("i", $data['id']);
    $stmt_old->execute();
    $old_data = $stmt_old->get_result()->fetch_assoc();
    $stmt_old->close();
    
    // Soft delete (update status)
    $stmt = $mysql->prepare("UPDATE `bahan_masuk` SET status = 'deleted' WHERE id = ?");
    $stmt->bind_param("i", $data['id']);
    
    if ($stmt->execute()) {
        logAudit('bahan_masuk', 'DELETE', $old_data, null);
        return ['status' => 'success', 'message' => 'Bahan masuk berhasil dihapus'];
    } else {
        return ['status' => 'error', 'message' => 'Gagal menghapus: ' . $stmt->error];
    }
}

function logAudit($tabel, $action, $data_lama, $data_baru) {
    global $mysql;
    
    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $data_lama_json = $data_lama ? json_encode($data_lama) : null;
    $data_baru_json = $data_baru ? json_encode($data_baru) : null;
    
    $stmt = $mysql->prepare(
        "INSERT INTO `audit_log` (user_id, tabel, action, data_lama, data_baru, ip_address) VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    $stmt->bind_param("isssss", $user_id, $tabel, $action, $data_lama_json, $data_baru_json, $ip);
    $stmt->execute();
    $stmt->close();
}

?>
