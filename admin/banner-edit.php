<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM site_banners WHERE id = ?");
$stmt->execute([$id]);
$banner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$banner) {
    header('Location: banners.php');
    exit;
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position = (int)$_POST['position'];
    $eyebrow = sanitizeInput($_POST['eyebrow']);
    $headline = sanitizeInput($_POST['headline']);
    $model_name = sanitizeInput($_POST['model_name']);
    $sub_label = sanitizeInput($_POST['sub_label']);
    $tagline = sanitizeInput($_POST['tagline']);
    $cta_text = sanitizeInput($_POST['cta_text']);
    $cta_url = sanitizeInput($_POST['cta_url']);
    $cta_style = sanitizeInput($_POST['cta_style']);
    $video_url = sanitizeInput($_POST['video_url']);
    $image_url = $banner['image_url']; // Default to existing

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploaded = uploadImage($_FILES['image'], "../uploads/banners/");
        if ($uploaded) {
            $image_url = "uploads/banners/" . basename($uploaded);
        } else {
            $error = "Failed to upload image.";
        }
    } elseif (!empty($_POST['image_url_manual'])) {
        $image_url = sanitizeInput($_POST['image_url_manual']);
    }

    if (!$error) {
        try {
            $stmt = $pdo->prepare("UPDATE site_banners SET 
                position = ?, eyebrow = ?, headline = ?, model_name = ?, 
                sub_label = ?, tagline = ?, cta_text = ?, cta_url = ?, 
                cta_style = ?, image_url = ?, video_url = ?
                WHERE id = ?");
            $stmt->execute([
                $position, $eyebrow, $headline, $model_name, 
                $sub_label, $tagline, $cta_text, $cta_url, 
                $cta_style, $image_url, $video_url, $id
            ]);
            $success = true;
            // Refresh banner data
            $stmt = $pdo->prepare("SELECT * FROM site_banners WHERE id = ?");
            $stmt->execute([$id]);
            $banner = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Banner — BMW Admin</title>
    <?php include 'partials/head.php'; ?>
</head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-edit"></i> Edit Banner #<?= $id ?></div>
            <div style="display:flex;gap:8px;">
                <a href="banners.php" class="adm-btn adm-btn--ghost"><i class="fas fa-arrow-left"></i> Back to List</a>
            </div>
        </div>
        <div class="adm-content">
            <?php if ($success): ?>
                <div class="adm-alert adm-alert--success" style="margin-bottom:20px;">
                    <i class="fas fa-check-circle"></i> Banner updated successfully! <a href="../index.php" target="_blank" style="color:inherit;text-decoration:underline;">View Website</a>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="adm-alert adm-alert--danger" style="margin-bottom:20px;">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">
                    
                    <!-- LEFT COLUMN -->
                    <div>
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Banner Content</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field">
                                    <label class="adm-label">Position</label>
                                    <input type="number" name="position" class="adm-input" value="<?= $banner['position'] ?>" required>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Eyebrow (Top Label)</label>
                                    <input type="text" name="eyebrow" class="adm-input" value="<?= htmlspecialchars($banner['eyebrow']) ?>">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Headline</label>
                                    <input type="text" name="headline" class="adm-input" value="<?= htmlspecialchars($banner['headline']) ?>">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Model Name</label>
                                    <input type="text" name="model_name" class="adm-input" value="<?= htmlspecialchars($banner['model_name']) ?>">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Sub Label / Subtitle</label>
                                    <input type="text" name="sub_label" class="adm-input" value="<?= htmlspecialchars($banner['sub_label']) ?>">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Tagline / Description</label>
                                    <textarea name="tagline" class="adm-textarea" rows="3"><?= htmlspecialchars($banner['tagline']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Call To Action</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field">
                                    <label class="adm-label">CTA Text</label>
                                    <input type="text" name="cta_text" class="adm-input" value="<?= htmlspecialchars($banner['cta_text']) ?>">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">CTA URL</label>
                                    <input type="text" name="cta_url" class="adm-input" value="<?= htmlspecialchars($banner['cta_url']) ?>">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">CTA Style</label>
                                    <select name="cta_style" class="adm-select">
                                        <option value="outline" <?= $banner['cta_style'] == 'outline' ? 'selected' : '' ?>>Outline (White)</option>
                                        <option value="blue" <?= $banner['cta_style'] == 'blue' ? 'selected' : '' ?>>Filled (Blue)</option>
                                        <option value="dark" <?= $banner['cta_style'] == 'dark' ? 'selected' : '' ?>>Dark (Black)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div>
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Media</span></div>
                            <div class="adm-field" style="margin-bottom:15px;">
                                <label class="adm-label">Banner Image (Upload)</label>
                                <input type="file" name="image" class="adm-input" style="padding:8px;">
                                <?php if ($banner['image_url']): ?>
                                <div style="margin-top:10px;">
                                    <img src="<?= htmlspecialchars(str_starts_with($banner['image_url'], 'http') ? $banner['image_url'] : '../' . $banner['image_url']) ?>" style="width:100%;border-radius:4px;border:1px solid #ddd;">
                                    <small style="color:#888;display:block;margin-top:5px;"><?= $banner['image_url'] ?></small>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">OR Image URL (Manual)</label>
                                <input type="text" name="image_url_manual" class="adm-input" value="<?= str_starts_with($banner['image_url'], 'http') ? htmlspecialchars($banner['image_url']) : '' ?>" placeholder="https://...">
                            </div>
                            <hr style="margin:20px 0;border:0;border-top:1px solid #eee;">
                            <div class="adm-field">
                                <label class="adm-label">Video URL (MP4 or YouTube)</label>
                                <input type="text" name="video_url" class="adm-input" value="<?= htmlspecialchars($banner['video_url']) ?>" placeholder="Leave empty for image-only">
                            </div>
                        </div>

                        <div style="margin-top:20px;">
                            <button type="submit" class="adm-btn adm-btn--primary" style="width:100%;justify-content:center;padding:15px;">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="banners.php" class="adm-btn adm-btn--ghost" style="width:100%;justify-content:center;margin-top:10px;">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

