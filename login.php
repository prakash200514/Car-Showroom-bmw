<?php
// Auth + redirect MUST happen before any HTML output
session_start();
include 'config/database.php';
include 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['name']    = $user['name'];

        if ($user['role_id'] == 1) {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: customer/dashboard.php');
        }
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}

// Only include HTML-outputting header AFTER redirect logic
include 'partials/header.php';
?>

<div style="min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; background: url('https://images.unsplash.com/photo-1555215695-3004980ad54e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat; padding: 40px 20px;">
    <div style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px); border-top: 4px solid var(--primary-color); border-radius: 12px; padding: 40px; width: 100%; max-width: 450px; box-shadow: 0 15px 35px rgba(0,0,0,0.5); text-align: center;">
        <div style="margin-bottom: 30px;">
            <i class="fas fa-user-circle" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 15px;"></i>
            <h2 style="color: #fff; font-size: 2rem; margin: 0;">Welcome Back</h2>
            <p style="color: #aaa; margin-top: 5px; font-size: 0.95rem;">Login to your BMW Showroom account</p>
        </div>

        <?php if($error): ?>
            <div style="background: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545; padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: left;">
                <p style="color: #dc3545; margin: 0; font-size: 0.9rem;"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="" style="text-align: left;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #ccc; margin-bottom: 8px; font-size: 0.9rem;">Email Address</label>
                <div style="position: relative;">
                    <i class="fas fa-envelope" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                    <input type="email" name="email" placeholder="Enter your email" required style="width: 100%; padding: 12px 15px 12px 45px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 6px; font-size: 1rem; outline: none; transition: border-color 0.3s; box-sizing: border-box;">
                </div>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; color: #ccc; margin-bottom: 8px; font-size: 0.9rem;">Password</label>
                <div style="position: relative;">
                    <i class="fas fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                    <input type="password" name="password" placeholder="Enter your password" required style="width: 100%; padding: 12px 15px 12px 45px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 6px; font-size: 1rem; outline: none; transition: border-color 0.3s; box-sizing: border-box;">
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 14px; background: var(--primary-color); color: #fff; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background 0.3s, transform 0.1s;">
                Login to Account
            </button>
        </form>

        <p style="margin-top: 25px; color: #aaa; font-size: 0.95rem;">
            Don't have an account? <a href="register.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Register Now</a>
        </p>
    </div>
</div>

<style>
    input:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 2px rgba(28, 107, 186, 0.3);
    }
    button[type="submit"]:hover {
        background: #155598;
        transform: translateY(-2px);
    }
</style>

<?php include 'partials/footer.php'; ?>
