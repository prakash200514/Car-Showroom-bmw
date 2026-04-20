<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
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
    $image_url = '';

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

    if (!$error && empty($image_url)) {
        $error = "Banner image is required (Upload or URL).";
    }

    if (!$error) {
        try {
            $stmt = $pdo->prepare("INSERT INTO site_banners 
                (position, eyebrow, headline, model_name, sub_label, tagline, cta_text, cta_url, cta_style, image_url, video_url, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([
                $position, $eyebrow, $headline, $model_name, 
                $sub_label, $tagline, $cta_text, $cta_url, 
                $cta_style, $image_url, $video_url
            ]);
            header('Location: banners.php?added=1');
            exit;
        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Get next position
$stmtPos = $pdo->query("SELECT MAX(position) as max_pos FROM site_banners");
$nextPos = (int)$stmtPos->fetch()['max_pos'] + 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Banner — BMW Admin</title>
    <?php include 'partials/head.php'; ?>
</head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-plus"></i> Add New Banner</div>
            <div style="display:flex;gap:8px;">
                <a href="banners.php" class="adm-btn adm-btn--ghost"><i class="fas fa-arrow-left"></i> Back to List</a>
            </div>
        </div>
        <div class="adm-content">
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
                                    <input type="number" name="position" class="adm-input" value="<?= $nextPos ?>" required>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Eyebrow (Top Label)</label>
                                    <input type="text" name="eyebrow" class="adm-input" placeholder="e.g. THE ALL-NEW">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Headline</label>
                                    <input type="text" name="headline" class="adm-input" placeholder="Main Heading">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Model Name</label>
                                    <input type="text" name="model_name" class="adm-input" placeholder="e.g. X5 M">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Sub Label / Subtitle</label>
                                    <input type="text" name="sub_label" class="adm-input" placeholder="Secondary Label">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Tagline / Description</label>
                                    <textarea name="tagline" class="adm-textarea" rows="3" placeholder="Additional details..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Call To Action</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field">
                                    <label class="adm-label">CTA Text</label>
                                    <input type="text" name="cta_text" class="adm-input" placeholder="e.g. Discover now">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">CTA URL</label>
                                    <input type="text" name="cta_url" class="adm-input" placeholder="e.g. cars.php">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">CTA Style</label>
                                    <select name="cta_style" class="adm-select">
                                        <option value="outline">Outline (White)</option>
                                        <option value="blue">Filled (Blue)</option>
                                        <option value="dark">Dark (Black)</option>
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
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">OR Image URL (Manual)</label>
                                <input type="text" name="image_url_manual" class="adm-input" placeholder="https://...">
                            </div>
                            <hr style="margin:20px 0;border:0;border-top:1px solid #eee;">
                            <div class="adm-field">
                                <label class="adm-label">Video URL (MP4 or YouTube)</label>
                                <input type="text" name="video_url" class="adm-input" placeholder="Leave empty for image-only">
                            </div>
                        </div>

                        <div style="margin-top:20px;">
                            <button type="submit" class="adm-btn adm-btn--primary" style="width:100%;justify-content:center;padding:15px;">
                                <i class="fas fa-save"></i> Save Banner
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
