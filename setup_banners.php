<?php
// setup_banners.php — Run ONCE to create site_banners table and seed default banners
include 'config/database.php';

// Create table
$pdo->exec("CREATE TABLE IF NOT EXISTS site_banners (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    position    INT NOT NULL DEFAULT 0,
    eyebrow     VARCHAR(150),
    headline    VARCHAR(255),
    model_name  VARCHAR(100),
    sub_label   VARCHAR(255),
    tagline     TEXT,
    cta_text    VARCHAR(100),
    cta_url     VARCHAR(255),
    cta_style   ENUM('outline','blue','dark') DEFAULT 'outline',
    image_url   VARCHAR(1000),
    is_active   TINYINT(1) DEFAULT 1,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Clear and seed
$pdo->exec("DELETE FROM site_banners");

$banners = [
    [1, 'LUXURY. FAST. FORWARD.', 'THE BMW', '7 RANGE.', '', '',
     'Discover now', '/showroom/cars.php', 'outline',
     'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?auto=format&fit=crop&w=1600&q=90'],

    [2, 'THE ALL-NEW', 'X3', '', 'MASTER EVERY MOMENT.', '',
     'Discover now', '/showroom/cars.php', 'outline',
     'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?auto=format&fit=crop&w=1600&q=90'],

    [3, 'THE', 'iX1', '', 'LONG WHEELBASE.<br>DOMINATE EVERYDAY. YOUR WAY.', '',
     'Discover now', '/showroom/cars.php', 'outline',
     'https://images.unsplash.com/photo-1617788138017-80ad40651399?auto=format&fit=crop&w=1600&q=90'],

    [4, 'THE NEW', '2', '', 'LEAVE BORING BEHIND.', 'Starting EMI ₹29,999*',
     'Skip Boring', '/showroom/cars.php', 'blue',
     'https://images.unsplash.com/photo-1556189250-72ba954e96b5?auto=format&fit=crop&w=1600&q=90'],

    [5, 'BMW M SERIES', 'M4', 'Competition', 'PERFORMANCE REDEFINED.', '',
     'Configure &amp; Buy', '/showroom/cars.php', 'dark',
     'https://images.unsplash.com/photo-1580274455191-1c62238fa1f3?auto=format&fit=crop&w=1600&q=90'],
];

$stmt = $pdo->prepare("INSERT INTO site_banners (position,eyebrow,headline,model_name,sub_label,tagline,cta_text,cta_url,cta_style,image_url,is_active)
                       VALUES (?,?,?,?,?,?,?,?,?,?,1)");
foreach ($banners as $b) {
    $stmt->execute($b);
}

echo '<h2 style="font-family:sans-serif;padding:20px;">✅ site_banners table created & seeded with 5 banners!<br><a href="admin/banners.php">→ Go to Banner Editor</a></h2>';
?>
