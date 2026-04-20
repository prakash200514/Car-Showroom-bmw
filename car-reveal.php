<?php 
include 'partials/header.php'; 
include 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    echo "<div class='container mt-5 py-5 text-center'><h2>Model not found.</h2><a href='index.php' class='btn btn-outline-dark mt-3'>Back to Home</a></div>";
    include 'partials/footer.php';
    exit;
}

// Fetch Gallery Images
$stmtV = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY is_primary DESC, id ASC");
$stmtV->execute([$id]);
$images = $stmtV->fetchAll();

$videoUrl = '';
$videoEmbed = '';
if (preg_match('/\[VIDEO:(.*?)\]/', $car['description'] ?? '', $m)) {
    $videoUrl = trim($m[1]);
    if (preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $vid)) {
        $videoEmbed = 'https://www.youtube.com/embed/' . $vid[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $vid[1] . '&rel=0&controls=0&showinfo=0';
    }
}
?>

<style>
/* ── Premium Reveal Storytelling Styles ── */
:root {
    --bmw-blue: #1c69d4;
    --text-main: #262626;
    --text-muted: #666;
}

body {
    background: #fff;
    color: var(--text-main);
    font-family: 'BMW Type Next', 'Inter', sans-serif;
}

.reveal-container {
    overflow-x: hidden;
}

/* Hero Section */
.reveal-hero {
    position: relative;
    height: 90vh;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

.reveal-hero__img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.8;
}

.reveal-hero__content {
    position: relative;
    z-index: 10;
    text-align: center;
}

.reveal-hero__eyebrow {
    font-size: 1.2rem;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    margin-bottom: 20px;
    display: block;
}

.reveal-hero__title {
    font-size: clamp(3.5rem, 10vw, 7rem);
    font-weight: 800;
    text-transform: uppercase;
    margin: 0;
    line-height: 1;
}

/* Storytelling Sections */
.reveal-section {
    padding: 100px 0;
    display: flex;
    align-items: center;
    gap: 80px;
    max-width: 1280px;
    margin: 0 auto;
}

.reveal-section--reverse {
    flex-direction: row-reverse;
}

.reveal-section__image {
    flex: 1;
}

.reveal-section__image img {
    width: 100%;
    height: auto;
    display: block;
}

.reveal-section__content {
    flex: 1;
    max-width: 500px;
}

.reveal-section__title {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 24px;
    line-height: 1.2;
}

.reveal-section__text {
    font-size: 1.15rem;
    color: var(--text-muted);
    line-height: 1.7;
    margin: 0;
}

/* Detail Grid */
.reveal-detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 1280px;
    margin: 40px auto;
    background: #f8f8f8;
}

.reveal-detail-col {
    position: relative;
}

.reveal-detail-col img {
    width: 100%;
    height: 480px;
    object-fit: cover;
}

/* Specs Table */
.reveal-specs {
    background: #f4f4f4;
    padding: 120px 0;
}

.reveal-specs__title {
    text-align: center;
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 60px;
}

.specs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1px;
    background: #ddd;
    max-width: 1200px;
    margin: 0 auto;
}

.specs-item {
    background: #fff;
    padding: 40px;
}

.specs-item h4 {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--text-muted);
    margin-bottom: 12px;
}

.specs-item p {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
}

/* CTA */
.reveal-cta {
    padding: 120px 0;
    text-align: center;
    background: #fff;
}

.reveal-cta h2 {
    font-size: 2.5rem;
    margin-bottom: 40px;
}

.reveal-btn {
    display: inline-block;
    padding: 18px 45px;
    background: #000;
    color: #fff;
    text-decoration: none;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    transition: background 0.3s;
}

.reveal-btn:hover {
    background: var(--bmw-blue);
    color: #fff;
}

@media (max-width: 992px) {
    .reveal-section {
        flex-direction: column !important;
        padding: 60px 20px;
        text-align: center;
    }
    .reveal-section__content {
        max-width: 100%;
    }
    .reveal-detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="reveal-container">
    
    <!-- Hero Section -->
    <section class="reveal-hero">
        <?php if(count($images) > 0): ?>
            <img src="<?= htmlspecialchars($images[0]['image_path']) ?>" class="reveal-hero__img" alt="Hero">
        <?php endif; ?>
        <div class="reveal-hero__content">
            <span class="reveal-hero__eyebrow">The all-new</span>
            <h1 class="reveal-hero__title"><?= htmlspecialchars($car['name']) ?></h1>
        </div>
    </section>

    <!-- Section 1 (Grille/Design) -->
    <section class="reveal-section">
        <div class="reveal-section__image">
            <?php if(!empty($car['img_reveal_1'])): ?>
                <img src="<?= htmlspecialchars($car['img_reveal_1']) ?>" alt="Detail 1">
            <?php elseif(count($images) > 1): ?>
                <img src="<?= htmlspecialchars($images[1]['image_path']) ?>" alt="Detail 1 fallback">
            <?php endif; ?>
        </div>
        <div class="reveal-section__content">
            <h2 class="reveal-section__title"><?= htmlspecialchars($car['reveal_section_1_title'] ?: 'Always unmistakable.') ?></h2>
            <p class="reveal-section__text">
                <?= nl2br(htmlspecialchars($car['reveal_section_1_text'] ?: 'Experience a bold presence that demands attention. High-precision design elements and iconic lines blend seamlessly to create a striking first impression.')) ?>
            </p>
        </div>
    </section>

    <!-- Section 2 (Side/Profile) -->
    <section class="reveal-section reveal-section--reverse">
        <div class="reveal-section__image">
            <?php if(!empty($car['img_reveal_2'])): ?>
                <img src="<?= htmlspecialchars($car['img_reveal_2']) ?>" alt="Detail 2">
            <?php elseif(count($images) > 2): ?>
                <img src="<?= htmlspecialchars($images[2]['image_path']) ?>" alt="Detail 2 fallback">
            <?php endif; ?>
        </div>
        <div class="reveal-section__content">
            <h2 class="reveal-section__title"><?= htmlspecialchars($car['reveal_section_2_title'] ?: 'Sculpted sophistication.') ?></h2>
            <p class="reveal-section__text">
                <?= nl2br(htmlspecialchars($car['reveal_section_2_text'] ?: 'An assertive stance meets artful precision. Contoured lines enhance the sleek silhouette, delivering a visual statement that lingers long after the vehicle has passed.')) ?>
            </p>
        </div>
    </section>

    <!-- Detail Grid (Headlights/Wheels) -->
    <section class="reveal-detail-grid">
        <div class="reveal-detail-col">
            <?php if(!empty($car['img_lights'])): ?>
                <img src="<?= htmlspecialchars($car['img_lights']) ?>" alt="Lights Detail">
            <?php elseif(count($images) > 3): ?>
                <img src="<?= htmlspecialchars($images[3]['image_path']) ?>" alt="Detail 3 fallback">
            <?php endif; ?>
        </div>
        <div class="reveal-detail-col">
            <?php if(!empty($car['img_tyres'])): ?>
                <img src="<?= htmlspecialchars($car['img_tyres']) ?>" alt="Wheels Detail">
            <?php elseif(count($images) > 4): ?>
                <img src="<?= htmlspecialchars($images[4]['image_path']) ?>" alt="Detail 4 fallback">
            <?php endif; ?>
        </div>
    </section>


    <!-- Technical Specs -->
    <section class="reveal-specs">
        <div class="container">
            <h2 class="reveal-specs__title">Technical Specifications.</h2>
            <div class="specs-grid">
                <div class="specs-item"><h4>Engine</h4><p><?= htmlspecialchars($car['engine_cc'] ?: '2998 cc') ?></p></div>
                <div class="specs-item"><h4>Power</h4><p><?= htmlspecialchars($car['power_hp'] ?: '375 hp') ?></p></div>
                <div class="specs-item"><h4>Transmission</h4><p><?= htmlspecialchars($car['spec_gearbox'] ?: $car['transmission']) ?></p></div>
                <div class="specs-item"><h4>Seating</h4><p><?= htmlspecialchars($car['spec_seats'] ?: $car['seats'] . ' Sport Seats') ?></p></div>
                <div class="specs-item"><h4>Lighting</h4><p><?= htmlspecialchars($car['spec_lights'] ?: 'Adaptive LED') ?></p></div>
                <div class="specs-item"><h4>Protection</h4><p><?= htmlspecialchars($car['spec_airbags'] ?: 'Dynamic Airbags') ?></p></div>
                <div class="specs-item"><h4>Safety</h4><p><?= htmlspecialchars($car['spec_safety'] ?: 'Active Guard') ?></p></div>
                <div class="specs-item"><h4>Sound</h4><p><?= htmlspecialchars($car['spec_speakers'] ?: 'HiFi Sound System') ?></p></div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="reveal-cta">
        <div class="container">
            <h2>Ready for the next step?</h2>
            <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;">
                <a href="test-drive.php?car_id=<?= $car['id'] ?>" class="reveal-btn">Book a Test Drive</a>
                <a href="cars.php" class="reveal-btn" style="background:transparent; color:#000; border:1px solid #000;">View All Models</a>
            </div>
        </div>
    </section>

</div>

<?php include 'partials/footer.php'; ?>
