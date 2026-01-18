<?php
/**
 * SITEA - Proses Login
 * File ini handle semua logic login
 * Dipisah dari UI agar clean & maintainable
 */

require_once 'config.php';

$response = [
    'status' => 'error',
    'message' => ''
];

// Only POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode($response));
}

// Get input
$username = sanitize($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    $response['message'] = 'Username dan password harus diisi!';
    echo json_encode($response);
    exit;
}

// Login dengan MySQL
$result = loginWithMySQL($username, $password);

if ($result['status'] === 200) {
    // Login berhasil
    $user = $result['user'];
    
    // Set session
    setSession($user);
    
    // Response success
    $response['status'] = 'success';
    $response['message'] = 'Login berhasil!';
    $response['redirect'] = 'home.php';
    
    echo json_encode($response);
} else {
    // Login gagal
    $response['status'] = 'error';
    $response['message'] = $result['message'];
    
    echo json_encode($response);
}

?>
