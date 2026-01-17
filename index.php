<?php 
session_start(); 
if (isset($_SESSION['user'])) { 
    header('Location: home.php'); 
    exit; 
}

$login_error = '';
if (isset($_POST['login'])) {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);
    
    $csvUrl = 'https://docs.google.com/spreadsheets/d/1D8ljWhv4GcBDuvh_xwQ3XLn5gW5tLLowVp7Ub9RHBz0/export?format=csv&gid=0';
    $data = @file_get_contents($csvUrl);
    
    if ($data !== FALSE) {
        $rows = array_slice(array_map('str_getcsv', explode("\n", trim($data))), 1);
        foreach ($rows as $row) {
            if (count($row) >= 4 && 
                trim($row[1]) === $user && 
                trim($row[2]) === $pass) {
                
                $_SESSION['user'] = trim($row[1]);
                $_SESSION['jabatan'] = trim($row[3]);
                session_write_close();
                header('Location: home.php');
                exit;
            }
        }
        $login_error = '❌ Username atau password salah!';
    } else {
        $login_error = '❌ Tidak bisa akses Google Sheets!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SITEA - Login</title>
    <link rel="stylesheet" href="desain.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo"><div class="logo-icon"></div></div>
            <h1>SITEA</h1>
            <p>Teh Hijau Gambung</p>
            <form method="POST" action="">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login">Masuk</button>
            </form>
            <?php if ($login_error): ?>
                <div class="message error"><?= $login_error ?></div>
            <?php endif; ?>
            <div class="footer">Buat Akun</div>
        </div>
    </div>
</body>
</html>
