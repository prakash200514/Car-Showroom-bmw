<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
// if (!isAdmin()) { header('Location: login.php'); exit(); }

$carId = intval($_GET['car_id'] ?? 0);
if (!$carId) { header('Location: cars.php'); exit(); }

$car = $pdo->prepare("SELECT * FROM cars WHERE id=?");
$car->execute([$carId]); $car = $car->fetch();
if (!$car) { header('Location: cars.php'); exit(); }

$flash = null;

// SET PRIMARY
if (isset($_GET['set_primary']) && is_numeric($_GET['set_primary'])) {
    $pdo->prepare("UPDATE car_images SET is_primary=0 WHERE car_id=?")->execute([$carId]);
    $pdo->prepare("UPDATE car_images SET is_primary=1 WHERE id=? AND car_id=?")->execute([$_GET['set_primary'],$carId]);
    $flash = ['type'=>'success','msg'=>'Primary image updated.'];
}

// DELETE IMAGE
if (isset($_GET['del_img']) && is_numeric($_GET['del_img'])) {
    $pdo->prepare("DELETE FROM car_images WHERE id=? AND car_id=?")->execute([$_GET['del_img'],$carId]);
    $flash = ['type'=>'success','msg'=>'Image deleted.'];
}

// ADD IMAGE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['img_url'])) {
    $url       = trim($_POST['img_url']);
    $isPrimary = intval($_POST['is_primary'] ?? 0);
    if ($isPrimary) {
        $pdo->prepare("UPDATE car_images SET is_primary=0 WHERE car_id=?")->execute([$carId]);
    }
    $pdo->prepare("INSERT INTO car_images (car_id,image_path,is_primary) VALUES (?,?,?)")->execute([$carId,$url,$isPrimary]);
    $flash = ['type'=>'success','msg'=>'Image added.'];
}

// Fetch images
$images = $pdo->prepare("SELECT * FROM car_images WHERE car_id=? ORDER BY is_primary DESC, id ASC");
$images->execute([$carId]); $images = $images->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Manage Images — <?= htmlspecialchars($car['name']) ?></title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-images"></i> Images: <?= htmlspecialchars($car['name']) ?></div>
            <div style="display:flex;gap:8px;">
                <a href="car-edit.php?id=<?= $carId ?>" class="adm-btn adm-btn--ghost"><i class="fas fa-pen"></i> Edit Car</a>
                <a href="cars.php" class="adm-btn adm-btn--ghost"><i class="fas fa-arrow-left"></i> All Cars</a>
            </div>
        </div>
        <div class="adm-content">

            <?php if ($flash): ?>
            <div class="adm-alert adm-alert--<?= $flash['type'] ?>"><i class="fas fa-check-circle"></i> <?= $flash['msg'] ?></div>
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

                <!-- Image Grid -->
                <div class="adm-card">
                    <div class="adm-card__header">
                        <span class="adm-card__title"><?= count($images) ?> Image<?= count($images)!=1?'s':'' ?></span>
                        <span style="font-size:0.72rem;color:var(--adm-muted);">First image is shown on public site as primary</span>
                    </div>

                    <?php if (count($images) > 0): ?>
                    <div class="adm-img-grid" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr));">
                        <?php foreach ($images as $img): ?>
                        <div class="adm-img-card" style="<?= $img['is_primary'] ? 'border-color:var(--adm-blue);' : '' ?>">
                            <?php if ($img['is_primary']): ?>
                            <div style="position:absolute;top:6px;left:6px;z-index:2;">
                                <span class="adm-badge adm-badge--blue" style="font-size:0.58rem;padding:2px 7px;">✓ Primary</span>
                            </div>
                            <?php endif; ?>
                            <img src="<?= htmlspecialchars($img['image_path']) ?>" alt=""
                                 style="height:130px;object-fit:cover;"
                                 onerror="this.src='';this.style.background='#f0f0f0';this.style.minHeight='130px';">
                            <div class="adm-img-card__bar" style="flex-direction:column;gap:5px;padding:8px;">
                                <div style="font-size:0.65rem;color:#aaa;word-break:break-all;line-height:1.3;max-height:36px;overflow:hidden;">
                                    <?= htmlspecialchars(basename(parse_url($img['image_path'], PHP_URL_PATH))) ?>
                                </div>
                                <div style="display:flex;gap:4px;width:100%;">
                                    <?php if (!$img['is_primary']): ?>
                                    <a href="?car_id=<?= $carId ?>&set_primary=<?= $img['id'] ?>"
                                       class="adm-btn adm-btn--outline adm-btn--sm" style="flex:1;justify-content:center;font-size:0.65rem;">
                                        Set Primary
                                    </a>
                                    <?php endif; ?>
                                    <a href="?car_id=<?= $carId ?>&del_img=<?= $img['id'] ?>"
                                       class="adm-btn adm-btn--danger adm-btn--sm" style="justify-content:center;"
                                       onclick="return confirm('Delete this image?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div style="text-align:center;padding:40px;color:var(--adm-muted);">
                        <i class="fas fa-image" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:0.3;"></i>
                        No images yet. Add one using the form →
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Add Image Form -->
                <div>
                    <div class="adm-card">
                        <div class="adm-card__header"><span class="adm-card__title">Add Image URL</span></div>
                        <form method="POST">
                            <div class="adm-field" style="margin-bottom:12px;">
                                <label class="adm-label">Image URL *</label>
                                <input class="adm-input" name="img_url" id="addImgUrl" placeholder="https://images.unsplash.com/photo-…" required>
                            </div>
                            <!-- Live preview -->
                            <div id="addPreview" style="margin-bottom:12px;"></div>
                            <label class="adm-toggle" style="margin-bottom:16px;">
                                <input type="checkbox" name="is_primary" value="1">
                                <div class="adm-toggle__slider"></div>
                                <span class="adm-toggle__label" style="font-size:0.8rem;">Set as Primary Image</span>
                            </label>
                            <button type="submit" class="adm-btn adm-btn--primary" style="width:100%;justify-content:center;">
                                <i class="fas fa-plus"></i> Add Image
                            </button>
                        </form>
                    </div>

                    <!-- Helpful BMW image URLs -->
                    <div class="adm-card">
                        <div class="adm-card__header"><span class="adm-card__title">Quick BMW Images</span></div>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            <?php foreach([
                                ['BMW M4 Competition', 'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=800&q=80'],
                                ['BMW X5 M (SUV)',     'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=800&q=80'],
                                ['BMW 7 Series',       'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=800&q=80'],
                                ['BMW i8 / Z4',        'https://images.unsplash.com/photo-1556189250-72ba954e96b5?auto=format&fit=crop&w=800&q=80'],
                                ['BMW 3 Series',       'https://images.unsplash.com/photo-1553440683-1b94dd08f6d8?auto=format&fit=crop&w=800&q=80'],
                                ['BMW M Sport (dark)', 'https://images.unsplash.com/photo-1580274455191-1c62238fa1f3?auto=format&fit=crop&w=800&q=80'],
                            ] as [$label,$url]): ?>
                            <button type="button" onclick="document.getElementById('addImgUrl').value='<?= $url ?>'; document.getElementById('addPreview').innerHTML='<img src=\'<?= $url ?>\' style=\'width:100%;height:80px;object-fit:cover;border:1px solid #e0e0e0;\'>';"
                                style="text-align:left;background:#f8f9fa;border:1px solid var(--adm-border);padding:7px 10px;font-size:0.74rem;cursor:pointer;color:var(--adm-text);transition:background 0.2s;"
                                onmouseover="this.style.background='#e8f0fe'" onmouseout="this.style.background='#f8f9fa'">
                                <i class="fas fa-car" style="color:var(--adm-blue);margin-right:6px;"></i><?= $label ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('addImgUrl').addEventListener('input', function() {
    const url = this.value.trim();
    document.getElementById('addPreview').innerHTML = url
        ? `<img src="${url}" style="width:100%;max-height:100px;object-fit:cover;border:1px solid #e0e0e0;" onerror="this.style.opacity=0.2;">`
        : '';
});
</script>
</body></html>
