<?php
include 'partials/header.php';
include 'config/database.php';

// Fetch Branches
$stmt = $pdo->query("SELECT * FROM branches");
$branches = $stmt->fetchAll();
?>

<div class="container mt-3">
    <h1 class="text-center mb-1" data-aos="fade-down">Our Showrooms</h1>
    <p class="text-center mb-2">Visit us at one of our premium locations.</p>

    <!-- Map Section -->
    <div class="card mb-2" data-aos="zoom-in" style="height: 400px; padding: 0; overflow: hidden;">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3430.566847842609!2d-96.79698788484914!3d32.77666418097127!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x864e99146f40b2eb%3A0x6bd7212480034606!2sDallas%2C%20TX!5e0!3m2!1sen!2sus!4v1625684674004!5m2!1sen!2sus" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy">
        </iframe>
    </div>

    <!-- Branches Grid -->
    <div class="grid grid-3">
        <?php if(count($branches) > 0): ?>
            <?php foreach($branches as $branch): ?>
                <div class="card" data-aos="fade-up">
                    <img src="<?= htmlspecialchars($branch['image'] ?: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') ?>" alt="<?= htmlspecialchars($branch['name']) ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: var(--radius);">
                    <h3 class="mt-1"><?= htmlspecialchars($branch['name']) ?></h3>
                    <p class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($branch['address']) ?>, <?= htmlspecialchars($branch['city']) ?></p>
                    <p><i class="fas fa-phone"></i> <?= htmlspecialchars($branch['phone']) ?></p>
                    <a href="https://maps.google.com/?q=<?= urlencode($branch['address']) ?>" target="_blank" class="btn btn-outline mt-1" style="width: 100%; text-align: center;">Get Directions</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card text-center" style="grid-column: 1/-1;">
                <h3>No branches found.</h3>
                <p>We are expanding soon!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
