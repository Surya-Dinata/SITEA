<?php
require_once 'config.php';

// Cek data di database
$stmt = $mysql->prepare("SELECT email, username, password FROM users WHERE username = ?");
$username = 'kepala pabrik';
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

echo "<h2>Debug Login</h2>";
echo "<pre>";
echo "Username yang dicari: " . $username . "\n\n";

if ($user) {
    echo "User ditemukan:\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Username: " . $user['username'] . "\n";
    echo "Password Hash: " . $user['password'] . "\n\n";
    
    // Test password verification
    $test_password = '12345';
    $verify_result = password_verify($test_password, $user['password']);
    
    echo "Test password_verify('12345', hash):\n";
    echo "Result: " . ($verify_result ? "TRUE (cocok)" : "FALSE (tidak cocok)") . "\n\n";
    
    // Generate hash baru
    $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
    echo "Generate hash baru untuk '12345':\n";
    echo $new_hash . "\n";
} else {
    echo "User tidak ditemukan!\n";
}
echo "</pre>";
?>
