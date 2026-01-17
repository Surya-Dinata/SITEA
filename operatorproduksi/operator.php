<?php 
session_start(); 
if (!isset($_SESSION['user']) || strtolower(trim($_SESSION['jabatan'])) !== 'operator') {
    header('Location: index.php'); exit; 
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OPERATOR - SITEA</title>
    <link rel="stylesheet" href="desain.css">
</head>
<body class="dashboard">
    <div class="login-box">
        <h1>⚙️ OPERATOR PRODUKSI</h1>
        <p><strong>User:</strong> <?= htmlentities($_SESSION['user']) ?></p>
        <div style="background: white; padding: 30px; border-radius: 15px;">
            <h3>✅ INPUT PRODUKSI:</h3>
            <ul style="line-height: 2;">
                <li>▶️ Produksi Harian</li>
                <li>⚠️ Kerusakan Mesin</li>
                <li>📝 Catatan Shift</li>
            </ul>
        </div>
        <a href="logout.php" class="footer" style="padding: 12px 24px; background: #4caf50; color: white; text-decoration: none; border-radius: 8px;">Logout</a>
    </div>
</body>
</html>
