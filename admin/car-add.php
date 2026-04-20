<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
// if (!isAdmin()) { header('Location: login.php'); exit(); }

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $brand       = trim($_POST['brand'] ?? 'BMW');
    $price       = floatval($_POST['price'] ?? 0);
    $body_type   = trim($_POST['body_type'] ?? '');
    $transmission= trim($_POST['transmission'] ?? '');
    $fuel_type   = trim($_POST['fuel_type'] ?? '');
    $year        = intval($_POST['year'] ?? date('Y'));
    $engine_cc   = trim($_POST['engine_cc'] ?? '');
    $power_hp    = trim($_POST['power_hp'] ?? '');
    $mileage     = trim($_POST['mileage'] ?? '');
    $seats       = intval($_POST['seats'] ?? 5);
    $description = trim($_POST['description'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $video_url   = trim($_POST['video_url'] ?? '');
    
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
        $img_paths[$field] = '';
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === 0) {
            $uploaded = uploadImage($_FILES[$field], "../uploads/cars/");
            if ($uploaded) {
                $img_paths[$field] = "uploads/cars/" . basename($uploaded);
            }
        }
    }



    // Upload Car Images (multiple)
    $uploaded_images = [];
    if (isset($_FILES['car_images'])) {
        $total_files = count($_FILES['car_images']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['car_images']['error'][$i] === UPLOAD_ERR_OK) {
                // Construct a single file array to pass to uploadImage
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
    $set_primary = isset($_POST['set_first_as_primary']) ? 1 : 0;

    if (!$name)       $errors[] = 'Model name is required.';
    if ($price <= 0)  $errors[] = 'Valid price is required.';
    if (!$body_type)  $errors[] = 'Body type is required.';
    if (!$fuel_type)  $errors[] = 'Fuel type is required.';
    if (!$transmission) $errors[] = 'Transmission is required.';

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO cars (name,brand,price,body_type,transmission,fuel_type,year,engine_cc,power_hp,mileage,seats,description,is_featured, spec_seats, spec_lights, spec_airbags, spec_safety, spec_tyres, spec_gearbox, spec_speakers, spec_advantages, reveal_section_1_title, reveal_section_1_text, reveal_section_2_title, reveal_section_2_text, img_reveal_1, img_reveal_2, img_seats, img_lights, img_airbags, img_safety, img_tyres, img_gearbox, img_speakers) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");


        $stmt->execute([$name,$brand,$price,$body_type,$transmission,$fuel_type,$year,$engine_cc,$power_hp,$mileage,$seats,$description,$is_featured, $spec_seats, $spec_lights, $spec_airbags, $spec_safety, $spec_tyres, $spec_gearbox, $spec_speakers, $spec_advantages, $reveal_section_1_title, $reveal_section_1_text, $reveal_section_2_title, $reveal_section_2_text, $img_paths['img_reveal_1'], $img_paths['img_reveal_2'], $img_paths['img_seats'], $img_paths['img_lights'], $img_paths['img_airbags'], $img_paths['img_safety'], $img_paths['img_tyres'], $img_paths['img_gearbox'], $img_paths['img_speakers']]);



        $carId = $pdo->lastInsertId();

        // Save images
        if (!empty($uploaded_images)) {
            $imgStmt = $pdo->prepare("INSERT INTO car_images (car_id, image_path, is_primary) VALUES (?,?,?)");
            foreach (array_values($uploaded_images) as $idx => $url) {
                $isPrimary = ($idx === 0 && $set_primary) ? 1 : 0;
                $imgStmt->execute([$carId, $url, $isPrimary]);
            }
        }

        // Save video URL (store in car_images with is_primary=-1 or as description note)
        // We'll use a simple approach: store video_url in description as JSON-like prefix if non-empty
        if ($video_url) {
            $pdo->prepare("UPDATE cars SET description=? WHERE id=?")->execute(["[VIDEO:{$video_url}]\n".$description, $carId]);
        }

        $_SESSION['flash'] = ['type'=>'success','msg'=>"\"$name\" added successfully!"];
        header('Location: cars.php'); exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Add New Car — BMW Admin</title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-plus-circle"></i> Add New Car</div>
            <a href="cars.php" class="adm-btn adm-btn--ghost"><i class="fas fa-arrow-left"></i> Back to Cars</a>
        </div>
        <div class="adm-content">

            <?php if ($errors): ?>
            <div class="adm-alert adm-alert--danger"><i class="fas fa-exclamation-circle"></i> <?= implode(' &nbsp;|&nbsp; ', $errors) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start;">

                    <!-- LEFT: Main Details -->
                    <div>
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Basic Information</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Model Name *</label>
                                    <input class="adm-input" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="e.g. BMW X5 xDrive40i" required>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Brand</label>
                                    <input class="adm-input" name="brand" value="<?= htmlspecialchars($_POST['brand'] ?? 'BMW') ?>">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Year *</label>
                                    <input class="adm-input" type="number" name="year" value="<?= htmlspecialchars($_POST['year'] ?? date('Y')) ?>" min="2015" max="2030" required>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Body Type *</label>
                                    <select class="adm-select" name="body_type" required>
                                        <option value="">-- Select --</option>
                                        <?php foreach(['Sedan','SUV','Coupe','Convertible','Hatchback','Wagon'] as $t): ?>
                                        <option value="<?= $t ?>" <?= ($_POST['body_type'] ?? '')===$t?'selected':'' ?>><?= $t ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Fuel Type *</label>
                                    <select class="adm-select" name="fuel_type" required>
                                        <option value="">-- Select --</option>
                                        <?php foreach(['Petrol','Diesel','Electric','Hybrid','Plug-in Hybrid'] as $f): ?>
                                        <option value="<?= $f ?>" <?= ($_POST['fuel_type'] ?? '')===$f?'selected':'' ?>><?= $f ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Transmission *</label>
                                    <select class="adm-select" name="transmission" required>
                                        <option value="">-- Select --</option>
                                        <option value="Automatic" <?= ($_POST['transmission'] ?? '')==='Automatic'?'selected':'' ?>>Automatic</option>
                                        <option value="Manual"    <?= ($_POST['transmission'] ?? '')==='Manual'?'selected':'' ?>>Manual</option>
                                    </select>
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Engine Displacement</label>
                                    <input class="adm-input" name="engine_cc" value="<?= htmlspecialchars($_POST['engine_cc'] ?? '') ?>" placeholder="e.g. 2998 cc">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Power Output</label>
                                    <input class="adm-input" name="power_hp" value="<?= htmlspecialchars($_POST['power_hp'] ?? '') ?>" placeholder="e.g. 375 hp">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Fuel Efficiency</label>
                                    <input class="adm-input" name="mileage" value="<?= htmlspecialchars($_POST['mileage'] ?? '') ?>" placeholder="e.g. 15 kmpl">
                                </div>
                                <div class="adm-field">
                                    <label class="adm-label">Seats</label>
                                    <input class="adm-input" type="number" name="seats" value="<?= htmlspecialchars($_POST['seats'] ?? '5') ?>" min="2" max="8">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Description</label>
                                    <textarea class="adm-textarea" name="description" rows="4" placeholder="Full description of this model..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed reveal Specs -->
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Revealing Page Specifications</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field"><label class="adm-label">Seating Detail</label><input class="adm-input" name="spec_seats" value="<?= htmlspecialchars($_POST['spec_seats'] ?? '') ?>"><input type="file" name="img_seats" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Lighting Tech</label><input class="adm-input" name="spec_lights" value="<?= htmlspecialchars($_POST['spec_lights'] ?? '') ?>"><input type="file" name="img_lights" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Airbags Info</label><input class="adm-input" name="spec_airbags" value="<?= htmlspecialchars($_POST['spec_airbags'] ?? '') ?>"><input type="file" name="img_airbags" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Safety Systems</label><input class="adm-input" name="spec_safety" value="<?= htmlspecialchars($_POST['spec_safety'] ?? '') ?>"><input type="file" name="img_safety" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Tyres & Wheels</label><input class="adm-input" name="spec_tyres" value="<?= htmlspecialchars($_POST['spec_tyres'] ?? '') ?>"><input type="file" name="img_tyres" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Gearbox Details</label><input class="adm-input" name="spec_gearbox" value="<?= htmlspecialchars($_POST['spec_gearbox'] ?? '') ?>"><input type="file" name="img_gearbox" class="adm-input mt-1"></div>
                                <div class="adm-field"><label class="adm-label">Sound System</label><input class="adm-input" name="spec_speakers" value="<?= htmlspecialchars($_POST['spec_speakers'] ?? '') ?>"><input type="file" name="img_speakers" class="adm-input mt-1"></div>
                                <div class="adm-field adm-form-full"><label class="adm-label">Primary Advantages</label><textarea class="adm-textarea" name="spec_advantages" rows="3" placeholder="Bullet points of key selling factors..."><?= htmlspecialchars($_POST['spec_advantages'] ?? '') ?></textarea></div>
                            </div>
                        </div>

                        <!-- Storytelling Reveal Sections -->
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Storytelling Sections (Reveal Page)</span></div>
                            <div class="adm-form-grid">
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Section 1: Label/Title & Image</label>
                                    <input class="adm-input" name="reveal_section_1_title" value="<?= htmlspecialchars($_POST['reveal_section_1_title'] ?? '') ?>">
                                    <input type="file" name="img_reveal_1" class="adm-input mt-1">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Section 1: Story Text</label>
                                    <textarea class="adm-textarea" name="reveal_section_1_text" rows="2"><?= htmlspecialchars($_POST['reveal_section_1_text'] ?? '') ?></textarea>
                                </div>
                                <hr class="adm-form-full" style="border:0; border-top:1px solid #eee; margin:10px 0;">
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Section 2: Label/Title & Image</label>
                                    <input class="adm-input" name="reveal_section_2_title" value="<?= htmlspecialchars($_POST['reveal_section_2_title'] ?? '') ?>">
                                    <input type="file" name="img_reveal_2" class="adm-input mt-1">
                                </div>
                                <div class="adm-field adm-form-full">
                                    <label class="adm-label">Section 2: Story Text</label>
                                    <textarea class="adm-textarea" name="reveal_section_2_text" rows="2"><?= htmlspecialchars($_POST['reveal_section_2_text'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>



                            </div>
                        </div>

                        <!-- Images -->
                        <div class="adm-card">
                            <div class="adm-card__header">
                                <span class="adm-card__title">Car Images</span>
                                <span style="font-size:0.72rem;color:var(--adm-muted);">First is primary by default.</span>
                            </div>
                            <div class="adm-field">
                                <label class="adm-label">Upload Images (JPG, PNG)</label>
                                <input type="file" name="car_images[]" id="carImages" class="adm-input" multiple accept="image/jpeg, image/png, image/webp">
                            </div>
                            <label class="adm-toggle" style="margin-top:12px; gap:10px;">
                                <input type="checkbox" name="set_first_as_primary" value="1" checked>
                                <div class="adm-toggle__slider"></div>
                                <span class="adm-toggle__label" style="font-size:0.8rem;">Set first image as Primary</span>
                            </label>
                            <!-- Live Preview -->
                            <div id="imgPreview" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;"></div>
                        </div>

                        <!-- YouTube Video -->
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">YouTube Video</span></div>
                            <div class="adm-field">
                                <label class="adm-label">YouTube URL or Video ID</label>
                                <input class="adm-input" name="video_url" id="videoUrl" value="<?= htmlspecialchars($_POST['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=FAarzDvbTMw">
                            </div>
                            <div id="videoPreview" style="margin-top:12px;"></div>
                        </div>
                    </div>

                    <!-- RIGHT: Pricing + Settings -->
                    <div>
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Pricing</span></div>
                            <div class="adm-field">
                                <label class="adm-label">Price (USD) *</label>
                                <input class="adm-input" type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" placeholder="95000.00" required>
                                <small style="color:var(--adm-muted);font-size:0.72rem;">Displayed as INR (×84 conversion) on the public site.</small>
                            </div>
                        </div>
                        <div class="adm-card">
                            <div class="adm-card__header"><span class="adm-card__title">Settings</span></div>
                            <label class="adm-toggle" style="gap:12px;padding:6px 0;">
                                <input type="checkbox" name="is_featured" <?= isset($_POST['is_featured'])?'checked':'' ?>>
                                <div class="adm-toggle__slider"></div>
                                <span class="adm-toggle__label">Featured on Homepage</span>
                            </label>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <button type="submit" class="adm-btn adm-btn--primary" style="width:100%;justify-content:center;padding:12px;">
                                <i class="fas fa-plus"></i> Add Car
                            </button>
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
    const preview = document.getElementById('imgPreview');
    preview.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        const url = URL.createObjectURL(file);
        preview.innerHTML += `<div style="width:40px;height:40px;border-radius:4px;overflow:hidden;border:1px solid #ddd;"><img src="${url}" style="width:100%;height:100%;object-fit:cover;"></div>`;
    });
});

// YouTube video preview
document.getElementById('videoUrl').addEventListener('input', function() {
    const val = this.value.trim();
    if (!val) { document.getElementById('videoPreview').innerHTML=''; return; }
    const match = val.match(/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/);
    const id = match ? match[1] : val;
    document.getElementById('videoPreview').innerHTML =
        `<iframe width="100%" height="160" src="https://www.youtube.com/embed/${id}" frameborder="0" allowfullscreen style="border:1px solid #e0e0e0;"></iframe>`;
});
</script>
</body></html>
