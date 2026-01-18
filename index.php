<?php
require_once 'config.php';

// Jika sudah login, redirect ke home
if (isset($_SESSION['user_id'])) { 
    header('Location: home.php'); 
    exit; 
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
            
            <form method="POST" action="proses_login.php" id="loginForm">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Masuk</button>
            </form>
            
            <div id="message"></div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('message');
            
            try {
                const response = await fetch('proses_login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    messageDiv.innerHTML = '<div class="message success">✅ ' + data.message + '</div>';
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 500);
                } else {
                    messageDiv.innerHTML = '<div class="message error">❌ ' + data.message + '</div>';
                }
            } catch (error) {
                messageDiv.innerHTML = '<div class="message error">❌ Terjadi kesalahan</div>';
            }
        });
    </script>
</body>
</html>
