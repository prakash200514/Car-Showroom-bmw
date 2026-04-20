<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
if (!isAdmin()) { header('Location: login.php'); exit(); }

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $cat_id   = intval($_POST['category_id'] ?? 0);
    $price    = floatval($_POST['price'] ?? 0);
    $qty      = intval($_POST['stock_qty'] ?? 0);
    $desc     = trim($_POST['description'] ?? '');
    $part_no  = trim($_POST['part_number'] ?? '');
    $image    = '';

    if (!$name) $errors[] = "Part name is required.";
    if ($cat_id <= 0) $errors[] = "Category is required.";
    if ($price <= 0) $errors[] = "Valid price is required.";

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        $uploadDir = '../uploads/spares/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['image']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExts)) {
            $errors[] = "Invalid image format. Only JPG, JPEG, PNG, and WEBP are allowed.";
        } else {
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $fileName);
            $destination = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image = '/showroom/uploads/spares/' . $newFileName;
            } else {
                $errors[] = "Failed to upload image. Please check directory permissions.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO spares (name, category_id, price, stock_qty, description, image, part_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $cat_id, $price, $qty, $desc, $image, $part_no]);
            
            $_SESSION['flash'] = ['type'=>'success','msg'=>'Spare part added successfully.'];
            header("Location: spares.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

$categories = $pdo->query("SELECT * FROM spare_categories ORDER BY category_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Add Spare Part — BMW Admin</title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-plus-circle"></i> Add Spare Part</div>
            <a href="spares.php" class="adm-btn adm-btn--ghost"><i class="fas fa-arrow-left"></i> Back to Spares</a>
        </div>
        <div class="adm-content">
            <?php if(!empty($errors)): ?>
                <div class="adm-alert adm-alert--error">
                    <ul><?php foreach($errors as $er) echo "<li>$er</li>"; ?></ul>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="adm-card" style="max-width:800px; padding:30px;">
                <h3 style="margin-bottom:20px; font-weight:700;">Basic Details</h3>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div class="adm-field">
                        <label class="adm-label">Part Name *</label>
                        <input type="text" name="name" class="adm-input" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="e.g. BMW Laserlight Assembly">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Part Number</label>
                        <input type="text" name="part_number" class="adm-input" value="<?= htmlspecialchars($_POST['part_number'] ?? '') ?>" placeholder="e.g. LGT-1928-BMW">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                    <div class="adm-field">
                        <label class="adm-label">Category *</label>
                        <select name="category_id" class="adm-select" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Price (INR) *</label>
                        <input type="number" step="0.01" name="price" class="adm-input" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" placeholder="0.00">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Stock Qty *</label>
                        <input type="number" name="stock_qty" class="adm-input" required value="<?= htmlspecialchars($_POST['stock_qty'] ?? '1') ?>" placeholder="0">
                    </div>
                </div>

                <div class="adm-field" style="margin-top:20px;">
                    <label class="adm-label">Product Image</label>
                    <input type="file" name="image" class="adm-input" accept="image/jpeg, image/png, image/webp">
                    <small style="color:var(--adm-muted); display:block; margin-top:5px;">Allowed formats: .jpg, .jpeg, .png, .webp (max size: 5MB)</small>
                </div>

                <div class="adm-field" style="margin-top:20px;">
                    <label class="adm-label">Description</label>
                    <textarea name="description" class="adm-textarea" rows="4" placeholder="Brief details about the spare part..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>

                <div style="margin-top:30px; display:flex; justify-content:flex-end;">
                    <button type="submit" class="adm-btn adm-btn--primary" style="padding:12px 30px; font-size:0.9rem;">Save Spare Part</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
