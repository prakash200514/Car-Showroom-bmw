<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isAdmin()) { header("Location: /showroom/login.php"); exit; }

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = (int)($_POST['booking_id'] ?? 0);

    if ($_POST['action'] === 'update_status' && $id > 0) {
        $status = $_POST['status'] ?? 'Pending';
        $amount = $_POST['amount'] !== '' ? (float)$_POST['amount'] : null;
        $allowed = ['Pending','Confirmed','In Progress','Completed','Cancelled'];
        if (in_array($status, $allowed)) {
            $pdo->prepare("UPDATE service_bookings SET status=?, amount=? WHERE id=?")->execute([$status, $amount, $id]);
            $_SESSION['flash_success'] = "Service request #$id updated successfully.";
        }
    } elseif ($_POST['action'] === 'delete' && $id > 0) {
        $pdo->prepare("DELETE FROM service_bookings WHERE id=?")->execute([$id]);
        $_SESSION['flash_success'] = "Service request #$id deleted.";
    }
    header("Location: service-requests.php");
    exit;
}

// Filters
$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$where = ['1=1'];
$params = [];
if ($statusFilter) { $where[] = 'sb.status = ?'; $params[] = $statusFilter; }
if ($search) {
    $where[] = '(sb.customer_name LIKE ? OR sb.customer_email LIKE ? OR sb.car_model LIKE ? OR sb.service_name LIKE ?)';
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
}
$whereStr = implode(' AND ', $where);

$requests = $pdo->prepare("
    SELECT sb.*, u.name as user_name, u.email as user_email 
    FROM service_bookings sb
    LEFT JOIN users u ON u.id = sb.user_id
    WHERE $whereStr
    ORDER BY sb.created_at DESC
");
$requests->execute($params);
$requests = $requests->fetchAll();

// Counts per status
$counts = $pdo->query("SELECT status, COUNT(*) as cnt FROM service_bookings GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$total = array_sum($counts);

$statusColors = [
    'Pending'     => '#f59e0b',
    'Confirmed'   => '#3b82f6',
    'In Progress' => '#8b5cf6',
    'Completed'   => '#10b981',
    'Cancelled'   => '#ef4444',
];

include 'partials/sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Requests – BMW Admin</title>
    <?php include 'partials/head.php'; ?>
    <style>
        body { display: flex; min-height: 100vh; background: #f4f5f7; }
        .adm-main { flex: 1; padding: 30px; overflow-y: auto; }

        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 15px; }
        .page-header h1 { font-size: 1.6rem; font-weight: 800; color: #111; }

        /* Stat Cards */
        .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: #fff; border-radius: 10px; padding: 18px 20px; border-left: 4px solid var(--c); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .stat-card .stat-num { font-size: 2rem; font-weight: 900; color: var(--c); }
        .stat-card .stat-label { font-size: 0.8rem; color: #666; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Filters */
        .filters { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; align-items: center; }
        .filters input[type=text] { padding: 9px 14px; border: 1.5px solid #ddd; border-radius: 7px; font-size: 0.9rem; outline: none; min-width: 220px; }
        .filters input:focus { border-color: #1c6bba; }
        .filter-btn { padding: 9px 16px; border-radius: 7px; border: 1.5px solid #ddd; background: #fff; cursor: pointer; font-size: 0.85rem; font-weight: 600; color: #555; transition: all 0.2s; }
        .filter-btn:hover, .filter-btn.active { background: #1c6bba; color: #fff; border-color: #1c6bba; }

        /* Table */
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; }
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        thead { background: #f8f9fa; }
        th { padding: 14px 16px; text-align: left; font-size: 0.78rem; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #eee; }
        td { padding: 14px 16px; font-size: 0.9rem; color: #333; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        /* Status badge */
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-block; }

        /* Inline edit form */
        .inline-form { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .inline-form select, .inline-form input[type=number] {
            padding: 6px 10px; border: 1.5px solid #ddd; border-radius: 6px; font-size: 0.85rem; outline: none;
        }
        .inline-form select:focus, .inline-form input:focus { border-color: #1c6bba; }
        .btn-sm { padding: 6px 14px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.82rem; font-weight: 700; transition: all 0.2s; }
        .btn-primary { background: #1c6bba; color: #fff; }
        .btn-primary:hover { background: #155598; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }

        .empty-msg { text-align: center; padding: 60px 20px; color: #999; font-size: 1.1rem; }
        .empty-msg i { font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.3; }

        .flash { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
    </style>
</head>
<body>

<main class="adm-main">

    <div class="page-header">
        <h1><i class="fas fa-tools" style="color:#1c6bba;margin-right:10px;"></i>Service Requests</h1>
        <a href="/showroom/services.php" target="_blank" class="btn-sm btn-primary" style="text-decoration:none;padding:10px 20px;">
            <i class="fas fa-external-link-alt"></i> View Services Page
        </a>
    </div>

    <?php if (isset($_SESSION['flash_success'])): ?>
    <div class="flash"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']); endif; ?>

    <!-- Stat Cards -->
    <div class="stat-cards">
        <div class="stat-card" style="--c:#64748b;">
            <div class="stat-num"><?= $total ?></div>
            <div class="stat-label">Total Requests</div>
        </div>
        <div class="stat-card" style="--c:#f59e0b;">
            <div class="stat-num"><?= $counts['Pending'] ?? 0 ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card" style="--c:#3b82f6;">
            <div class="stat-num"><?= $counts['Confirmed'] ?? 0 ?></div>
            <div class="stat-label">Confirmed</div>
        </div>
        <div class="stat-card" style="--c:#8b5cf6;">
            <div class="stat-num"><?= $counts['In Progress'] ?? 0 ?></div>
            <div class="stat-label">In Progress</div>
        </div>
        <div class="stat-card" style="--c:#10b981;">
            <div class="stat-num"><?= $counts['Completed'] ?? 0 ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card" style="--c:#ef4444;">
            <div class="stat-num"><?= $counts['Cancelled'] ?? 0 ?></div>
            <div class="stat-label">Cancelled</div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="filters">
        <input type="text" name="search" placeholder="🔍  Search by name, car, service..." value="<?= htmlspecialchars($search) ?>">
        <a href="service-requests.php" class="filter-btn <?= !$statusFilter ? 'active' : '' ?>">All</a>
        <?php foreach (['Pending','Confirmed','In Progress','Completed','Cancelled'] as $s): ?>
        <a href="?status=<?= urlencode($s) ?>&search=<?= urlencode($search) ?>" class="filter-btn <?= $statusFilter === $s ? 'active' : '' ?>"><?= $s ?></a>
        <?php endforeach; ?>
    </form>

    <!-- Table -->
    <div class="table-card">
        <div class="table-responsive">
        <?php if (count($requests) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Car Model</th>
                    <th>Preferred Date</th>
                    <th>Notes</th>
                    <th>Status & Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
            <tr>
                <td><strong>#<?= $r['id'] ?></strong><br><span style="font-size:0.75rem;color:#999;"><?= date('d M Y', strtotime($r['created_at'])) ?></span></td>
                <td>
                    <strong><?= htmlspecialchars($r['customer_name'] ?: ($r['user_name'] ?? 'Guest')) ?></strong><br>
                    <a href="mailto:<?= htmlspecialchars($r['customer_email'] ?: $r['user_email']) ?>" style="font-size:0.8rem;color:#1c6bba;"><?= htmlspecialchars($r['customer_email'] ?: ($r['user_email'] ?? '—')) ?></a><br>
                    <?php if ($r['customer_phone']): ?>
                    <span style="font-size:0.8rem;color:#555;"><i class="fas fa-phone" style="font-size:0.7rem;"></i> <?= htmlspecialchars($r['customer_phone']) ?></span>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($r['service_name'] ?? '—') ?></strong></td>
                <td><?= htmlspecialchars($r['car_model']) ?></td>
                <td>
                    <i class="fas fa-calendar-alt" style="color:#1c6bba;"></i>
                    <?= date('d M Y', strtotime($r['service_date'])) ?>
                </td>
                <td style="max-width:180px;font-size:0.85rem;color:#555;">
                    <?= $r['notes'] ? htmlspecialchars(substr($r['notes'], 0, 80)) . (strlen($r['notes']) > 80 ? '…' : '') : '—' ?>
                </td>
                <td>
                    <form method="POST" class="inline-form">
                        <input type="hidden" name="booking_id" value="<?= $r['id'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <select name="status">
                            <?php foreach (['Pending','Confirmed','In Progress','Completed','Cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $r['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="amount" step="0.01" min="0" placeholder="₹ Amount" value="<?= $r['amount'] ?? '' ?>" style="width:110px;">
                        <button type="submit" class="btn-sm btn-primary">Save</button>
                    </form>
                    <div style="margin-top:6px;">
                        <span class="status-badge" style="background:<?= $statusColors[$r['status']] ?>22;color:<?= $statusColors[$r['status']] ?>;">
                            <?= $r['status'] ?>
                        </span>
                        <?php if ($r['amount']): ?>
                        <span style="font-size:0.85rem;font-weight:700;color:#10b981;margin-left:8px;">₹<?= number_format($r['amount'], 2) ?></span>
                        <?php endif; ?>
                    </div>
                </td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this request permanently?')">
                        <input type="hidden" name="booking_id" value="<?= $r['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-msg">
            <i class="fas fa-clipboard-list"></i>
            No service requests found<?= $statusFilter ? " with status \"$statusFilter\"" : '' ?>.
        </div>
        <?php endif; ?>
        </div>
    </div>

</main>

</body>
</html>
