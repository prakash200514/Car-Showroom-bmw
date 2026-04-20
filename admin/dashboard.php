<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
// Auth check (commented for dev ease – uncomment in production)
// if (!isAdmin()) { header('Location: login.php'); exit(); }

// Real stats
$totalCars      = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$totalUsers     = $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 5")->fetchColumn();
$totalBookings  = $pdo->query("SELECT COUNT(*) FROM test_drive_bookings")->fetchColumn();
$pendingBook    = $pdo->query("SELECT COUNT(*) FROM test_drive_bookings WHERE status='Requested'")->fetchColumn();
$totalEnquiries = $pdo->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
$totalImages    = $pdo->query("SELECT COUNT(*) FROM car_images")->fetchColumn();

// Recent bookings
$recentBookings = $pdo->query("
    SELECT tdb.*, u.name AS user_name, c.name AS car_name
    FROM test_drive_bookings tdb
    JOIN users u ON u.id = tdb.user_id
    JOIN cars c ON c.id = tdb.car_id
    ORDER BY tdb.created_at DESC LIMIT 8
")->fetchAll();

// Cars by body type for chart
$bodyTypes = $pdo->query("SELECT body_type, COUNT(*) as cnt FROM cars GROUP BY body_type")->fetchAll();
$bodyLabels = json_encode(array_column($bodyTypes, 'body_type'));
$bodyCounts = json_encode(array_column($bodyTypes, 'cnt'));

function statusBadge($status) {
    $map = [
        'Requested'  => 'orange',
        'Confirmed'  => 'blue',
        'Completed'  => 'green',
        'Cancelled'  => 'red',
    ];
    $color = $map[$status] ?? 'grey';
    return "<span class=\"adm-badge adm-badge--{$color}\">{$status}</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard — BMW Admin</title>
    <?php include 'partials/head.php'; ?>
</head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <!-- Top Bar -->
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-chart-line"></i> Dashboard</div>
            <div class="adm-topbar__right">
                <span style="font-size:0.75rem;color:#aaa;"><?= date('D, d M Y') ?></span>
                <div class="adm-topbar__user">
                    <div class="adm-topbar__avatar">A</div>
                    Admin
                </div>
            </div>
        </div>

        <div class="adm-content">

            <!-- Stat Cards -->
            <div class="adm-stat-grid">
                <div class="adm-stat adm-stat--blue">
                    <div class="adm-stat__icon"><i class="fas fa-car"></i></div>
                    <div class="adm-stat__value"><?= $totalCars ?></div>
                    <div class="adm-stat__label">Total Cars</div>
                </div>
                <div class="adm-stat adm-stat--green">
                    <div class="adm-stat__icon"><i class="fas fa-users"></i></div>
                    <div class="adm-stat__value"><?= $totalUsers ?></div>
                    <div class="adm-stat__label">Registered Users</div>
                </div>
                <div class="adm-stat adm-stat--orange">
                    <div class="adm-stat__icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="adm-stat__value"><?= $totalBookings ?></div>
                    <div class="adm-stat__label">Test Drive Bookings</div>
                </div>
                <div class="adm-stat adm-stat--red">
                    <div class="adm-stat__icon"><i class="fas fa-clock"></i></div>
                    <div class="adm-stat__value"><?= $pendingBook ?></div>
                    <div class="adm-stat__label">Pending Requests</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div style="display:grid;grid-template-columns:1fr 300px;gap:16px;margin-bottom:20px;">
                <!-- Bookings Chart (placeholder monthly) -->
                <div class="adm-card" style="margin-bottom:0;">
                    <div class="adm-card__header">
                        <span class="adm-card__title">Monthly Test Drive Bookings</span>
                    </div>
                    <canvas id="bookChart" height="100"></canvas>
                </div>
                <!-- Car Body Type Doughnut -->
                <div class="adm-card" style="margin-bottom:0;display:flex;flex-direction:column;">
                    <div class="adm-card__header">
                        <span class="adm-card__title">Car Types</span>
                    </div>
                    <div style="flex:1;display:flex;align-items:center;justify-content:center;">
                        <canvas id="typeChart" style="max-height:200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
                <a href="car-add.php" class="adm-btn adm-btn--primary"><i class="fas fa-plus"></i> Add New Car</a>
                <a href="banners.php" class="adm-btn adm-btn--outline"><i class="fas fa-image"></i> Edit Banners</a>
                <a href="bookings.php" class="adm-btn adm-btn--ghost"><i class="fas fa-calendar"></i> View All Bookings</a>
                <a href="enquiries.php" class="adm-btn adm-btn--ghost"><i class="fas fa-envelope"></i> View Enquiries</a>
            </div>

            <!-- Recent Bookings Table -->
            <div class="adm-card">
                <div class="adm-card__header">
                    <span class="adm-card__title">Recent Test Drive Requests</span>
                    <a href="bookings.php" class="adm-btn adm-btn--ghost adm-btn--sm">View All</a>
                </div>
                <?php if (count($recentBookings) > 0): ?>
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr>
                            <th>#</th><th>Customer</th><th>Car</th><th>Date</th><th>Time</th><th>Status</th><th>Actions</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td style="color:var(--adm-muted);">#<?= $b['id'] ?></td>
                            <td><strong><?= htmlspecialchars($b['user_name']) ?></strong></td>
                            <td><?= htmlspecialchars($b['car_name']) ?></td>
                            <td><?= htmlspecialchars($b['booking_date']) ?></td>
                            <td><?= htmlspecialchars($b['time_slot']) ?></td>
                            <td><?= statusBadge($b['status']) ?></td>
                            <td>
                                <?php if ($b['status'] === 'Requested'): ?>
                                <a href="bookings.php?confirm=<?= $b['id'] ?>" class="adm-btn adm-btn--success adm-btn--sm">Confirm</a>
                                <a href="bookings.php?cancel=<?= $b['id'] ?>" class="adm-btn adm-btn--danger adm-btn--sm" onclick="return confirm('Cancel this booking?')">Cancel</a>
                                <?php else: ?>
                                <span style="color:var(--adm-muted);font-size:0.75rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p style="text-align:center;color:var(--adm-muted);padding:30px 0;">No bookings yet.</p>
                <?php endif; ?>
            </div>

            <!-- Additional Stats Row -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="adm-card" style="margin-bottom:0;">
                    <div class="adm-card__header"><span class="adm-card__title">Quick Stats</span></div>
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <?php foreach([
                            ['Total Car Images',  $totalImages,    'images',        'var(--adm-blue)'],
                            ['Total Enquiries',   $totalEnquiries, 'envelope',      '#b38600'],
                        ] as [$lbl,$val,$ico,$col]): ?>
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:#fafafa;border:1px solid var(--adm-border);">
                            <div style="display:flex;align-items:center;gap:10px;font-size:0.82rem;color:var(--adm-text);">
                                <i class="fas fa-<?= $ico ?>" style="color:<?= $col ?>;width:16px;"></i>
                                <?= $lbl ?>
                            </div>
                            <strong style="color:<?= $col ?>;font-size:1rem;"><?= $val ?></strong>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="adm-card" style="margin-bottom:0;">
                    <div class="adm-card__header"><span class="adm-card__title">System</span></div>
                    <div style="font-size:0.8rem;line-height:2;">
                        <div>PHP Version: <strong><?= PHP_VERSION ?></strong></div>
                        <div>Server Time: <strong><?= date('d M Y, h:i A') ?></strong></div>
                        <div>DB: <strong>MySQL (showroom_db)</strong></div>
                        <div>Admin Panel: <strong>v2.0 BMW Edition</strong></div>
                    </div>
                </div>
            </div>

        </div><!-- /adm-content -->
    </div><!-- /adm-main -->
</div>

<script>
// Monthly bookings line chart (placeholder data)
new Chart(document.getElementById('bookChart'), {
    type: 'line',
    data: {
        labels: ['Sep','Oct','Nov','Dec','Jan','Feb'],
        datasets: [{
            label: 'Test Drives',
            data: [4,7,5,9,12,<?= $totalBookings ?>],
            borderColor: '#1C69D4',
            backgroundColor: 'rgba(28,105,212,0.07)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#1C69D4',
            pointRadius: 4
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

// Car types doughnut
new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: <?= $bodyLabels ?: '["No Data"]' ?>,
        datasets: [{
            data: <?= $bodyCounts ?: '[1]' ?>,
            backgroundColor: ['#1C69D4','#198754','#ffc107','#dc3545','#6c757d','#0dcaf0'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } } },
        cutout: '65%'
    }
});
</script>
</body>
</html>
