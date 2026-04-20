<?php
session_start();
include 'config/database.php';
include 'includes/functions.php';

// Fetch Categories
$stmtCategories = $pdo->query("SELECT * FROM spare_categories");
$categories = $stmtCategories->fetchAll();

// Handle Search and Category Filter
$categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$search = trim($_GET['search'] ?? '');

$query = "SELECT s.*, c.category_name 
          FROM spares s 
          LEFT JOIN spare_categories c ON s.category_id = c.id 
          WHERE 1=1";
$params = [];

if ($categoryId > 0) {
    $query .= " AND s.category_id = ?";
    $params[] = $categoryId;
}
if ($search !== '') {
    $query .= " AND (s.name LIKE ? OR s.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmtProducts = $pdo->prepare($query);
$stmtProducts->execute($params);
$products = $stmtProducts->fetchAll();

include 'partials/header.php';
?>

<link rel="stylesheet" href="bmw-spares.css">

<body class="spares-page">

    <!-- Hero Section -->
    <section class="sp-hero">
        <div class="sp-container">
            <div class="sp-hero__content">
                <h1>Original BMW Car Spares & Accessories</h1>
                <p>Find genuine BMW spare parts, performance accessories, interior upgrades, and maintenance essentials for your luxury drive.</p>
                <div class="sp-hero__actions">
                    <a href="#featured" class="sp-btn sp-btn--primary">Shop Now</a>
                    <a href="#categories" class="sp-btn sp-btn--outline">View Categories</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Search & Filter -->
    <div class="sp-search-bar">
        <div class="sp-container">
            <form class="sp-search-flex" method="GET" action="bmw-spares.php#featured">
                <input type="text" name="search" class="sp-input" placeholder="Search BMW spare parts..." value="<?= htmlspecialchars($search) ?>">
                <select name="category_id" class="sp-select" onchange="this.form.submit()">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="sp-btn sp-btn--primary" style="padding:12px 20px;">Search</button>
            </form>
        </div>
    </div>

    <!-- Product Categories -->
    <section id="categories" class="sp-section" style="background:var(--sp-dark);">
        <div class="sp-container">
            <div class="sp-section-head">
                <h2>Browse by Category</h2>
                <p>Discover our extensive range of genuine BMW parts categorized for your convenience.</p>
            </div>
            
            <div class="sp-category-grid">
                <?php 
                // We'll dynamically show DB categories if they exist, or fallback to icons.
                $icons = [
                    'Engine Parts' => 'fa-cogs',
                    'Brake System' => 'fa-compact-disc',
                    'Lighting' => 'fa-lightbulb',
                    'Wheels & Tyres' => 'fa-car-side',
                    'Interior Covers' => 'fa-chair',
                    'Floor Mats' => 'fa-layer-group',
                ];
                
                if (count($categories) > 0): 
                    foreach ($categories as $cat): 
                        $iconClass = $icons[$cat['category_name']] ?? 'fa-tools';
                ?>
                    <div class="sp-glass-card sp-cat-card">
                        <div class="sp-cat-icon-container"><i class="fas <?= $iconClass ?>"></i></div>
                        <h3><?= htmlspecialchars($cat['category_name']) ?></h3>
                        <p>Explore genuine parts for your BMW.</p>
                        <a href="bmw-spares.php?category_id=<?= $cat['id'] ?>#featured" class="sp-btn sp-btn--outline">Explore</a>
                    </div>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <p style="text-align:center; color:var(--sp-text-muted);">No categories found in the database. Please add some from the admin panel.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="featured" class="sp-section" style="background:#080808;">
        <div class="sp-container">
            <div class="sp-section-head">
                <h2>Featured BMW Spares</h2>
                <p>Top-rated genuine auto parts loved by BMW enthusiasts.</p>
                <div style="margin-top:20px;">
                    <a href="cart.php" class="sp-btn sp-btn--outline"><i class="fas fa-shopping-cart"></i> View My Cart</a>
                </div>
            </div>
            
            <div class="sp-product-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $prod): ?>
                    <div class="sp-glass-card sp-prod-card">
                        <div class="sp-prod-img-wrap">
                            <?php if ($prod['stock_qty'] > 0): ?>
                                <span class="sp-prod-badge" style="background:var(--sp-blue);">In Stock</span>
                            <?php else: ?>
                                <span class="sp-prod-badge" style="background:#b30000;">Out of Stock</span>
                            <?php endif; ?>
                            
                            <?php 
                                $imgSrc = $prod['image'] ?: 'https://images.unsplash.com/photo-1621685800588-433e5ab7dc10?w=500&q=80'; 
                            ?>
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" class="sp-prod-img">
                        </div>
                        <div class="sp-prod-body">
                            <h4 class="sp-prod-title"><?= htmlspecialchars($prod['name']) ?></h4>
                            <p class="sp-prod-desc"><?= htmlspecialchars(substr($prod['description'] ?? '', 0, 80)) ?>...</p>
                            <div class="sp-prod-price">₹<?= number_format($prod['price'], 2) ?></div>
                            <div class="sp-prod-actions">
                                <?php if ($prod['stock_qty'] > 0): ?>
                                    <button class="sp-btn--cart add-to-cart-btn" data-id="<?= $prod['id'] ?>" data-name="<?= htmlspecialchars($prod['name']) ?>">Add to Cart</button>
                                <?php else: ?>
                                    <button class="sp-btn--cart" disabled style="background:#333; color:#888; cursor:not-allowed;">Unavailable</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align:center; grid-column: 1 / -1; color:var(--sp-text-muted);">No products found. Please try a different category or search term.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Special Offer Banner -->
    <section class="sp-offer-banner">
        <div class="sp-container">
            <h2>Get up to 25% off on selected BMW spare parts</h2>
            <p style="color:#e0e0e0; font-size:1.1rem; margin-bottom:30px;">Upgrade your drive this season with genuine M-Performance accessories.</p>
            <a href="bmw-spares.php#featured" class="sp-btn sp-btn--outline" style="border-width:2px;">Buy Now</a>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="sp-section" style="background:var(--sp-darker);">
        <div class="sp-container">
            <div class="sp-section-head">
                <h2>Why Choose Us</h2>
                <p>We provide the ultimate BMW assurance with every single part you purchase.</p>
            </div>
            <div class="sp-features-grid">
                <div class="sp-feature-box">
                    <i class="fas fa-check-circle"></i>
                    <h4>Genuine BMW Quality</h4>
                    <p>100% authentic parts sourced directly from BMW manufacturing facilities.</p>
                </div>
                <div class="sp-feature-box">
                    <i class="fas fa-shipping-fast"></i>
                    <h4>Fast Delivery</h4>
                    <p>Expedited shipping options globally so you don't wait for your upgrades.</p>
                </div>
                <div class="sp-feature-box">
                    <i class="fas fa-lock"></i>
                    <h4>Secure Payment</h4>
                    <p>Military-grade encrypted transactions for absolute peace of mind.</p>
                </div>
                <div class="sp-feature-box">
                    <i class="fas fa-undo"></i>
                    <h4>Easy Return Policy</h4>
                    <p>Hassle-free 30 days return guarantee on all unused spare parts.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'partials/footer.php'; ?>

    <!-- CSS for Spares UI Elements -->
    <style>
    /* Toast Notification */
    .sp-toast {
        position: fixed;
        bottom: -100px;
        right: 20px;
        background: var(--bmw-blue, #1c6bba);
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: bold;
        z-index: 2147483647;
        transition: bottom 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    .sp-toast.show {
        bottom: 20px;
    }
    </style>

    <!-- JS for AJAX Cart Add -->
    <script src="bmw-spares.js?v=<?= time() ?>"></script>
</body>
</html>
