<?php
session_start();
include 'config/database.php';
include 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
    if ($car_id > 0) {
        $_SESSION['redirect_after_login'] = 'book-car.php?car_id=' . $car_id;
    }
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
$error = '';
$success = '';

// Fetch car details
if ($car_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->execute([$car_id]);
    $car = $stmt->fetch();
    
    if (!$car) {
        $error = "The selected car does not exist.";
    }
} else {
    $error = "No car selected for booking.";
}

$booking_amount = 1000.00; // Fixed deposit

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $card_name = $_POST['card_name'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    if (empty($card_name) || empty($card_number) || empty($expiry) || empty($cvv)) {
        $error = "Please fill in all payment details.";
    } else {
        // Mock payment processing
        try {
            // Create car booking record
            $sql = "INSERT INTO car_bookings (user_id, car_id, booking_amount, payment_status, payment_method) 
                    VALUES (?, ?, ?, 'Paid', 'Credit Card')";
            $stmtInsert = $pdo->prepare($sql);
            $stmtInsert->execute([$user_id, $car_id, $booking_amount]);
            $booking_id = $pdo->lastInsertId();
            
            // Store details in session for receipt
            $_SESSION['last_car_booking'] = [
                'booking_id' => 'BKG-' . str_pad($booking_id, 6, "0", STR_PAD_LEFT),
                'date' => date('Y-m-d H:i:s'),
                'car_name' => $car['name'],
                'car_brand' => $car['brand'],
                'car_price' => $car['price'],
                'booking_amount' => $booking_amount,
                'customer_name' => $card_name,
                'status' => 'Confirmed'
            ];
            
            header("Location: car-receipt.php");
            exit;
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage() . " | user_id=" . ($user_id ?? 'NULL') . " | car_id=" . $car_id;
        }
    }
}

include 'partials/header.php';
?>

<div class="container mt-3" style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <!-- Breadcrumb -->
    <p style="opacity: 0.7; margin-bottom: 20px;">
        <a href="index.php">Home</a> <span style="margin: 0 10px; color: var(--primary-color);">></span> 
        <a href="cars.php">Cars</a> <span style="margin: 0 10px; color: var(--primary-color);">></span> 
        <?php if(isset($car)): ?>
            <a href="car-details.php?id=<?= $car['id'] ?>"><?= htmlspecialchars($car['name']) ?></a> <span style="margin: 0 10px; color: var(--primary-color);">></span> 
        <?php endif; ?>
        <span style="color: var(--text-color);">Book Car</span>
    </p>

    <div class="card card-glass" style="padding: 40px;">
        <div class="text-center mb-3">
            <h1 style="font-size: 2.2rem; color: var(--text-color);"><i class="fas fa-car" style="color: var(--primary-color);"></i> Confirm Booking</h1>
            <p class="text-muted mt-1">Secure your <?= isset($car) ? htmlspecialchars($car['name']) : 'car' ?> with a refundable deposit.</p>
        </div>

        <?php if ($error): ?>
            <div style="background: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                <p style="color: #dc3545; margin: 0;"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!isset($error) || $error !== "The selected car does not exist." && $error !== "No car selected for booking."): ?>

            <div class="grid" style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 30px; background: rgba(255,255,255,0.03); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
                <div>
                    <h4 style="margin-bottom: 5px; color: var(--text-light);">Selected Model</h4>
                    <p style="font-size: 1.2rem; font-weight: bold; color: var(--primary-color);"><?= htmlspecialchars($car['name']) ?></p>
                    <p class="text-muted" style="font-size: 0.9rem; margin-top: 5px;">Total Price: <?= formatPrice($car['price']) ?></p>
                </div>
                <div style="text-align: right; display: flex; flex-direction: column; justify-content: center;">
                    <h4 style="color: #28a745; font-size: 1.5rem; margin: 0;">Deposit amount: ₹<?= number_format($booking_amount, 2) ?></h4>
                    <p class="text-muted" style="font-size: 0.85rem; margin-top: 5px;">(To be adjusted against final billing)</p>
                </div>
            </div>

            <form method="POST" action="book-car.php?car_id=<?= $car_id ?>">
                
                <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Payment Details</h3>
                
                <div class="grid grid-2" style="gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="card_name" style="display: block; margin-bottom: 8px; font-weight: 500;">Cardholder Name <span style="color: red;">*</span></label>
                        <input type="text" name="card_name" id="card_name" required placeholder="John Doe" style="width: 100%; padding: 12px; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 6px;">
                    </div>
                    <div>
                        <label for="card_number" style="display: block; margin-bottom: 8px; font-weight: 500;">Card Number <span style="color: red;">*</span></label>
                        <input type="text" name="card_number" id="card_number" required placeholder="XXXX-XXXX-XXXX-XXXX" style="width: 100%; padding: 12px; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 6px;">
                    </div>
                </div>

                <div class="grid grid-2" style="gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label for="expiry" style="display: block; margin-bottom: 8px; font-weight: 500;">Expiry Date (MM/YY) <span style="color: red;">*</span></label>
                        <input type="text" name="expiry" id="expiry" required placeholder="12/25" style="width: 100%; padding: 12px; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 6px;">
                    </div>
                    <div>
                        <label for="cvv" style="display: block; margin-bottom: 8px; font-weight: 500;">CVV <span style="color: red;">*</span></label>
                        <input type="password" name="cvv" id="cvv" required placeholder="***" style="width: 100%; padding: 12px; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 6px;">
                    </div>
                </div>

                <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 6px; margin-bottom: 25px; border: 1px solid var(--border-color); font-size: 0.9rem; line-height: 1.5;">
                    <i class="fas fa-lock" style="color: #28a745; margin-right: 5px;"></i> 
                    <strong>Secure Payment:</strong> We use industry-standard encryption. Your deposit is 100% refundable if you change your mind within 7 days.
                </div>

                <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 1.1rem; border-radius: 6px; background: #28a745; color: #fff; font-weight: bold; border: none;">
                    Pay ₹<?= number_format($booking_amount, 2) ?> & Book Now
                </button>
            </form>
        <?php endif; ?>

    </div>
</div>

<?php include 'partials/footer.php'; ?>
