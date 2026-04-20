<?php
include '../config/database.php';
include '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role_id = 1"); // Check for admin role
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['name'] = $user['name'];
        redirect('dashboard.php');
    } else {
        $error = "Invalid admin credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - ShowroomPro</title>
    <link rel="stylesheet" href="/showroom/assets/css/theme.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; height: 100vh; background: var(--background-color);">

<div class="card glass text-center" style="width: 100%; max-width: 400px;">
    <h2>Admin Portal</h2>
    <?php if($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mt-1">
            <input type="email" name="email" placeholder="Admin Email" value="admin@showroom.com" required>
        </div>
        <div class="mt-1">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary mt-1" style="width: 100%;">Login</button>
    </form>
    <p class="mt-1"><a href="/showroom/index.php">Back to Website</a></p>
</div>

</body>
</html>
