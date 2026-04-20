<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
// if (!isAdmin()) { header('Location: login.php'); exit(); }

// Handle actions
if (isset($_GET['confirm']) && is_numeric($_GET['confirm'])) {
    $pdo->prepare("UPDATE test_drive_bookings SET status='Confirmed' WHERE id=?")->execute([$_GET['confirm']]);
    $_SESSION['bflash'] = ['type'=>'success','msg'=>'Booking confirmed.'];
    header('Location: bookings.php'); exit();
}
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $pdo->prepare("UPDATE test_drive_bookings SET status='Cancelled' WHERE id=?")->execute([$_GET['cancel']]);
    $_SESSION['bflash'] = ['type'=>'danger','msg'=>'Booking cancelled.'];
    header('Location: bookings.php'); exit();
}
if (isset($_GET['complete']) && is_numeric($_GET['complete'])) {
    $pdo->prepare("UPDATE test_drive_bookings SET status='Completed' WHERE id=?")->execute([$_GET['complete']]);
    $_SESSION['bflash'] = ['type'=>'success','msg'=>'Booking marked as completed.'];
    header('Location: bookings.php'); exit();
}

$statusFilter = $_GET['status'] ?? '';
$where  = '1';
$params = [];
if ($statusFilter) { $where .= ' AND tdb.status=?'; $params[] = $statusFilter; }

$limit  = 15;
$page   = max(1, intval($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

$bookings = $pdo->prepare("
    SELECT tdb.*, u.name AS user_name, u.phone, c.name AS car_name
    FROM test_drive_bookings tdb
    JOIN users u ON u.id = tdb.user_id
    JOIN cars c ON c.id = tdb.car_id
    WHERE $where
    ORDER BY tdb.created_at DESC
    LIMIT $limit OFFSET $offset
");
$bookings->execute($params); $bookings = $bookings->fetchAll();

$total = $pdo->prepare("SELECT COUNT(*) FROM test_drive_bookings tdb WHERE $where");
$total->execute($params); $totalBookings = $total->fetchColumn();
$totalPages = max(1, ceil($totalBookings/$limit));

// Counts per status
$counts = [];
foreach(['Requested','Confirmed','Completed','Cancelled'] as $s) {
    $counts[$s] = $pdo->prepare("SELECT COUNT(*) FROM test_drive_bookings WHERE status=?");
    $counts[$s]->execute([$s]);
    $counts[$s] = $counts[$s]->fetchColumn();
}

$flash = $_SESSION['bflash'] ?? null;
unset($_SESSION['bflash']);

function bBadge($s) {
    $m = ['Requested'=>'orange','Confirmed'=>'blue','Completed'=>'green','Cancelled'=>'red'];
    return "<span class=\"adm-badge adm-badge--".($m[$s]??'grey')."\">{$s}</span>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Test Drive Bookings — BMW Admin</title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-calendar-check"></i> Test Drive Bookings</div>
            <span style="font-size:0.78rem;color:var(--adm-muted);"><?= number_format($totalBookings) ?> total</span>
        </div>
        <div class="adm-content">

            <?php if ($flash): ?>
            <div class="adm-alert adm-alert--<?= $flash['type'] ?>"><i class="fas fa-check-circle"></i> <?= $flash['msg'] ?></div>
            <?php endif; ?>

            <!-- Status Filter Tabs -->
            <div style="display:flex;gap:0;margin-bottom:16px;border:1px solid var(--adm-border);background:#fff;overflow:hidden;">
                <a href="bookings.php" style="padding:10px 20px;font-size:0.78rem;font-weight:600;color:<?= !$statusFilter?'#fff':'var(--adm-muted)' ?>;background:<?= !$statusFilter?'var(--adm-blue)':'transparent' ?>;text-decoration:none;border-right:1px solid var(--adm-border);">
                    All (<?= array_sum($counts) ?>)
                </a>
                <?php foreach(['Requested'=>'orange','Confirmed'=>'blue','Completed'=>'green','Cancelled'=>'red'] as $s=>$c): ?>
                <a href="?status=<?= $s ?>" style="padding:10px 20px;font-size:0.78rem;font-weight:600;color:<?= $statusFilter===$s?'#fff':'var(--adm-muted)' ?>;background:<?= $statusFilter===$s?"var(--adm-$c)":'transparent' ?>;text-decoration:none;border-right:1px solid var(--adm-border);">
                    <?= $s ?> (<?= $counts[$s] ?>)
                </a>
                <?php endforeach; ?>
            </div>

            <div class="adm-card" style="padding:0;overflow:hidden;">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr>
                            <th>#</th><th>Customer</th><th>Phone</th><th>Car</th><th>Date</th><th>Time Slot</th><th>Status</th><th>Actions</th>
                        </tr></thead>
                        <tbody>
                        <?php if (count($bookings) > 0): ?>
                        <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td style="color:var(--adm-muted);">#<?= $b['id'] ?></td>
                            <td><strong><?= htmlspecialchars($b['user_name']) ?></strong></td>
                            <td style="color:var(--adm-muted);font-size:0.78rem;"><?= htmlspecialchars($b['phone']) ?></td>
                            <td><?= htmlspecialchars($b['car_name']) ?></td>
                            <td><?= htmlspecialchars($b['booking_date']) ?></td>
                            <td><?= htmlspecialchars($b['time_slot']) ?></td>
                            <td><?= bBadge($b['status']) ?></td>
                            <td style="white-space:nowrap;">
                                <?php if ($b['status'] === 'Requested'): ?>
                                <a href="?confirm=<?= $b['id'] ?>" class="adm-btn adm-btn--success adm-btn--sm"><i class="fas fa-check"></i> Confirm</a>
                                <a href="?cancel=<?= $b['id'] ?>"  class="adm-btn adm-btn--danger  adm-btn--sm"
                                   onclick="return confirm('Cancel this booking?')"><i class="fas fa-times"></i> Cancel</a>
                                <?php elseif ($b['status'] === 'Confirmed'): ?>
                                <a href="?complete=<?= $b['id'] ?>" class="adm-btn adm-btn--outline adm-btn--sm"><i class="fas fa-flag-checkered"></i> Complete</a>
                                <a href="?cancel=<?= $b['id'] ?>"   class="adm-btn adm-btn--danger  adm-btn--sm"
                                   onclick="return confirm('Cancel?')"><i class="fas fa-times"></i></a>
                                <?php else: ?>
                                <span style="color:var(--adm-muted);font-size:0.75rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--adm-muted);">No bookings found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="adm-pagination" style="margin-top:12px;">
                <?php for ($i=1;$i<=$totalPages;$i++): ?>
                <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>" class="adm-page <?= $i===$page?'active':'' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body></html>
