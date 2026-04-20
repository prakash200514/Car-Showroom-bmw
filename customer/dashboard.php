<?php
include '../partials/header.php';
include '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('/showroom/login.php');
}

$user_id = $_SESSION['user_id'];

// Fetch upcoming test drive bookings for this user
$stmtTD = $pdo->prepare("
    SELECT td.*, c.name as car_name, b.name as branch_name, b.city as branch_city
    FROM test_drive_bookings td
    LEFT JOIN cars c ON c.id = td.car_id
    LEFT JOIN branches b ON b.id = td.branch_id
    WHERE td.user_id = ?
    ORDER BY td.booking_date DESC
    LIMIT 1
");
$stmtTD->execute([$user_id]);
$testDrive = $stmtTD->fetch();

// Fetch car bookings for this user
$stmtCB = $pdo->prepare("
    SELECT cb.*, c.name as car_name, c.brand as car_brand
    FROM car_bookings cb
    LEFT JOIN cars c ON c.id = cb.car_id
    WHERE cb.user_id = ?
    ORDER BY cb.created_at DESC
    LIMIT 1
");
$stmtCB->execute([$user_id]);
$carBooking = $stmtCB->fetch();

// Fetch wishlist
$stmtWL = $pdo->prepare("
    SELECT w.*, c.name as car_name, c.body_type
    FROM wishlist w
    LEFT JOIN cars c ON c.id = w.car_id
    WHERE w.user_id = ?
    LIMIT 5
");
$stmtWL->execute([$user_id]);
$wishlistItems = $stmtWL->fetchAll();
?>

<style>
    .dashboard-wrapper {
        padding: 40px var(--container-padding);
        max-width: var(--max-width);
        margin: 0 auto;
        margin-top: var(--navbar-height);
    }
    
    .dashboard-header {
        margin-bottom: 40px;
    }
    
    .dashboard-header h1 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--bmw-dark);
        margin-bottom: 8px;
    }
    
    .dashboard-header p {
        color: var(--text-muted);
        font-size: 1.1rem;
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    @media (min-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
        }
    }
    
    .dash-section {
        margin-bottom: 40px;
    }
    
    .dash-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        margin-bottom: 20px;
        color: var(--bmw-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dash-card {
        background: #fff;
        border: 1px solid var(--border-color);
        padding: 24px;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        margin-bottom: 16px;
    }
    
    .dash-card:hover {
        box-shadow: var(--shadow);
        transform: translateY(-2px);
    }
    
    .dash-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--bmw-dark);
    }
    
    .dash-card-meta {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 16px;
    }
    
    .dash-card-meta strong {
        color: var(--text-color);
    }
    
    .action-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .action-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: var(--bmw-light-grey);
        border-radius: var(--radius-sm);
        color: var(--bmw-dark);
        font-weight: 500;
        font-size: 0.95rem;
        transition: var(--transition-fast);
        border: 1px solid transparent;
    }
    
    .action-item:hover {
        background: #fff;
        border-color: var(--bmw-blue);
        color: var(--bmw-blue);
        box-shadow: var(--shadow-sm);
        transform: translateX(4px);
    }
    
    .action-item i {
        font-size: 1.2rem;
        color: var(--bmw-blue);
        width: 24px;
        text-align: center;
    }
    
    .wishlist-item {
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .wishlist-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .wishlist-title {
        font-weight: 600;
        color: var(--bmw-dark);
        margin-bottom: 4px;
        display: block;
    }
    
    .wishlist-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    
    .badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        border-radius: 2px;
        margin-bottom: 12px;
    }
    
    .badge-blue { background: rgba(28, 105, 212, 0.1); color: var(--bmw-blue); }
    .badge-green { background: rgba(46, 125, 50, 0.1); color: var(--success-color); }
    .badge-orange { background: rgba(230, 126, 34, 0.1); color: #e67e22; }

    .empty-state {
        text-align: center;
        padding: 30px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 12px;
        display: block;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: 0.95rem;
        margin-bottom: 15px;
    }
</style>

<div class="dashboard-wrapper">
    <div class="dashboard-header" data-aos="fade-up">
        <h1>Welcome Back, <?= htmlspecialchars($_SESSION['name'] ?? 'Guest') ?> 👋</h1>
        <p>A quick overview of your activities, bookings, and saved preferences.</p>
    </div>

    <div class="dashboard-grid">
        <!-- Left Column (Actions & Links) -->
        <div class="dash-left" data-aos="fade-up" data-aos-delay="100">
            
            <div class="dash-section">
                <div class="dash-section-title">
                    <i class="fas fa-star" style="color: #f1c40f;"></i> Quick Actions
                </div>
                <div class="action-list">
                    <a href="/showroom/cars.php" class="action-item"><i class="fas fa-search"></i> Explore New Models</a>
                    <a href="/showroom/cars.php" class="action-item"><i class="fas fa-edit"></i> Book a Test Drive</a>
                    <a href="/showroom/services.php" class="action-item"><i class="fas fa-wrench"></i> Book a Service</a>
                    <a href="/showroom/cars.php" class="action-item"><i class="fas fa-heart"></i> Browse Cars to Wishlist</a>
                    <a href="/showroom/new-launch.php" class="action-item"><i class="fas fa-bolt"></i> New Launches</a>
                    <a href="/showroom/cars.php" class="action-item"><i class="fas fa-bullseye"></i> Recommended for You</a>
                </div>
            </div>

            <div class="dash-section">
                <div class="dash-section-title">
                    <i class="fas fa-compass" style="color: var(--bmw-blue);"></i> Explore More
                </div>
                <div class="action-list">
                    <a href="/showroom/cars.php" class="action-item"><i class="fas fa-exchange-alt"></i> Compare Models</a>
                    <a href="/showroom/bmw-spares.php" class="action-item"><i class="fas fa-cogs"></i> Accessories & Spares</a>
                    <a href="/showroom/contact.php" class="action-item"><i class="fas fa-headset"></i> Customer Support</a>
                    <a href="/showroom/contact.php" class="action-item"><i class="fas fa-clipboard-list"></i> Request a Quote</a>
                </div>
            </div>

        </div>

        <!-- Right Column (Highlights) -->
        <div class="dash-right" data-aos="fade-up" data-aos-delay="200">
            <div class="dash-section-title">
                <i class="fas fa-fire" style="color: #e74c3c;"></i> Your Activity
            </div>

            <!-- Test Drive Booking -->
            <div class="dash-card">
                <div class="badge badge-blue">Test Drive Booking</div>
                <?php if ($testDrive): ?>
                    <h4><?= htmlspecialchars($testDrive['car_name']) ?></h4>
                    <div class="dash-card-meta">
                        <div><i class="far fa-calendar-alt"></i> <strong><?= date('d M, h:i A', strtotime($testDrive['booking_date'])) ?></strong></div>
                        <div style="margin-top:4px;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($testDrive['branch_name'] . ' (' . $testDrive['branch_city'] . ')') ?></div>
                        <div style="margin-top:4px;"><i class="fas fa-clock"></i> <?= htmlspecialchars($testDrive['time_slot']) ?></div>
                        <div style="margin-top:6px;">
                            <span style="background: <?= $testDrive['status'] === 'Confirmed' ? '#e8f5e9' : 'rgba(28,107,186,0.1)' ?>; color: <?= $testDrive['status'] === 'Confirmed' ? '#2e7d32' : 'var(--bmw-blue)' ?>; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                                <?= htmlspecialchars($testDrive['status']) ?>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-steering-wheel"></i>
                        <p>No test drive booked yet.</p>
                        <a href="/showroom/cars.php" class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 18px;">Book Now</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Car Booking -->
            <div class="dash-card">
                <div class="badge badge-orange">Car Booking</div>
                <?php if ($carBooking): ?>
                    <h4><?= htmlspecialchars($carBooking['car_name']) ?></h4>
                    <div class="dash-card-meta">
                        <div><i class="fas fa-calendar-alt"></i> <strong>Booked on <?= date('d M Y', strtotime($carBooking['created_at'])) ?></strong></div>
                        <div style="margin-top:4px;"><i class="fas fa-rupee-sign"></i> Deposit Paid: ₹<?= number_format($carBooking['booking_amount'], 2) ?></div>
                        <div style="margin-top:6px;">
                            <span style="background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">
                                <?= htmlspecialchars($carBooking['payment_status']) ?>
                            </span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-car"></i>
                        <p>No car booking yet.</p>
                        <a href="/showroom/cars.php" class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 18px;">Reserve a Car</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Wishlist -->
            <div class="dash-card">
                <h4><i class="fas fa-heart" style="color: #e74c3c; margin-right: 8px;"></i> Your Wishlist</h4>
                <?php if (count($wishlistItems) > 0): ?>
                    <div style="margin-top: 16px;">
                        <?php foreach ($wishlistItems as $item): ?>
                        <div class="wishlist-item">
                            <div>
                                <span class="wishlist-title"><?= htmlspecialchars($item['car_name']) ?></span>
                                <span class="wishlist-desc"><?= htmlspecialchars($item['body_type'] ?? 'BMW Collection') ?></span>
                            </div>
                            <a href="/showroom/car-details.php?id=<?= $item['car_id'] ?>" style="color: var(--bmw-blue);"><i class="fas fa-chevron-right"></i></a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 20px;">
                        <a href="/showroom/cars.php" class="btn-link">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-heart"></i>
                        <p>Your wishlist is empty.</p>
                        <a href="/showroom/cars.php" class="btn btn-primary" style="font-size: 0.9rem; padding: 8px 18px;">Browse Cars</a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>


<style>
    /* Dashboard specific styles */
    .dashboard-wrapper {
        padding: 40px var(--container-padding);
        max-width: var(--max-width);
        margin: 0 auto;
        margin-top: var(--navbar-height);
    }
    
    .dashboard-header {
        margin-bottom: 40px;
    }
    
    .dashboard-header h1 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--bmw-dark);
        margin-bottom: 8px;
    }
    
    .dashboard-header p {
        color: var(--text-muted);
        font-size: 1.1rem;
    }
    
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    @media (min-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
        }
    }
    
    .dash-section {
        margin-bottom: 40px;
    }
    
    .dash-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        margin-bottom: 20px;
        color: var(--bmw-dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .dash-card {
        background: #fff;
        border: 1px solid var(--border-color);
        padding: 24px;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        margin-bottom: 16px;
    }
    
    .dash-card:hover {
        box-shadow: var(--shadow);
        transform: translateY(-2px);
    }
    
    .dash-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--bmw-dark);
    }
    
    .dash-card-meta {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 16px;
    }
    
    .dash-card-meta strong {
        color: var(--text-color);
    }
    
    /* Action Lists */
    .action-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .action-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: var(--bmw-light-grey);
        border-radius: var(--radius-sm);
        color: var(--bmw-dark);
        font-weight: 500;
        font-size: 0.95rem;
        transition: var(--transition-fast);
        border: 1px solid transparent;
    }
    
    .action-item:hover {
        background: #fff;
        border-color: var(--bmw-blue);
        color: var(--bmw-blue);
        box-shadow: var(--shadow-sm);
        transform: translateX(4px);
    }
    
    .action-item i {
        font-size: 1.2rem;
        color: var(--bmw-blue);
        width: 24px;
        text-align: center;
    }
    
    .wishlist-item {
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .wishlist-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .wishlist-title {
        font-weight: 600;
        color: var(--bmw-dark);
        margin-bottom: 4px;
        display: block;
    }
    
    .wishlist-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    
    .badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        border-radius: 2px;
        margin-bottom: 12px;
    }
    
    .badge-blue { background: rgba(28, 105, 212, 0.1); color: var(--bmw-blue); }
    .badge-green { background: rgba(46, 125, 50, 0.1); color: var(--success-color); }
    
</style>

<div class="dashboard-wrapper">
    <div class="dashboard-header" data-aos="fade-up">
        <h1>Welcome Back, <?= htmlspecialchars($_SESSION['name'] ?? 'Muthu Prakash') ?> 👋</h1>
        <p>A quick overview of your activities, bookings, and saved preferences.</p>
    </div>

    <div class="dashboard-grid">
        <!-- Left Column (Actions & Links) -->
        <div class="dash-left" data-aos="fade-up" data-aos-delay="100">
            
            <div class="dash-section">
                <div class="dash-section-title">
                    <i class="fas fa-star" style="color: #f1c40f;"></i> Quick Actions
                </div>
                <div class="action-list">
                    <a href="/showroom/cars.php" class="action-item"><i class="fas fa-search"></i> Explore New Models</a>
                    <a href="/showroom/test-drive.php" class="action-item"><i class="fas fa-edit"></i> Book a Test Drive</a>
                    <a href="#" class="action-item"><i class="fas fa-wrench"></i> Schedule Service</a>
                    <a href="#" class="action-item"><i class="fas fa-heart"></i> View Wishlist</a>
                    <a href="#" class="action-item"><i class="fas fa-file-alt"></i> Service History</a>
                    <a href="#" class="action-item"><i class="fas fa-bullseye"></i> Recommended for You</a>
                </div>
            </div>

            <div class="dash-section">
                <div class="dash-section-title">
                    <i class="fas fa-compass" style="color: var(--bmw-blue);"></i> Explore More
                </div>
                <div class="action-list">
                    <a href="#" class="action-item"><i class="fas fa-exchange-alt"></i> Compare Models</a>
                    <a href="#" class="action-item"><i class="fas fa-rupee-sign"></i> Finance & EMI Options</a>
                    <a href="#" class="action-item"><i class="fas fa-wifi"></i> ConnectedDrive Features</a>
                    <a href="#" class="action-item"><i class="fas fa-cogs"></i> Accessories & Customizations</a>
                </div>
            </div>

            <div class="dash-section">
                <div class="dash-section-title">
                    <i class="fas fa-headset" style="color: var(--bmw-blue);"></i> Assistance & Support
                </div>
                <div class="action-list">
                    <a href="#" class="action-item"><i class="fas fa-user-headset"></i> Customer Support</a>
                    <a href="#" class="action-item"><i class="fas fa-car-crash"></i> Accident Assist</a>
                    <a href="#" class="action-item"><i class="fas fa-clipboard-list"></i> Request a Quote</a>
                </div>
            </div>

        </div>

        <!-- Right Column (Highlights) -->
        <div class="dash-right" data-aos="fade-up" data-aos-delay="200">
            <div class="dash-section-title">
                <i class="fas fa-fire" style="color: #e74c3c;"></i> Your Highlights
            </div>

            <!-- Upcoming Test Drive -->
            <div class="dash-card">
                <div class="badge badge-blue">Upcoming Test Drive</div>
                <h4>BMW X5</h4>
                <div class="dash-card-meta">
                    <div><i class="far fa-calendar-alt"></i> <strong>24 Feb, 10:30 AM</strong></div>
                    <div style="margin-top:4px;"><i class="fas fa-map-marker-alt"></i> Location: Coimbatore Showroom</div>
                </div>
                <a href="#" class="btn-link">View Details <i class="fas fa-arrow-right"></i></a>
            </div>

            <!-- Recent Service -->
            <div class="dash-card">
                <div class="badge badge-green">Completed Service</div>
                <h4>BMW 3 Series</h4>
                <div class="dash-card-meta">
                    <div><i class="fas fa-check-circle"></i> <strong>Completed</strong></div>
                    <div style="margin-top:4px;"><i class="far fa-clock"></i> Last Service: 12 Jan 2025</div>
                </div>
                <a href="#" class="btn-link">Service Report <i class="fas fa-arrow-right"></i></a>
            </div>

            <!-- Wishlist -->
            <div class="dash-card">
                <h4><i class="fas fa-heart" style="color: #e74c3c; margin-right: 8px;"></i> Your Wishlist</h4>
                <div style="margin-top: 16px;">
                    <div class="wishlist-item">
                        <div>
                            <span class="wishlist-title">BMW 7 Series</span>
                            <span class="wishlist-desc">Luxury Sedan</span>
                        </div>
                        <a href="#" style="color: var(--bmw-blue);"><i class="fas fa-chevron-right"></i></a>
                    </div>
                    <div class="wishlist-item">
                        <div>
                            <span class="wishlist-title">BMW i4</span>
                            <span class="wishlist-desc">Electric Performance</span>
                        </div>
                        <a href="#" style="color: var(--bmw-blue);"><i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                    <a href="#" class="btn-link">View Full Wishlist <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
