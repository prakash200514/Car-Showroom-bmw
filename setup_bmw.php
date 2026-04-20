<?php
include 'config/database.php';

try {
    // Disable foreign key checks to allow clearing tables
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Clear existing car data
    $pdo->exec("TRUNCATE TABLE cars");
    $pdo->exec("TRUNCATE TABLE car_images");
    $pdo->exec("TRUNCATE TABLE car_variants");
    $pdo->exec("TRUNCATE TABLE car_360_frames");
    
    // Enable foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "Cleared existing car data.<br>";

    // Insert BMW Cars
    $cars = [
        [
            'name' => 'BMW M4 Competition',
            'brand' => 'BMW',
            'price' => 79000.00,
            'body_type' => 'Coupe',
            'transmission' => 'Automatic',
            'fuel_type' => 'Petrol',
            'year' => 2024,
            'engine_cc' => '2993 cc',
            'power_hp' => '503 hp',
            'mileage' => '10 kmpl',
            'seats' => 4,
            'description' => 'The BMW M4 Competition Coupe is the ultimate driving machine. With its 3.0-liter BMW M TwinPower Turbo inline 6-cylinder engine, it delivers 503 hp and 0-60 mph in just 3.8 seconds.',
            'image' => 'https://images.unsplash.com/photo-1617788138017-80ad40651399?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'BMW X5 M',
            'brand' => 'BMW',
            'price' => 105900.00,
            'body_type' => 'SUV',
            'transmission' => 'Automatic',
            'fuel_type' => 'Petrol',
            'year' => 2024,
            'engine_cc' => '4395 cc',
            'power_hp' => '617 hp',
            'mileage' => '8 kmpl',
            'seats' => 5,
            'description' => 'The BMW X5 M is a high-performance SAV that combines luxury with raw power. Verify the dominance of the M TwinPower Turbo V-8 engine.',
            'image' => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'BMW i8 Roadster',
            'brand' => 'BMW',
            'price' => 163300.00,
            'body_type' => 'Convertible',
            'transmission' => 'Automatic',
            'fuel_type' => 'Hybrid',
            'year' => 2023,
            'engine_cc' => '1499 cc',
            'power_hp' => '369 hp',
            'mileage' => '40 kmpl',
            'seats' => 2,
            'description' => 'The BMW i8 Roadster is a plug-in hybrid sports car with gullwing doors and a futuristic design. It defines a new era of sustainable driving.',
            'image' => 'https://images.unsplash.com/photo-1556189250-72ba954e96b5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'BMW 3 Series Gran Limousine',
            'brand' => 'BMW',
            'price' => 45000.00,
            'body_type' => 'Sedan',
            'transmission' => 'Automatic',
            'fuel_type' => 'Diesel',
            'year' => 2024,
            'engine_cc' => '1995 cc',
            'power_hp' => '190 hp',
            'mileage' => '18 kmpl',
            'seats' => 5,
            'description' => 'The BMW 3 Series Gran Limousine offers best-in-class comfort and driving dynamics. Experience the joy of driving.',
            'image' => 'https://images.unsplash.com/photo-1555215695-3004980adade?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'name' => 'BMW Z4 M40i',
            'brand' => 'BMW',
            'price' => 65000.00,
            'body_type' => 'Convertible',
            'transmission' => 'Automatic',
            'fuel_type' => 'Petrol',
            'year' => 2024,
            'engine_cc' => '2998 cc',
            'power_hp' => '335 hp',
            'mileage' => '12 kmpl',
            'seats' => 2,
            'description' => 'The BMW Z4 Roadster is a classic sports car reinterpreted. With its long boonet and short overhangs, it captures the spirit of the open road.',
            'image' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
         [
            'name' => 'BMW 7 Series',
            'brand' => 'BMW',
            'price' => 95000.00,
            'body_type' => 'Sedan',
            'transmission' => 'Automatic',
            'fuel_type' => 'Hybrid',
            'year' => 2024,
            'engine_cc' => '2998 cc',
            'power_hp' => '375 hp',
            'mileage' => '15 kmpl',
            'seats' => 5,
            'description' => 'The BMW 7 Series is the epitome of luxury and innovation. Features a theater screen and executive lounge seating.',
            'image' => 'https://images.unsplash.com/photo-1553440683-1b94dd08f6d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO cars (name, brand, price, body_type, transmission, fuel_type, year, engine_cc, power_hp, mileage, seats, description, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $imgStmt = $pdo->prepare("INSERT INTO car_images (car_id, image_path, is_primary) VALUES (?, ?, 1)");

    foreach ($cars as $car) {
        $stmt->execute([
            $car['name'], $car['brand'], $car['price'], $car['body_type'], 
            $car['transmission'], $car['fuel_type'], $car['year'], 
            $car['engine_cc'], $car['power_hp'], $car['mileage'], 
            $car['seats'], $car['description']
        ]);
        
        $carId = $pdo->lastInsertId();
        
        // Insert Image
        $imgStmt->execute([$carId, $car['image']]);
        
        echo "Inserted: " . $car['name'] . "<br>";
    }
    
    echo "<h1>Database Seeded Successfully with BMW Inventory!</h1>";
    echo "<a href='index.php'>Go to Homepage</a>";

} catch(PDOException $e) {
    die("ERROR: Could not populate database. " . $e->getMessage());
}
?>
