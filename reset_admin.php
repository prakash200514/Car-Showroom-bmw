<?php
// reset_admin.php — Run once to create/reset the admin account
// DELETE THIS FILE after use in production!
include 'config/database.php';

$email    = 'admin@showroom.com';
$password = 'Admin@123';
$hash     = password_hash($password, PASSWORD_DEFAULT);

// Check if exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$email]);
$existing = $stmt->fetch();

if ($existing) {
    // Update existing
    $pdo->prepare("UPDATE users SET password_hash=?, role_id=1, name='Super Admin' WHERE email=?")
        ->execute([$hash, $email]);
    $msg = "✅ Admin account <strong>reset</strong> successfully.";
} else {
    // Create new
    $pdo->prepare("INSERT INTO users (name,email,phone,password_hash,role_id) VALUES (?,?,?,?,1)")
        ->execute(['Super Admin', $email, '9999999999', $hash]);
    $msg = "✅ Admin account <strong>created</strong> successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Reset</title>
<style>
body { font-family: 'Inter', Arial, sans-serif; background: #f4f5f7; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
.box { background: #fff; border: 1px solid #e0e0e0; padding: 40px 48px; max-width: 420px; width: 90%; text-align: center; }
.logo { font-size: 2rem; margin-bottom: 16px; }
h2 { font-size: 1.2rem; font-weight: 800; color: #111; margin-bottom: 20px; }
.cred { background: #f0f4ff; border: 1.5px solid #1C69D4; padding: 20px; margin: 20px 0; text-align: left; }
.cred div { font-size: 0.85rem; margin-bottom: 8px; color: #444; }
.cred strong { color: #1C69D4; font-size: 1rem; }
a.btn { display: inline-block; background: #1C69D4; color: #fff; padding: 12px 28px; text-decoration: none; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.05em; margin-top: 8px; }
.warn { font-size: 0.72rem; color: #999; margin-top: 16px; }
</style>
</head>
<body>
<div class="box">
    <div class="logo">⚙️</div>
    <h2><?= $msg ?></h2>
    <div class="cred">
        <div>📧 Email: <strong><?= $email ?></strong></div>
        <div>🔑 Password: <strong><?= $password ?></strong></div>
    </div>
    <a href="/showroom/login.php" class="btn">→ Go to Login</a>
    <p class="warn">⚠️ Delete this file from your server after use.</p>
</div>
</body>
</html>
