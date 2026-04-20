<?php
include 'config/database.php';
include 'includes/functions.php';

// Fetch the 6 most recently added cars
$stmt = $pdo->query("SELECT c.*, 
                    (SELECT image_path FROM car_images WHERE car_id = c.id ORDER BY is_primary DESC, id ASC LIMIT 1) as primary_image 
                    FROM cars c 
                    ORDER BY c.id DESC LIMIT 6");
$new_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'partials/header.php';
?>

<style>
/* New Launch Page Specific Styles */
.nl-hero {
    position: relative;
    width: 100%;
    min-height: 50vh;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 100px 20px;
    text-align: center;
}
.nl-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1555529733-0e67056058ab?auto=format&fit=crop&q=80&w=2000') center/cover;
    z-index: 0;
    opacity: 0.6;
}
.nl-hero-content {
    position: relative;
    z-index: 1;
    color: #fff;
    max-width: 800px;
}
.nl-hero-content h1 {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 900;
    letter-spacing: -0.02em;
    margin-bottom: 20px;
    text-transform: uppercase;
}
.nl-hero-content p {
    font-size: clamp(1.1rem, 2vw, 1.3rem);
    color: #ccc;
    line-height: 1.6;
}

.nl-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 40px;
    padding: 60px 5%;
    background: var(--bg-color, #fff);
}

.nl-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #eee;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
}
.nl-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}
.nl-card-img-wrap {
    position: relative;
    width: 100%;
    height: 220px;
    overflow: hidden;
    background: #111;
}
.nl-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.nl-card:hover .nl-card-img {
    transform: scale(1.05);
}
.nl-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: var(--bmw-blue, #1c6bba);
    color: white;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 6px 12px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}
.nl-card-body {
    padding: 25px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.nl-card-title {
    font-size: 1.4rem;
    font-weight: 800;
    margin-bottom: 10px;
    color: #111;
}
.nl-card-subtitle {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 20px;
}
.nl-card-specs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
}
.nl-card-spec {
    font-size: 0.85rem;
    color: #444;
    display: flex;
    align-items: center;
    gap: 8px;
}
.nl-card-footer {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #eee;
    padding-top: 20px;
}
.nl-card-price {
    font-size: 1.2rem;
    font-weight: 800;
    color: #111;
}
.nl-card-btn {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--bmw-blue, #1c6bba);
    text-transform: uppercase;
    letter-spacing: 1px;
}
</style>

<div class="nl-hero">
    <div class="nl-hero-content" data-aos="fade-up">
        <h1>New Arrivals</h1>
        <p>Discover the latest models and innovations freshly added to our prestigious BMW showroom collection.</p>
    </div>
</div>

<div class="nl-grid">
    <?php if (count($new_cars) > 0): ?>
        <?php foreach ($new_cars as $car): ?>
            <a href="car-reveal.php?id=<?= $car['id'] ?>" class="nl-card" data-aos="fade-up">
                <div class="nl-card-img-wrap">
                    <span class="nl-badge">New Launch</span>
                    <?php 
                        $imgSrc = $car['primary_image'] ? $car['primary_image'] : 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=600&q=80';
                    ?>
                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($car['name']) ?>" class="nl-card-img">
                </div>
                <div class="nl-card-body">
                    <h3 class="nl-card-title"><?= htmlspecialchars($car['name']) ?></h3>
                    <div class="nl-card-subtitle"><?= htmlspecialchars($car['body_type']) ?> | <?= htmlspecialchars($car['fuel_type']) ?></div>
                    
                    <div class="nl-card-specs">
                        <div class="nl-card-spec"><i class="fas fa-tachometer-alt" style="color:var(--bmw-blue);"></i> <?= htmlspecialchars($car['power_hp']) ?> HP</div>
                        <div class="nl-card-spec"><i class="fas fa-gas-pump" style="color:var(--bmw-blue);"></i> <?= htmlspecialchars($car['mileage']) ?></div>
                        <div class="nl-card-spec"><i class="fas fa-cogs" style="color:var(--bmw-blue);"></i> <?= htmlspecialchars($car['transmission']) ?></div>
                        <div class="nl-card-spec"><i class="fas fa-calendar-alt" style="color:var(--bmw-blue);"></i> <?= htmlspecialchars($car['year']) ?></div>
                    </div>

                    <div class="nl-card-footer">
                        <div class="nl-card-price">₹<?= number_format($car['price'], 2) ?></div>
                        <div class="nl-card-btn">Explore <i class="fas fa-arrow-right"></i></div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 100px 20px;">
            <i class="fas fa-car" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
            <h3 style="color: #666;">No new launches at the moment.</h3>
            <p style="color: #999;">Check back soon for the latest BMW models!</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'partials/footer.php'; ?>
