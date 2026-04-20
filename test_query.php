<?php
include 'config/database.php';
echo "<pre>";

// Check car_images table
$imgs = $pdo->query("DESCRIBE car_images")->fetchAll(PDO::FETCH_ASSOC);
foreach($imgs as $i) echo $i['Field'] . " - " . $i['Type'] . "\n";
echo "</pre>";
