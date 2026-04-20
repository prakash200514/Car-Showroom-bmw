<?php
include 'partials/header.php';
include 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Fetch Car Details
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    echo "<div class='container mt-3'><h2>Car not found.</h2></div>";
    include 'partials/footer.php';
    exit;
}

// Fetch Gallery Images
$stmtV = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ?");
$stmtV->execute([$id]);
$images = $stmtV->fetchAll();

// Fetch 360 Frames
$stmt360 = $pdo->prepare("SELECT * FROM car_360_frames WHERE car_id = ? ORDER BY frame_no ASC");
$stmt360->execute([$id]);
$frames = $stmt360->fetchAll();
?>

<div class="container mt-2">
    <!-- Breadcrumb -->
    <p style="opacity: 0.7; margin-bottom: 20px;"><a href="index.php">Home</a> <span style="margin: 0 10px; color: var(--primary-color);">></span> <a href="cars.php">Cars</a> <span style="margin: 0 10px; color: var(--primary-color);">></span> <span style="color: var(--text-color);"><?= htmlspecialchars($car['name']) ?></span></p>

    <div class="grid grid-2" style="grid-template-columns: 2fr 1fr; align-items: start; gap: 30px;">
        
        <!-- Left Column: Visuals -->
        <div>
            <!-- Main Gallery Swiper -->
            <div class="swiper-container gallery-top" style="height: 500px; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-lg);">
                <div class="swiper-wrapper">
                    <?php if(count($images) > 0): ?>
                        <?php foreach($images as $img): ?>
                            <div class="swiper-slide">
                                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="Car Image" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Placeholder -->
                        <div class="swiper-slide">
                            <img src="https://images.unsplash.com/photo-1542362567-b07e54358753?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Custom Navigation Buttons if needed, or use default swiper ones styled in CSS -->
                <div class="swiper-button-next" style="color: white;"></div>
                <div class="swiper-button-prev" style="color: white;"></div>
            </div>

            <?php
            // Extract YouTube video URL from description if stored as [VIDEO:url]
            $videoUrl = '';
            $videoEmbed = '';
            if (preg_match('/\[VIDEO:(.*?)\]/', $car['description'] ?? '', $m)) {
                $videoUrl = trim($m[1]);
                // Convert watch URL to embed
                if (preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $vid)) {
                    $videoEmbed = 'https://www.youtube.com/embed/' . $vid[1] . '?autoplay=0&rel=0';
                }
            }
            ?>

            <?php if ($videoEmbed): ?>
            <!-- YouTube Video Section -->
            <div class="mt-2 text-center">
                <h3 style="margin-bottom:12px;color:var(--primary-color);">Watch in Action</h3>
                <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:var(--radius);box-shadow:var(--shadow-lg);">
                    <iframe src="<?= htmlspecialchars($videoEmbed) ?>"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
            <?php endif; ?>

            <?php if(count($frames) >= 12): ?>
            <!-- ── TRUE 360° Viewer (only when 12+ sequential frames exist) ── -->
            <div class="mt-2 text-center">
                <h3 style="margin-bottom:15px;color:var(--primary-color);">360&deg; Experience</h3>
                <div class="card card-glass" style="padding:10px;overflow:hidden;position:relative;">
                    <div id="viewer-360" style="width:100%;height:420px;background:radial-gradient(circle,#2a2a2a,#000);position:relative;cursor:grab;overflow:hidden;border-radius:var(--radius);">
                        <div id="loader-360" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:var(--primary-color);text-align:center;">
                            <i class="fas fa-spinner fa-spin fa-3x"></i>
                            <div style="margin-top:10px;font-size:0.75rem;color:rgba(255,255,255,0.5);">Loading…</div>
                        </div>
                        <div id="err-360" style="display:none;position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:rgba(255,255,255,0.5);text-align:center;">
                            <i class="fas fa-exclamation-circle fa-2x"></i><div style="margin-top:8px;font-size:0.8rem;">Frames unavailable</div>
                        </div>
                        <img id="current-frame" src="<?= htmlspecialchars($frames[0]['image_path']) ?>"
                             style="width:100%;height:100%;object-fit:contain;pointer-events:none;opacity:0;transition:opacity 0.4s;">
                        <div style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.6);color:#fff;padding:7px 18px;border-radius:30px;backdrop-filter:blur(5px);border:1px solid rgba(255,255,255,0.1);pointer-events:none;font-size:0.85rem;">
                            <i class="fas fa-arrows-alt-h" style="color:var(--primary-color);"></i> Drag to Rotate
                        </div>
                    </div>
                </div>
            </div>

            <?php elseif(count($images) > 1): ?>
            <!-- ── Premium Auto-Showcase (when no 360 frame data) ── -->
            <div class="mt-2">
                <h3 style="margin-bottom:12px;color:var(--primary-color);text-align:center;">Gallery Showcase</h3>
                <div id="showcase" style="position:relative;width:100%;height:380px;overflow:hidden;border-radius:var(--radius);box-shadow:var(--shadow-lg);background:#111;">
                    <?php foreach($images as $i => $img): ?>
                    <div class="sc-slide" style="position:absolute;inset:0;opacity:<?= $i===0?'1':'0' ?>;transition:opacity 1s ease;z-index:<?= $i===0?'2':'1' ?>;">
                        <img src="<?= htmlspecialchars($img['image_path'] ?? $img['image_url'] ?? '') ?>"
                             style="width:100%;height:100%;object-fit:cover;transform:scale(1.06);transition:transform 6s ease;"
                             alt="<?= htmlspecialchars($car['name']) ?>">
                    </div>
                    <?php endforeach; ?>
                    <!-- Dots -->
                    <div style="position:absolute;bottom:14px;left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:10;">
                        <?php foreach($images as $i => $_): ?>
                        <div class="sc-dot" data-i="<?= $i ?>" style="width:7px;height:7px;border-radius:50%;background:<?= $i===0?'#fff':'rgba(255,255,255,0.4)' ?>;cursor:pointer;transition:background 0.3s;"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Specs Tabs -->
            <div class="mt-2 card card-glass">
                <h3 style="border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px;">Specifications</h3>
                <div class="grid grid-2" style="row-gap: 20px;">
                    <p><strong style="color: var(--primary-color);">Engine:</strong> <br> <?= $car['engine_cc'] ?? '3.0L TwinPower Turbo' ?></p>
                    <p><strong style="color: var(--primary-color);">Power:</strong> <br> <?= $car['power_hp'] ?? '375 HP' ?></p>
                    <p><strong style="color: var(--primary-color);">0-60 mph:</strong> <br> 4.2s</p>
                    <p><strong style="color: var(--primary-color);">Top Speed:</strong> <br> 155 mph</p>
                    <p><strong style="color: var(--primary-color);">Transmission:</strong> <br> <?= $car['transmission'] ?></p>
                    <p><strong style="color: var(--primary-color);">Fuel Type:</strong> <br> <?= $car['fuel_type'] ?></p>
                </div>
            </div>
        </div>

        <!-- Right Column: Details & Actions -->
        <div class="card card-glass" style="position: sticky; top: 100px; padding: 30px;">
            <h1 style="margin-bottom: 5px; font-size: 2.2rem;"><?= htmlspecialchars($car['name']) ?></h1>
            <p class="text-muted" style="margin-bottom: 20px;"><?= htmlspecialchars($car['brand']) ?> Collection</p>
            
            <h2 style="color: var(--primary-color); font-size: 2.5rem; margin-bottom: 25px;"><?= formatPrice($car['price']) ?></h2>
            
            <hr style="border: 0; border-top: 1px solid var(--border-color); opacity: 0.5; margin: 25px 0;">
            
            <a href="test-drive.php?car_id=<?= $car['id'] ?>" class="btn btn-primary" style="width: 100%; text-align: center; margin-bottom: 10px; padding: 15px;">Book Test Drive</a>
            <a href="book-car.php?car_id=<?= $car['id'] ?>" class="btn" style="width: 100%; text-align: center; margin-bottom: 15px; padding: 15px; background: #28a745; color: #fff; border: 1px solid #28a745; font-size: 1.1rem; border-radius: 6px;"><i class="fas fa-check-circle"></i> Book Car Online ($1,000 Deposit)</a>
            
            <div class="d-flex justify-content-between mb-1" style="gap: 10px;">
                <button class="btn btn-outline" style="flex: 1; font-size: 0.85rem;"><i class="far fa-heart"></i> Wishlist</button>
                <button class="btn btn-outline" style="flex: 1; font-size: 0.85rem;"><i class="fas fa-exchange-alt"></i> Compare</button>
            </div>
            
            <!-- EMI Calculator -->
            <div class="mt-2" style="background: rgba(0,0,0,0.2); padding: 20px; border-radius: var(--radius); border: 1px solid var(--border-color);">
                <h4 style="margin-bottom: 15px; color: var(--text-light);">EMI Calculator</h4>
                <div class="mt-1">
                    <label style="font-size: 0.85rem; color: var(--text-muted);">Loan Amount (₹)</label>
                    <input type="number" id="loan-amount" value="<?= $car['price'] ?>" style="width: 100%; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); padding: 8px; border-radius: 6px;">
                </div>
                <div class="mt-1">
                    <label style="font-size: 0.85rem; color: var(--text-muted);">Interest Rate (%)</label>
                    <input type="number" id="interest-rate" value="5.5" style="width: 100%; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); padding: 8px; border-radius: 6px;">
                </div>
                <div class="mt-1">
                    <label style="font-size: 0.85rem; color: var(--text-muted);">Months: <span id="months-val" style="color: var(--primary-color);">60</span></label>
                    <input type="range" id="loan-months" min="12" max="84" value="60" oninput="document.getElementById('months-val').innerText = this.value" style="width: 100%; accent-color: var(--primary-color);">
                </div>
                <h3 class="text-center mt-2" style="color: var(--primary-color);" id="emi-result">₹0 / month</h3>
            </div>
        </div>
    </div>
</div>

<script>
    // EMI Calculator Logic
    function calculateEMI() {
        const principal = document.getElementById('loan-amount').value;
        const rate = document.getElementById('interest-rate').value / 12 / 100;
        const months = document.getElementById('loan-months').value;
        
        if (principal && rate && months) {
            const x = Math.pow(1 + rate, months);
            const monthly = (principal * x * rate) / (x - 1);
            document.getElementById('emi-result').innerText = '₹' + monthly.toFixed(2) + ' / month';
        }
    }
    
    document.querySelectorAll('#loan-amount, #interest-rate, #loan-months').forEach(input => {
        input.addEventListener('input', calculateEMI);
    });
    calculateEMI(); // Init

    // ── Gallery Showcase Auto-Cycle ─────────────────────────
    (function() {
        const showcase = document.getElementById('showcase');
        if (!showcase) return;
        const slides = showcase.querySelectorAll('.sc-slide');
        const dots   = showcase.querySelectorAll('.sc-dot');
        if (slides.length < 2) return;
        let cur = 0, timer;

        const goTo = (n) => {
            slides[cur].style.opacity = '0';
            slides[cur].style.zIndex  = '1';
            dots[cur].style.background = 'rgba(255,255,255,0.4)';
            cur = (n + slides.length) % slides.length;
            slides[cur].style.opacity = '1';
            slides[cur].style.zIndex  = '2';
            dots[cur].style.background = '#fff';
            // Trigger Ken Burns on the active slide's img
            const img = slides[cur].querySelector('img');
            img.style.transform = 'scale(1)';
            setTimeout(() => { img.style.transform = 'scale(1.06)'; }, 50);
        };

        const start = () => { timer = setInterval(() => goTo(cur + 1), 4000); };
        const stop  = () => { clearInterval(timer); };

        dots.forEach(d => d.addEventListener('click', () => { stop(); goTo(+d.dataset.i); start(); }));
        showcase.addEventListener('mouseenter', stop);
        showcase.addEventListener('mouseleave', start);
        start();
    })();

    // 360 Viewer Logic (Enhanced)
    <?php if(count($frames) > 0): ?>
    const viewer = document.getElementById('viewer-360');
    const frameImg = document.getElementById('current-frame');
    const loader = document.getElementById('loader-360');
    // PHP Array to JS Array
    const frames = <?= json_encode(array_column($frames, 'image_path')) ?>;
    
    // Preload Images — with error handling + 5s timeout
    const show360Error = () => {
        document.getElementById('loader-360').style.display = 'none';
        document.getElementById('err-360').style.display = 'block';
    };

    const preloadImages = () => {
        let loadedCount = 0;
        let failed = 0;
        const total = frames.length;

        // Hard timeout: if not all loaded in 5s, show error
        const timeout = setTimeout(() => {
            document.getElementById('loader-360').style.display = 'none';
            if (failed === total) {
                show360Error();
            } else {
                // Some loaded — proceed with what we have
                document.getElementById('loader-360').style.display = 'none';
                document.getElementById('current-frame').style.opacity = '1';
                startAutoRotate();
            }
        }, 5000);

        frames.forEach(src => {
            const img = new Image();
            img.src = src;
            img.onload = () => {
                loadedCount++;
                if (loadedCount + failed === total) {
                    clearTimeout(timeout);
                    if (failed === total) { show360Error(); return; }
                    document.getElementById('loader-360').style.display = 'none';
                    document.getElementById('current-frame').style.opacity = '1';
                    startAutoRotate();
                }
            };
            img.onerror = () => {
                failed++;
                if (loadedCount + failed === total) {
                    clearTimeout(timeout);
                    if (failed === total) { show360Error(); return; }
                    document.getElementById('loader-360').style.display = 'none';
                    document.getElementById('current-frame').style.opacity = '1';
                    startAutoRotate();
                }
            };
        });
    };
    
    let currentFrameIdx = 0;
    let isDragging = false;
    let startX = 0;
    let autoRotateInterval;

    // Auto Rotate Function
    const startAutoRotate = () => {
        stopAutoRotate(); // clear any existing
        autoRotateInterval = setInterval(() => {
            currentFrameIdx = (currentFrameIdx + 1) % frames.length;
             requestAnimationFrame(() => {
                 frameImg.src = frames[currentFrameIdx];
             });
        }, 100); // Speed of rotation
    };

    const stopAutoRotate = () => {
        clearInterval(autoRotateInterval);
    };

    preloadImages();

    viewer.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.clientX;
        viewer.style.cursor = 'grabbing';
        stopAutoRotate(); // Stop on interaction
    });

    window.addEventListener('mouseup', () => {
        isDragging = false;
        viewer.style.cursor = 'grab';
        // Optional: Resume auto-rotate after delay?
        // setTimeout(startAutoRotate, 3000); 
    });

    window.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        const dx = e.clientX - startX;
        
        // Sensitivity
        if (Math.abs(dx) > 5) { 
            if (dx > 0) {
                // Drag Right -> Rotate Left (Show previous frames)
                currentFrameIdx = (currentFrameIdx - 1 + frames.length) % frames.length;
            } else {
                // Drag Left -> Rotate Right (Show next frames)
                currentFrameIdx = (currentFrameIdx + 1) % frames.length;
            }
            requestAnimationFrame(() => {
                frameImg.src = frames[currentFrameIdx];
            });
            startX = e.clientX;
        }
    });
    
    // Touch Events for Mobile
    viewer.addEventListener('touchstart', (e) => {
        isDragging = true;
        startX = e.touches[0].clientX;
        stopAutoRotate();
    });

    window.addEventListener('touchend', () => {
        isDragging = false;
    });

    window.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        const dx = e.touches[0].clientX - startX;
        if (Math.abs(dx) > 5) {
             if (dx > 0) {
                currentFrameIdx = (currentFrameIdx - 1 + frames.length) % frames.length;
            } else {
                currentFrameIdx = (currentFrameIdx + 1) % frames.length;
            }
            requestAnimationFrame(() => {
                frameImg.src = frames[currentFrameIdx];
            });
            startX = e.touches[0].clientX;
        }
    });
    <?php endif; ?>
</script>

<?php include 'partials/footer.php'; ?>
