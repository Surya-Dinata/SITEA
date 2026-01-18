<?php
// Generate bcrypt hash untuk password 12345
$password = '12345';
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
?>
