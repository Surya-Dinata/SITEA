<?php
/**
 * ============================================================================
 * SITEA - Sistem Informasi Teh Gambung
 * File Konfigurasi Terpusat
 * 
 * File ini berisi:
 * - MySQL Connection
 * - Authentication Functions
 * - Session Management
 * - Helper Functions
 * ============================================================================
 */

// ============================================================================
// ERROR REPORTING & TIMEZONE
// ============================================================================
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
date_default_timezone_set('Asia/Jakarta');

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================
define('MYSQL_HOST', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');
define('MYSQL_DB', 'sitea');

// ============================================================================
// MYSQL CONNECTION
// ============================================================================
$mysql = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

// Check connection
if ($mysql->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Database Connection Error: ' . $mysql->connect_error
    ]));
}

// Set charset UTF-8
$mysql->set_charset("utf8mb4");

// ============================================================================
// SESSION MANAGEMENT
// ============================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// AUTHENTICATION FUNCTIONS
// ============================================================================

/**
 * Login dengan username & password (MySQL)
 * 
 * @param string $username
 * @param string $password
 * @return array
 */
function loginWithMySQL($username, $password) {
    global $mysql;
    
    // Query user dari MySQL
    $stmt = $mysql->prepare("SELECT * FROM `users` WHERE username = ? AND status = 'active'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        return [
            'status' => 401,
            'message' => 'Username tidak ditemukan'
        ];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return [
            'status' => 401,
            'message' => 'Password salah'
        ];
    }
    
    // Login berhasil
    return [
        'status' => 200,
        'user' => $user
    ];
}

/**
 * Get user by username
 * 
 * @param string $username
 * @return array|null
 */
function getUserByUsername($username) {
    global $mysql;
    
    $stmt = $mysql->prepare("SELECT * FROM `users` WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user ? $user : null;
}

/**
 * Get user by ID
 * 
 * @param int $id
 * @return array|null
 */
function getUserByID($id) {
    global $mysql;
    
    $stmt = $mysql->prepare("SELECT * FROM `users` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user ? $user : null;
}

/**
 * Hash password dengan bcrypt
 * 
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Verify password
 * 
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check session & redirect if not logged in
 * 
 * @param array $allowed_roles - Role yang diizinkan (optional)
 * @return void
 */
function checkSession($allowed_roles = []) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
    
    // Check role if specified
    if (!empty($allowed_roles)) {
        if (!in_array($_SESSION['jabatan'], $allowed_roles)) {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }
    }
}

/**
 * Set session after login
 * 
 * @param array $user - User data from database
 * @return void
 */
function setSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user'] = $user['username']; // For compatibility
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['jabatan'] = $user['jabatan'];
    $_SESSION['status'] = $user['status'];
}

/**
 * Clear session & logout
 * 
 * @return void
 */
function logoutSession() {
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Format rupiah
 * 
 * @param float $amount
 * @return string
 */
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date Indonesia
 * 
 * @param string $date - Format: Y-m-d
 * @return string
 */
function formatDateIndo($date) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $parse = date_parse($date);
    return $parse['day'] . ' ' . $months[$parse['month']] . ' ' . $parse['year'];
}

/**
 * Sanitize input
 * 
 * @param string $input
 * @return string
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 * 
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ============================================================================
// CONSTANTS
// ============================================================================
define('BASE_URL', 'http://localhost/SITEA/SITEA');
define('APP_NAME', 'SITEA - Sistem Informasi Teh Gambung');

// Jabatan / Roles
define('ROLE_ADMIN', 'Admin');
define('ROLE_KEPALA_PABRIK', 'Kepala Pabrik');
define('ROLE_OPERATOR', 'Operator Produksi');
define('ROLE_QC', 'Quality Control');

// Status
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');

?>
