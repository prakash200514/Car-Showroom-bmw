<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
// if (!isAdmin()) { header('Location: login.php'); exit(); }

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: cars.php'); exit(); }

// Fetch car
$car = $pdo->prepare("SELECT * FROM cars WHERE id=?");
$car->execute([$id]);
$car = $car->fetch();
if (!$car) { header('Location: cars.php'); exit(); }

// Fetch existing images
$images = $pdo->prepare("SELECT * FROM car_images WHERE car_id=? ORDER BY is_primary DESC");
$images->execute([$id]);
$images = $images->fetchAll();

// Extract video URL from description if stored
$videoUrl = '';
$desc = $car['description'] ?? '';
if (preg_match('/^\[VIDEO:(.*?)\]\n?/', $desc, $m)) {
    $videoUrl = $m[1];
    $desc = preg_replace('/^\[VIDEO:.*?\]\n?/', '', $desc);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name'] ?? '');
    $brand        = trim($_POST['brand'] ?? 'BMW');
    $price        = floatval($_POST['price'] ?? 0);
    $body_type    = trim($_POST['body_type'] ?? '');
    $transmission = trim($_POST['transmission'] ?? '');
    $fuel_type    = trim($_POST['fuel_type'] ?? '');
    $year         = intval($_POST['year'] ?? date('Y'));
    $engine_cc    = trim($_POST['engine_cc'] ?? '');
    $power_hp     = trim($_POST['power_hp'] ?? '');
    $mileage      = trim($_POST['mileage'] ?? '');
    $seats        = intval($_POST['seats'] ?? 5);
    $description  = trim($_POST['description'] ?? '');
    $is_featured  = isset($_POST['is_featured']) ? 1 : 0;
    $video_url    = trim($_POST['video_url'] ?? '');
    $new_img_url  = trim($_POST['new_img_url'] ?? '');
    $set_primary  = isset($_POST['set_primary']) ? 1 : 0;
    
    // New Specs
    $spec_seats      = trim($_POST['spec_seats'] ?? '');
    $spec_lights     = trim($_POST['spec_lights'] ?? '');
    $spec_airbags    = trim($_POST['spec_airbags'] ?? '');
    $spec_safety     = trim($_POST['spec_safety'] ?? '');
    $spec_tyres      = trim($_POST['spec_tyres'] ?? '');
    $spec_gearbox    = trim($_POST['spec_gearbox'] ?? '');
    $spec_speakers   = trim($_POST['spec_speakers'] ?? '');
    $spec_advantages = trim($_POST['spec_advantages'] ?? '');

    // Storytelling Sections
    $reveal_section_1_title = trim($_POST['reveal_section_1_title'] ?? '');
    $reveal_section_1_text  = trim($_POST['reveal_section_1_text'] ?? '');
    $reveal_section_2_title = trim($_POST['reveal_section_2_title'] ?? '');
    $reveal_section_2_text  = trim($_POST['reveal_section_2_text'] ?? '');

    // Handle Image Highlights Uploads
    $highlight_imgs = ['img_reveal_1','img_reveal_2','img_seats','img_lights','img_airbags','img_safety','img_tyres','img_gearbox','img_speakers'];
    $img_paths = [];
    foreach($highlight_imgs as $field) {
        $img_paths[$field] = $car[$field] ?? ''; // keep old
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === 0) {
            $uploaded = uploadImage($_FILES[$field], "../uploads/cars/");
            if ($uploaded) {
                $img_paths[$field] = "uploads/cars/" . basename($uploaded);
            }
        }
    }




    if (!$name)        $errors[] = 'Model name is required.';
    if ($price <= 0)   $errors[] = 'Valid price is required.';
    if (!$body_type)   $errors[] = 'Body type is required.';
    if (!$fuel_type)   $errors[] = 'Fuel type is required.';
    if (!$transmission)$errors[] = 'Transmission is required.';

    if (!$errors) {
        $finalDesc = $video_url ? "[VIDEO:{$video_url}]\n{$description}" : $description;
        $pdo->prepare("UPDATE cars SET name=?,brand=?,price=?,body_type=?,transmission=?,fuel_type=?,year=?,engine_cc=?,power_hp=?,mileage=?,seats=?,description=?,is_featured=?, spec_seats=?, spec_lights=?, spec_airbags=?, spec_safety=?, spec_tyres=?, spec_gearbox=?, spec_speakers=?, spec_advantages=?, reveal_section_1_title=?, reveal_section_1_text=?, reveal_section_2_title=?, reveal_section_2_text=?, img_reveal_1=?, img_reveal_2=?, img_seats=?, img_lights=?, img_airbags=?, img_safety=?, img_tyres=?, img_gearbox=?, img_speakers=? WHERE id=?")
            ->execute([$name,$brand,$price,$body_type,$transmission,$fuel_type,$year,$engine_cc,$power_hp,$mileage,$seats,$finalDesc,$is_featured, $spec_seats, $spec_lights, $spec_airbags, $spec_safety, $spec_tyres, $spec_gearbox, $spec_speakers, $spec_advantages, $reveal_section_1_title, $reveal_section_1_text, $reveal_section_2_title, $reveal_section_2_text, $img_paths['img_reveal_1'], $img_paths['img_reveal_2'], $img_paths['img_seats'], $img_paths['img_lights'], $img_paths['img_airbags'], $img_paths['img_safety'], $img_paths['img_tyres'], $img_paths['img_gearbox'], $img_paths['img_speakers'], $id]);




        // Add new images if provided
        $uploaded_images = [];
        if (isset($_FILES['car_images'])) {
            $total_files = count($_FILES['car_images']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['car_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name'     => $_FILES['car_images']['name'][$i],
                        'type'     => $_FILES['car_images']['type'][$i],
                        'tmp_name' => $_FILES['car_images']['tmp_name'][$i],
                        'error'    => $_FILES['car_images']['error'][$i],
                        'size'     => $_FILES['car_images']['size'][$i],
                    ];
                    $uploaded = uploadImage($file, "../uploads/cars/");
                    if ($uploaded) {
                        $uploaded_images[] = "uploads/cars/" . basename($uploaded);
                    }
                }
            }
        }
        
        if (!empty($uploaded_images)) {
            if ($set_primary) {
                // Unset other primary images first
                $pdo->prepare("UPDATE car_images SET is_primary=0 WHERE car_id=?")->execute([$id]);
            }
            $imgStmt = $pdo->prepare("INSERT INTO car_images (car_id, image_path, is_primary) VALUES (?,?,?)");
            foreach (array_values($uploaded_images) as $idx => $url) {
                $isPrimary = ($idx === 0 && $set_primary) ? 1 : 0;
                $imgStmt->execute([$id, $url, $isPrimary]);
            }
        }

        $_SESSION['flash'] = ['type'=>'success','msg'=>"\"$name\" updated successfully."];
        header('Location: cars.php'); exit();
    }
    // Re-populate for error display
    $car = array_merge($car, compact('name','brand','price','body_type','transmission','fuel_type','year','engine_cc','power_hp','mileage','seats','description','is_featured'));
    $videoUrl = $video_url;
    $desc = $description;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Edit Car — BMW Admin</title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-pen"></i> Edit: <?= htmlspecialchars($car['name']) ?></div>
            <div style="display:flex;gap:8px;">
                <a href="car-images.php?car_id=<?= $id ?>" class="adm-btn adm-btn--ghost"><i class="fas fa-images"></i> Manage Images</a>
                <a href="cars.php" class="adm-btn adm-btn--ghost"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
        <div class="adm-content">

            <?php if ($errors): ?>
            <div class="adm-alert adm-alert--danger"><i class="fas fa-exclamation-circle"></i> <?= implode(' &nbsp;|&nbsp; ', $errors) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

                    <!-- LEFT -->
                    <div>
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Basic Information</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Model Name *</label>
                                    <input class="adm-input" name="name" value="<?= htmlspecialchars($car['name']) ?>" required>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Brand</label>
                                    <input class="adm-input" name="brand" value="<?= htmlspecialchars($car['brand']) ?>">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Year</label>
                                    <input class="adm-input" type="number" name="year" value="<?= $car['year'] ?>">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Body Type *</label>
                                    <select class="adm-select" name="body_type" required>
                                        <?php foreach(['Sedan','SUV','Coupe','Convertible','Hatchback','Wagon'] as $t): ?>
                                        <option value="<?= $t ?>" <?= $car['body_type']===$t?'selected':'' ?>><?= $t ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Fuel Type *</label>
                                    <select class="adm-select" name="fuel_type" required>
                                        <?php foreach(['Petrol','Diesel','Electric','Hybrid','Plug-in Hybrid'] as $f): ?>
                                        <option value="<?= $f ?>" <?= $car['fuel_type']===$f?'selected':'' ?>><?= $f ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Transmission *</label>
                                    <select class="adm-select" name="transmission" required>
                                        <option value="Automatic" <?= $car['transmission']==='Automatic'?'selected':'' ?>>Automatic</option>
                                        <option value="Manual"    <?= $car['transmission']==='Manual'?'selected':'' ?>>Manual</option>
                                    </select>
                                </div>
                                <div class="adm-field"><label class="adm-label">Engine CC</label><input class="adm-input" name="engine_cc" value="<?= htmlspecialchars($car['engine_cc'] ?? '') ?>" placeholder="2998 cc"></div>
                                <div class="adm-field"><label class="adm-label">Power HP</label><input class="adm-input" name="power_hp" value="<?= htmlspecialchars($car['power_hp'] ?? '') ?>" placeholder="375 hp"></div>
                                <div class="adm-field"><label class="adm-label">Mileage</label><input class="adm-input" name="mileage" value="<?= htmlspecialchars($car['mileage'] ?? '') ?>" placeholder="15 kmpl"></div>
                                <div class="adm-field"><label class="adm-label">Seats</label><input class="adm-input" type="number" name="seats" value="<?= $car['seats'] ?? 5 ?>" min="2" max="8"></div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Description</label>
                                    <textarea class="adm-textarea" name="description" rows="4"><?= htmlspecialchars($desc) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed reveal Specs -->
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Revealing Page Specifications</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field"><label class="adm-label">Seating Detail</label><input class="adm-input" name="spec_seats" value="<?= htmlspecialchars($car['spec_seats'] ?? '') ?>"><input type="file" name="img_seats" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Lighting Tech</label><input class="adm-input" name="spec_lights" value="<?= htmlspecialchars($car['spec_lights'] ?? '') ?>"><input type="file" name="img_lights" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Airbags Info</label><input class="adm-input" name="spec_airbags" value="<?= htmlspecialchars($car['spec_airbags'] ?? '') ?>"><input type="file" name="img_airbags" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Safety Systems</label><input class="adm-input" name="spec_safety" value="<?= htmlspecialchars($car['spec_safety'] ?? '') ?>"><input type="file" name="img_safety" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Tyres & Wheels</label><input class="adm-input" name="spec_tyres" value="<?= htmlspecialchars($car['spec_tyres'] ?? '') ?>"><input type="file" name="img_tyres" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Gearbox Details</label><input class="adm-input" name="spec_gearbox" value="<?= htmlspecialchars($car['spec_gearbox'] ?? '') ?>"><input type="file" name="img_gearbox" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Sound System</label><input class="adm-input" name="spec_speakers" value="<?= htmlspecialchars($car['spec_speakers'] ?? '') ?>"><input type="file" name="img_speakers" class="adm-input mt-1"></div>
                                <div class="adm-field adm-form-full"><label class="adm-label">Primary Advantages</label><textarea class="adm-textarea" name="spec_advantages" rows="3"><?= htmlspecialchars($car['spec_advantages'] ?? '') ?></textarea></div>
                            </div>
                        </div>

                        <!-- Storytelling Reveal Sections -->
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Storytelling Sections (Reveal Page)</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Section 1: Label/Title & Image</label>
                                    <input class="adm-input" name="reveal_section_1_title" value="<?= htmlspecialchars($car['reveal_section_1_title'] ?? '') ?>">
                                    <input type="file" name="img_reveal_1" class="adm-input mt-1">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Section 1: Story Text</label>
                                    <textarea class="adm-textarea" name="reveal_section_1_text" rows="2"><?= htmlspecialchars($car['reveal_section_1_text'] ?? '') ?></textarea>
                                </div>
                                <hr class="adm-form-full" style="border:0; border-top:1px solid #eee; margin:10px 0;">
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Section 2: Label/Title & Image</label>
                                    <input class="adm-input" name="reveal_section_2_title" value="<?= htmlspecialchars($car['reveal_section_2_title'] ?? '') ?>">
                                    <input type="file" name="img_reveal_2" class="adm-input mt-1">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Section 2: Story Text</label>
                                    <textarea class="adm-textarea" name="reveal_section_2_text" rows="2"><?= htmlspecialchars($car['reveal_section_2_text'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>



                            </div>
                        </div>

                        <!-- Existing Images -->
                        <?php if (count($images) > 0): ?>
                        <div class="adm-card">
                            <div class="adm-card__header">
                                <span class="adm-card__title">Current Images</span>
                                <a href="car-images.php?car_id=<?= $id ?>" class="adm-btn adm-btn--outline adm-btn--sm"><i class="fas fa-images"></i> Full Image Manager</a>
                            </div>
                            <div class="adm-img-grid">
                                <?php foreach ($images as $img): ?>
                                <div class="adm-img-card">
                                    <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="">
                                    <div class="adm-img-card__bar">
                                        <?php if ($img['is_primary']): ?>
                                        <span class="adm-badge adm-badge--blue" style="font-size:0.58rem;">Primary</span>
                                        <?php else: ?>
                                        <span style="font-size:0.65rem;color:#bbb;">Image</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Add Additional Image -->
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Add New Images</span></div>
                            <div class="adm-field">
                                <label class="adm-label">Upload Images (JPG, PNG)</label>
                                <input type="file" name="car_images[]" id="carImages" class="adm-input" multiple accept="image/jpeg, image/png, image/webp">
                            </div>
                            <div id="newImgPreview" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;"></div>
                            <label class="adm-toggle" style="margin-top:12px; gap:10px;">
                                <input type="checkbox" name="set_primary" value="1" <?= count($images)===0 ? 'checked' : '' ?>>
                                <div class="adm-toggle__slider"></div>
                                <span class="adm-toggle__label" style="font-size:0.8rem;">Set first uploaded image as Primary</span>
                            </label>
                        </div>

                        <!-- YouTube Video -->
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">YouTube Video</span></div>
                            <div class="adm-field">
                                <label class="adm-label">YouTube URL or Video ID</label>
                                <input class="adm-input" name="video_url" id="videoUrl" value="<?= htmlspecialchars($videoUrl) ?>" placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div id="videoPreview" style="margin-top:12px;">
                                <?php if ($videoUrl):
                                    preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/', $videoUrl, $m);
                                    $vid = $m[1] ?? $videoUrl;
                                ?>
                                <iframe width="100%" height="160" src="https://www.youtube.com/embed/<?= htmlspecialchars($vid) ?>" frameborder="0" allowfullscreen style="border:1px solid #e0e0e0;"></iframe>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div>
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Pricing</span></div>
                            <div class="adm-field">
                                <label class="adm-label">Price (USD) *</label>
                                <input class="adm-input" type="number" name="price" step="0.01" min="0" value="<?= $car['price'] ?>" required>
                                <small style="color:var(--adm-muted);font-size:0.72rem;">≈ ₹<?= number_format($car['price']*84,0) ?> INR</small>
                            </div>
                        </div>
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Settings</span></div>
                            <label class="adm-toggle" style="gap:12px;padding:6px 0;">
                                <input type="checkbox" name="is_featured" <?= $car['is_featured']?'checked':'' ?>>
                                <div class="adm-toggle__slider"></div>
                                <span class="adm-toggle__label">Featured on Homepage</span>
                            </label>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <button type="submit" class="adm-btn adm-btn--primary" style="width:100%;justify-content:center;padding:12px;">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="car-images.php?car_id=<?= $id ?>" class="adm-btn adm-btn--outline" style="width:100%;justify-content:center;">
                                <i class="fas fa-images"></i> Manage All Images
                            </a>
                            <a href="cars.php" class="adm-btn adm-btn--ghost" style="width:100%;justify-content:center;">Cancel</a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script>
// Live image URL preview
document.getElementById('carImages').addEventListener('change', function(e) {
    const preview = document.getElementById('newImgPreview');
    preview.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        const url = URL.createObjectURL(file);
        preview.innerHTML += `<div style="width:40px;height:40px;border-radius:4px;overflow:hidden;border:1px solid #ddd;"><img src="${url}" style="width:100%;height:100%;object-fit:cover;"></div>`;
    });
});
document.getElementById('videoUrl').addEventListener('input', function() {
    const val = this.value.trim();
    if (!val) { document.getElementById('videoPreview').innerHTML=''; return; }
    const match = val.match(/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/);
    const id_ = match ? match[1] : val;
    document.getElementById('videoPreview').innerHTML =
        `<iframe width="100%" height="160" src="https://www.youtube.com/embed/${id_}" frameborder="0" allowfullscreen style="border:1px solid #e0e0e0;"></iframe>`;
});
// Update INR estimate live
const priceInput = document.querySelector('input[name="price"]');
const inrNote = priceInput?.nextElementSibling;
if (priceInput && inrNote) {
    priceInput.addEventListener('input', function() {
        const inr = Math.round(parseFloat(this.value||0)*84);
        inrNote.textContent = '≈ ₹' + inr.toLocaleString('en-IN') + ' INR';
    });
}
</script>
</body></html>
