<?php
include 'config/database.php';

// Configuration
$adminEmail = 'admin@showroom.com';
$adminPassword = 'Admin@123';
$adminName = 'Super Admin';

// Check if admin exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);

if ($stmt->fetch()) {
    echo "Admin account already exists.";
} else {
    // Create Admin
    $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
    $roleId = 1; // Admin Role

    $sql = "INSERT INTO users (name, email, phone, password_hash, role_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$adminName, $adminEmail, '1234567890', $hash, $roleId])) {
        echo "Admin account created successfully.<br>";
        echo "Email: $adminEmail<br>";
        echo "Password: $adminPassword<br>";
    } else {
        echo "Failed to create admin account.";
    }
}
?>
