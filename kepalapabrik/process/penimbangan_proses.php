<?php
// Start output buffering immediately
ob_start();

require_once '../../config.php';

// Check session
checkSession([ROLE_KEPALA_PABRIK]);

// Clear any buffered output and set headers
ob_end_clean();
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Get action from GET, POST, or JSON body
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if (empty($action)) {
    $json_data = json_decode(file_get_contents('php://input'), true);
    $action = $json_data['action'] ?? '';
}

$response = ['status' => 'error', 'message' => 'Tindakan tidak dikenal'];

try {
    if ($action === 'get') {
        // GET single data for edit
        $id = (int)($_GET['id'] ?? 0);
        
        $query = "SELECT * FROM `penimbangan` WHERE id = ? AND status = 'active'";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data) {
            $response = ['status' => 'success', 'data' => $data];
        } else {
            $response = ['status' => 'error', 'message' => 'Data tidak ditemukan'];
        }
    } 
    elseif ($action === 'create') {
        // INSERT new data
        $data = json_decode(file_get_contents('php://input'), true);
        
        $tanggal = $data['tanggal'] ?? '';
        $petugas = $data['petugas'] ?? '';
        $tahap = $data['tahap'] ?? 'Drying';
        $berat_awal = (float)($data['berat_awal'] ?? 0);
        $berat_akhir = (float)($data['berat_akhir'] ?? 0);
        $catatan = $data['catatan'] ?? '';
        $user_id = $_SESSION['user_id'];
        
        // Validation
        if (empty($tanggal) || empty($petugas) || $berat_awal <= 0 || $berat_akhir < 0) {
            throw new Exception('Data tidak lengkap atau tidak valid');
        }
        
        if ($berat_akhir > $berat_awal) {
            throw new Exception('Berat akhir tidak boleh lebih besar dari berat awal');
        }
        
        $query = "INSERT INTO `penimbangan` (tanggal, petugas, tahap, berat_awal, berat_akhir, user_id, catatan, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param("sssddis", $tanggal, $petugas, $tahap, $berat_awal, $berat_akhir, $user_id, $catatan);
        
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Data penimbangan berhasil ditambahkan'];
        } else {
            throw new Exception('Gagal menyimpan data: ' . $stmt->error);
        }
    } 
    elseif ($action === 'update') {
        // UPDATE existing data
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Check if JSON decoding failed
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data: ' . json_last_error_msg());
        }
        $id = (int)($data['id'] ?? 0);
        
        $tanggal = $data['tanggal'] ?? '';
        $petugas = $data['petugas'] ?? '';
        $tahap = $data['tahap'] ?? 'Drying';
        $berat_awal = (float)($data['berat_awal'] ?? 0);
        $berat_akhir = (float)($data['berat_akhir'] ?? 0);
        $catatan = $data['catatan'] ?? '';
        
        // Validation
        if ($id <= 0 || empty($tanggal) || empty($petugas) || $berat_awal <= 0 || $berat_akhir < 0) {
            throw new Exception('Data tidak lengkap atau tidak valid');
        }
        
        if ($berat_akhir > $berat_awal) {
            throw new Exception('Berat akhir tidak boleh lebih besar dari berat awal');
        }
        
        $query = "UPDATE `penimbangan` SET tanggal=?, petugas=?, tahap=?, berat_awal=?, berat_akhir=?, catatan=? WHERE id=?";
        $stmt = $mysql->prepare($query);
        
        if (!$stmt) {
            throw new Exception('Prepare statement failed: ' . $mysql->error);
        }
        
        $stmt->bind_param("sssddsi", $tanggal, $petugas, $tahap, $berat_awal, $berat_akhir, $catatan, $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = ['status' => 'success', 'message' => 'Data penimbangan berhasil diperbarui'];
            } else {
                $response = ['status' => 'error', 'message' => 'Tidak ada perubahan data'];
            }
        } else {
            throw new Exception('Gagal mengupdate data: ' . $stmt->error);
        }
    } 
    elseif ($action === 'delete') {
        // SOFT DELETE
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)($data['id'] ?? 0);
        
        if ($id <= 0) {
            throw new Exception('ID data tidak valid');
        }
        
        $query = "UPDATE `penimbangan` SET status='inactive' WHERE id=?";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Data penimbangan berhasil dihapus'];
        } else {
            throw new Exception('Gagal menghapus data: ' . $stmt->error);
        }
    }
    else {
        $response = ['status' => 'error', 'message' => 'Aksi tidak valid'];
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
} catch (Error $e) {
    $response = ['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()];
}

// Ensure we output valid JSON
$json_output = json_encode($response);
if ($json_output === false) {
    $json_output = json_encode(['status' => 'error', 'message' => 'JSON encoding error']);
}

echo $json_output;
exit;
