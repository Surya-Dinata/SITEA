<?php 
session_start(); 
if (!isset($_SESSION['user']) || strtolower(trim($_SESSION['jabatan'])) !== 'qc') {
    header('Location: index.php'); exit; 
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>QUALITY CONTROL - SITEA</title>
    <link rel="stylesheet" href="desain.css">
</head>
<body class="dashboard">
    <div class="login-box">
        <h1>🔍 QUALITY CONTROL</h1>
        <p><strong>User:</strong> <?= htmlentities($_SESSION['user']) ?></p>
        <div style="background: white; padding: 30px; border-radius: 15px;">
            <h3>✅ HASIL QC:</h3>
            <ul style="line-height: 2;">
                <li>✅ Pass QC</li>
                <li>❌ Reject</li>
                <li>📊 Report Harian</li>
            </ul>
        </div>
        <a href="logout.php" class="footer" style="padding: 12px 24px; background: #4caf50; color: white; text-decoration: none; border-radius: 8px;">Logout</a>
    </div>
</body>
</html>
