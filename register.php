<?php
include 'partials/header.php';
include 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role_id = 5; // Customer

            $sql = "INSERT INTO users (name, email, phone, password_hash, role_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $email, $phone, $password_hash, $role_id])) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<div style="min-height: calc(100vh - 80px); display: flex; align-items: center; justify-content: center; background: url('https://images.unsplash.com/photo-1617531653332-bd46c24f2068?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat; padding: 40px 20px;">
    <div style="background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px); border-top: 4px solid var(--primary-color); border-radius: 12px; padding: 40px; width: 100%; max-width: 500px; box-shadow: 0 15px 35px rgba(0,0,0,0.5); text-align: center;">
        <div style="margin-bottom: 30px;">
            <i class="fas fa-user-plus" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 15px;"></i>
            <h2 style="color: #fff; font-size: 2rem; margin: 0;">Create Account</h2>
            <p style="color: #aaa; margin-top: 5px; font-size: 0.95rem;">Join the BMW Showroom community</p>
        </div>

        <?php if($error): ?>
            <div style="background: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545; padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: left;">
                <p style="color: #dc3545; margin: 0; font-size: 0.9rem;"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div style="background: rgba(40, 167, 69, 0.1); border-left: 4px solid #28a745; padding: 12px; margin-bottom: 20px; border-radius: 4px; text-align: left;">
                <p style="color: #28a745; margin: 0; font-size: 0.9rem;"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="" style="text-align: left;">
            <div style="display: grid; grid-template-columns: 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; color: #ccc; margin-bottom: 8px; font-size: 0.9rem;">Full Name</label>
                    <div style="position: relative;">
                        <i class="fas fa-user" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                        <input type="text" name="name" placeholder="John Doe" required style="width: 100%; padding: 12px 15px 12px 45px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 6px; font-size: 1rem; outline: none; transition: border-color 0.3s; box-sizing: border-box;">
                    </div>
                </div>

                <div>
                    <label style="display: block; color: #ccc; margin-bottom: 8px; font-size: 0.9rem;">Email Address</label>
                    <div style="position: relative;">
                        <i class="fas fa-envelope" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                        <input type="email" name="email" placeholder="example@email.com" required style="width: 100%; padding: 12px 15px 12px 45px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 6px; font-size: 1rem; outline: none; transition: border-color 0.3s; box-sizing: border-box;">
                    </div>
                </div>

                <div>
                    <label style="display: block; color: #ccc; margin-bottom: 8px; font-size: 0.9rem;">Phone Number</label>
                    <div style="position: relative;">
                        <i class="fas fa-phone" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                        <input type="text" name="phone" placeholder="+1 234 567 890" required style="width: 100%; padding: 12px 15px 12px 45px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 6px; font-size: 1rem; outline: none; transition: border-color 0.3s; box-sizing: border-box;">
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; color: #ccc; margin-bottom: 8px; font-size: 0.9rem;">Password</label>
                    <div style="position: relative;">
                        <i class="fas fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                        <input type="password" name="password" placeholder="Password" required style="width: 100%; padding: 12px 15px 12px 45px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 6px; font-size: 1rem; outline: none; transition: border-color 0.3s; box-sizing: border-box;">
                    </div>
                </div>

                <div>
                    <label style="display: block; color: #ccc; margin-bottom: 8px; font-size: 0.9rem;">Confirm</label>
                    <div style="position: relative;">
                        <i class="fas fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #888;"></i>
                        <input type="password" name="confirm_password" placeholder="Confirm" required style="width: 100%; padding: 12px 15px 12px 45px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 6px; font-size: 1rem; outline: none; transition: border-color 0.3s; box-sizing: border-box;">
                    </div>
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 14px; background: var(--primary-color); color: #fff; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: background 0.3s, transform 0.1s;">
                Register Account
            </button>
        </form>

        <p style="margin-top: 25px; color: #aaa; font-size: 0.95rem;">
            Already have an account? <a href="login.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Login Here</a>
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
