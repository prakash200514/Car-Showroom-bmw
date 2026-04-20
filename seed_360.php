<?php
include 'config/database.php';

try {
    echo "Seeding 360 Frames for BMW M4 (ID: 1)...<br>";
    
    // Clear existing frames for Car 1
    $pdo->exec("DELETE FROM car_360_frames WHERE car_id = 1");
    
    // We will use 8 images to simulate a rotation (approximate angles)
    // In a real scenario, these would be 36 images named 1.jpg to 36.jpg
    $frames = [
        'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&q=80', // Front-ish
        'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80', // Side
        'https://images.unsplash.com/photo-1555215695-3004980adade?w=800&q=80', // Back/Side (different car but for demo)
        // Repeating for smooth loop effect (in a real app, use distinct images)
        'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&q=80', 
         'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80',
         'https://images.unsplash.com/photo-1555215695-3004980adade?w=800&q=80',
         'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&q=80',
         'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80'
    ];

    $stmt = $pdo->prepare("INSERT INTO car_360_frames (car_id, frame_no, image_path) VALUES (?, ?, ?)");
    
    foreach ($frames as $index => $url) {
        $stmt->execute([1, $index + 1, $url]);
        echo "Inserted Frame " . ($index + 1) . "<br>";
    }
    
    echo "<h3>Success! 360 Frames seeded.</h3>";
    echo "<a href='virtual-showroom.php?id=1'>View Virtual Showroom</a>";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
