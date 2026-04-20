<?php
// seed_spares.php
include 'config/database.php';

echo "Seeding categories and spares...<br>";

// Pre-fill categories
$categories = ['Engine Parts', 'Brake System', 'Lighting', 'Wheels & Tyres', 'Interior Covers', 'Floor Mats'];
foreach($categories as $cat) {
    // check if it exists
    $stmt = $pdo->prepare("SELECT id FROM spare_categories WHERE category_name = ?");
    $stmt->execute([$cat]);
    if(!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO spare_categories (category_name) VALUES (?)")->execute([$cat]);
    }
}

// Get category IDs
$cats = $pdo->query("SELECT id, category_name FROM spare_categories")->fetchAll(PDO::FETCH_KEY_PAIR);
$catsArr = array_flip($cats);

// Dummy products
$products = [
    ['name' => 'BMW Premium Brake Pad Set', 'category' => 'Brake System', 'price' => 249.00, 'part' => 'BRK-M5-001', 'qty' => 15, 'img' => 'https://images.unsplash.com/photo-1621685800588-433e5ab7dc10?w=500&q=80', 'desc' => 'Original M-Sport high performance front brake pads for X5 series.'],
    ['name' => 'BMW Laserlight Assembly', 'category' => 'Lighting', 'price' => 1299.00, 'part' => 'LGT-LSR-02', 'qty' => 5, 'img' => 'https://images.unsplash.com/photo-1549399542-7e3f8b79c341?w=500&q=80', 'desc' => 'Adaptive LED headlight assembly for 3 Series & M3.'],
    ['name' => '21" M Light Alloy Wheels', 'category' => 'Wheels & Tyres', 'price' => 899.00, 'part' => 'WHL-21-M', 'qty' => 8, 'img' => 'https://images.unsplash.com/photo-1634812328117-6ffefd1c16cf?w=500&q=80', 'desc' => 'Double-spoke style 754 M Bicolor standard wheels.'],
    ['name' => 'M Performance Carbon Engine Cover', 'category' => 'Engine Parts', 'price' => 649.00, 'part' => 'ENG-CBN-99', 'qty' => 3, 'img' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=500&q=80', 'desc' => 'Lightweight carbon fiber engine bay cover plate.']
];

foreach($products as $p) {
    if(!isset($catsArr[$p['category']])) continue;
    $catId = $catsArr[$p['category']];
    
    // check if product exists
    $stmt = $pdo->prepare("SELECT id FROM spares WHERE name = ?");
    $stmt->execute([$p['name']]);
    if(!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO spares (name, category_id, price, stock_qty, description, image, part_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$p['name'], $catId, $p['price'], $p['qty'], $p['desc'], $p['img'], $p['part']]);
        echo "Inserted {$p['name']} <br>";
    }
}

echo "Done!";
?>
