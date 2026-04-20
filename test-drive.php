<?php
session_start();
include 'config/database.php';
include 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Optionally preserve the car_id to redirect back after login
    $car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
    if ($car_id > 0) {
        $_SESSION['redirect_after_login'] = 'test-drive.php?car_id=' . $car_id;
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
    $error = "No car selected for test drive.";
}

// Fetch branches
$stmtBranch = $pdo->query("SELECT * FROM branches ORDER BY name ASC");
$branches = $stmtBranch->fetchAll();

// If no branches exist, let's create a dummy one for testing so FK doesn't fail
if (count($branches) == 0) {
    $insBranch = $pdo->prepare("INSERT INTO branches (name, address, city, phone) VALUES ('Main City Branch', '123 Auto Avenue', 'Metropolis', '1800-BMW-TEST')");
    $insBranch->execute();
    
    // Fetch again
    $stmtBranch = $pdo->query("SELECT * FROM branches ORDER BY name ASC");
    $branches = $stmtBranch->fetchAll();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $branch_id    = (int)$_POST['branch_id'];
    $booking_date = $_POST['booking_date'];
    $time_slot    = $_POST['time_slot'];

    // Basic validation
    if (empty($branch_id) || empty($booking_date) || empty($time_slot)) {
        $error = "Please fill in all required fields.";
    } else if (strtotime($booking_date) < strtotime(date('Y-m-d'))) {
        $error = "Booking date cannot be in the past.";
    } else {
        // Insert booking
        try {
            $sql = "INSERT INTO test_drive_bookings (user_id, car_id, branch_id, booking_date, time_slot, status) 
                    VALUES (?, ?, ?, ?, ?, 'Requested')";
            $stmtInsert = $pdo->prepare($sql);
            $stmtInsert->execute([$user_id, $car_id, $branch_id, $booking_date, $time_slot]);
            
            $success = "Your test drive request has been successfully submitted! Our team will contact you shortly to confirm your booking.";
        } catch (PDOException $e) {
            $error = "An error occurred while saving your request. Please try again later.";
            // $error .= " " . $e->getMessage(); // For debugging
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
        <span style="color: var(--text-color);">Book Test Drive</span>
    </p>

    <div class="card card-glass" style="padding: 40px;">
        <div class="text-center mb-3">
            <h1 style="font-size: 2.2rem; color: var(--text-color);"><i class="fas fa-steering-wheel" style="color: var(--primary-color);"></i> Book a Test Drive</h1>
            <p class="text-muted mt-1">Experience the thrill firsthand. Schedule your test drive today.</p>
        </div>

        <?php if ($error): ?>
            <div style="background: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                <p style="color: #dc3545; margin: 0;"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: rgba(40, 167, 69, 0.1); border-left: 4px solid #28a745; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                <p style="color: #28a745; margin: 0;"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></p>
            </div>
            <div class="text-center mt-3">
                <a href="customer/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <a href="cars.php" class="btn btn-outline" style="margin-left: 10px;">Explore More Cars</a>
            </div>
        <?php elseif (!isset($error) || $error !== "The selected car does not exist." && $error !== "No car selected for test drive."): ?>

            <div class="grid" style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 30px; background: rgba(255,255,255,0.03); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
                <div>
                    <h4 style="margin-bottom: 5px; color: var(--text-light);">Selected Model</h4>
                    <p style="font-size: 1.2rem; font-weight: bold; color: var(--primary-color);"><?= htmlspecialchars($car['name']) ?></p>
                    <p class="text-muted" style="font-size: 0.9rem; margin-top: 5px;"><?= htmlspecialchars($car['body_type']) ?> | <?= htmlspecialchars($car['transmission']) ?></p>
                </div>
                <div style="text-align: right; display: flex; flex-direction: column; justify-content: center;">
                    <a href="cars.php" style="color: var(--primary-color); font-size: 0.9rem; text-decoration: underline;">Change Car</a>
                </div>
            </div>

            <form method="POST" action="test-drive.php?car_id=<?= $car_id ?>">
                
                <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">Booking Details</h3>
                
                <div class="grid grid-2" style="gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="branch_id" style="display: block; margin-bottom: 8px; font-weight: 500;">Select Dealership Branch <span style="color: red;">*</span></label>
                        <select name="branch_id" id="branch_id" required style="width: 100%; padding: 12px; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 6px; cursor: pointer;">
                            <option value="">-- Select a Branch --</option>
                            <?php foreach($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>">
                                    <?= htmlspecialchars($branch['name']) ?> (<?= htmlspecialchars($branch['city']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-2" style="gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label for="booking_date" style="display: block; margin-bottom: 8px; font-weight: 500;">Preferred Date <span style="color: red;">*</span></label>
                        <input type="date" name="booking_date" id="booking_date" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" style="width: 100%; padding: 12px; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 6px;">
                    </div>
                    <div>
                        <label for="time_slot" style="display: block; margin-bottom: 8px; font-weight: 500;">Preferred Time Slot <span style="color: red;">*</span></label>
                        <select name="time_slot" id="time_slot" required style="width: 100%; padding: 12px; background: var(--surface-color); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 6px; cursor: pointer;">
                            <option value="">-- Select Time --</option>
                            <option value="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</option>
                            <option value="11:30 AM - 12:30 PM">11:30 AM - 12:30 PM</option>
                            <option value="02:00 PM - 03:00 PM">02:00 PM - 03:00 PM</option>
                            <option value="03:30 PM - 04:30 PM">03:30 PM - 04:30 PM</option>
                            <option value="05:00 PM - 06:00 PM">05:00 PM - 06:00 PM</option>
                        </select>
                    </div>
                </div>

                <div style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 6px; margin-bottom: 25px; border: 1px solid var(--border-color); font-size: 0.9rem; line-height: 1.5;">
                    <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 5px;"></i> 
                    <strong>Important Note:</strong> Please carry your valid Driving License and Government ID proof when visiting for the test drive. Our representative will call you to confirm the exact time based on availability.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem; border-radius: 6px;">
                    Confirm Booking Request
                </button>
            </form>
        <?php endif; ?>

    </div>
</div>

<?php include 'partials/footer.php'; ?>
