<?php
session_start();
if(empty($_SESSION['user']) || strtolower(trim($_SESSION['jabatan'])) != 'admin'){
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ADMIN PANEL</title>
    <meta charset="UTF-8">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{background:#f0f8f0;padding:50px;text-align:center;font-family:Arial,sans-serif;}
        .box{max-width:800px;margin:0 auto;background:white;padding:40px;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.1);}
        h1{font-size:32px;color:#2e7d32;margin-bottom:20px;}
        p{font-size:18px;color:#388e3c;margin-bottom:30px;}
        .content{background:white;padding:30px;border-radius:15px;margin:20px 0;box-shadow:0 5px 15px rgba(0,0,0,0.1);}
        ul{list-style:none;text-align:left;display:inline-block;}
        li{line-height:2;padding:10px;background:#f8f9fa;border-radius:8px;margin:5px 0;}
        a{display:inline-block;padding:12px 24px;background:#4caf50;color:white;text-decoration:none;border-radius:8px;font-weight:bold;}
    </style>
</head>
<body>
    <div class="box">
        <h1>🛠️ DASHBOARD ADMIN</h1>
        <p><strong>User:</strong> <?php echo htmlspecialchars($_SESSION['user']); ?> 
        | <strong>Jabatan:</strong> <?php echo htmlspecialchars($_SESSION['jabatan']); ?></p>
        
        <div class="content">
            <h3>✅ FULL AKSES ADMIN</h3>
            <ul>
                <li>📊 Dashboard Semua</li>
                <li>👥 Kelola User</li>
                <li>⚙️ Sistem Setup</li>
                <li>📈 Report Lengkap</li>
                <li>💰 Keuangan</li>
            </ul>
        </div>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
