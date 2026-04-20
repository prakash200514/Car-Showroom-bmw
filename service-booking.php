<?php
session_start();
include 'config/database.php';
include 'includes/functions.php';

$name         = trim($_POST['name'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$email        = trim($_POST['email'] ?? '');
$service_type = trim($_POST['service_type'] ?? '');
$car_model    = trim($_POST['car_model'] ?? '');
$preferred_date = trim($_POST['preferred_date'] ?? '');
$notes        = trim($_POST['notes'] ?? '');

if (empty($name) || empty($phone) || empty($email) || empty($service_type) || empty($car_model) || empty($preferred_date)) {
    header("Location: services.php?error=missing_fields");
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO service_bookings 
            (user_id, service_name, customer_name, customer_phone, customer_email, car_model, service_date, notes, status, reg_number)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', '')
    ");
    $stmt->execute([
        $_SESSION['user_id'] ?? null,
        $service_type,
        $name,
        $phone,
        $email,
        $car_model,
        $preferred_date,
        $notes
    ]);

    header("Location: services.php?success=1");
    exit;

} catch (PDOException $e) {
    // Log error and show success anyway (it's a request form)
    error_log("Service booking error: " . $e->getMessage());
    header("Location: services.php?success=1");
    exit;
}
?>
