<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';

// Original banners.php (Restored)
// This file manages the `site_banners` table as a simpler fallback

$errors = [];
$success = false;

// Create table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        position INT,
        eyebrow VARCHAR(255),
        headline VARCHAR(255),
        model_name VARCHAR(255),
        sub_label VARCHAR(255),
        tagline VARCHAR(255),
        cta_text VARCHAR(255),
        cta_url VARCHAR(255),
        cta_style VARCHAR(50),
        image_url VARCHAR(255),
        video_url VARCHAR(255),
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {}

// For simplicity in this project, just let's check one record or fallback to static if not implemented
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM site_banners WHERE id=?")->execute([$_GET['delete']]);
    header('Location: banners.php'); exit;
}

$stmt = $pdo->query("SELECT * FROM site_banners ORDER BY position ASC LIMIT 10");
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Homepage Banners — Admin</title>
    <?php include 'partials/head.php'; ?>
</head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-image"></i> Homepage Banners</div>
            <div style="display:flex;gap:10px;">
                <a href="banner-add.php" class="adm-btn adm-btn--primary"><i class="fas fa-plus"></i> Add New Banner</a>
                <a href="setup_banners.php" class="adm-btn adm-btn--outline">Run Banner Setup Script</a>
            </div>
        </div>

        <div class="adm-content">
            <?php if (isset($_GET['added'])): ?>
                <div class="adm-alert adm-alert--success" style="margin-bottom:20px;">
                    <i class="fas fa-check-circle"></i> New banner added successfully!
                </div>
            <?php endif; ?>

            <p style="margin-bottom:20px;color:#666;">

                Currently displaying simple banner list. To completely manage the old banners via a GUI was originally in `admin/banners.php`. 
                (Restored to simple state).
            </p>

            <div class="adm-card" style="padding:0;overflow:hidden;">
                <table class="adm-table">
                    <thead><tr>
                        <th>Position</th>
                        <th>Preview</th>
                        <th>Model</th>
                        <th>Video</th>
                        <th>Actions</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($banners as $b): ?>
                    <tr>
                        <td><?= $b['position'] ?></td>
                        <td>
                            <?php if ($b['image_url']): ?>
                            <img src="<?= htmlspecialchars(str_starts_with($b['image_url'], 'http') ? $b['image_url'] : '/showroom/'.$b['image_url']) ?>" style="height:40px;">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($b['model_name']) ?></td>
                        <td><?= $b['video_url'] ? 'Yes' : 'No' ?></td>
                        <td>
                            <a href="banner-edit.php?id=<?= $b['id'] ?>" class="adm-btn adm-btn--sm" style="background:var(--bmw-blue);color:#fff;margin-right:5px;"><i class="fas fa-edit"></i></a>
                            <a href="?delete=<?= $b['id'] ?>" class="adm-btn adm-btn--danger adm-btn--sm" onclick="return confirm('Delete?');"><i class="fas fa-trash"></i></a>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
</body>
</html>
