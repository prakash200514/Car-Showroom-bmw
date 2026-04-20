<?php
include 'config/database.php';
$cats = $pdo->query("SELECT * FROM spare_categories")->fetchAll(PDO::FETCH_ASSOC);
$spares = $pdo->query("SELECT * FROM spares")->fetchAll(PDO::FETCH_ASSOC);
echo "Categories:\n";
print_r($cats);
echo "\nSpares:\n";
print_r($spares);
?>
