<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
// if (!isAdmin()) { header('Location: login.php'); exit(); }

// DELETE car
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM cars WHERE id=?")->execute([$_GET['delete']]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Car deleted successfully.'];
    header('Location: cars.php'); exit();
}
// TOGGLE featured
if (isset($_GET['feature']) && is_numeric($_GET['feature'])) {
    $cur = $pdo->prepare("SELECT is_featured FROM cars WHERE id=?");
    $cur->execute([$_GET['feature']]);
    $val = $cur->fetchColumn();
    $pdo->prepare("UPDATE cars SET is_featured=? WHERE id=?")->execute([($val ? 0 : 1), $_GET['feature']]);
    header('Location: cars.php'); exit();
}

$search = trim($_GET['search'] ?? '');
$btype  = $_GET['body_type'] ?? '';
$where  = '1';
$params = [];
if ($search) { $where .= ' AND name LIKE ?'; $params[] = "%$search%"; }
if ($btype)  { $where .= ' AND body_type=?';  $params[] = $btype; }

$limit  = 12;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

$stmt = $pdo->prepare("SELECT c.*, ci.image_path FROM cars c LEFT JOIN car_images ci ON ci.car_id=c.id AND ci.is_primary=1 WHERE $where ORDER BY c.id DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$cars = $stmt->fetchAll();

$total = $pdo->prepare("SELECT COUNT(*) FROM cars WHERE $where");
$total->execute($params);
$totalCars  = $total->fetchColumn();
$totalPages = max(1, ceil($totalCars/$limit));

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Manage Cars — BMW Admin</title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-car"></i> All Cars</div>
            <a href="car-add.php" class="adm-btn adm-btn--primary"><i class="fas fa-plus"></i> Add New Car</a>
        </div>
        <div class="adm-content">

            <?php if ($flash): ?>
            <div class="adm-alert adm-alert--<?= $flash['type'] ?>"><i class="fas fa-check-circle"></i> <?= $flash['msg'] ?></div>
            <?php endif; ?>

            <!-- Search & Filter -->
            <div class="adm-card" style="padding:16px 20px;margin-bottom:16px;">
                <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
                    <div class="adm-field" style="flex:1;min-width:200px;">
                        <label class="adm-label">Search</label>
                        <input class="adm-input" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by model name…">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Body Type</label>
                        <select class="adm-select" name="body_type">
                            <option value="">All Types</option>
                            <?php foreach(['Sedan','SUV','Coupe','Convertible','Hatchback'] as $t): ?>
                            <option value="<?= $t ?>" <?= $btype===$t?'selected':'' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="adm-btn adm-btn--primary" type="submit"><i class="fas fa-search"></i> Search</button>
                    <?php if ($search || $btype): ?>
                    <a href="cars.php" class="adm-btn adm-btn--ghost"><i class="fas fa-times"></i> Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Count -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <span style="font-size:0.78rem;color:var(--adm-muted);"><?= number_format($totalCars) ?> car<?= $totalCars!=1?'s':'' ?> found</span>
            </div>

            <!-- Table -->
            <div class="adm-card" style="padding:0;overflow:hidden;">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr>
                            <th style="width:80px;">Image</th>
                            <th>Model Name</th>
                            <th>Body</th>
                            <th>Fuel</th>
                            <th>Transmission</th>
                            <th>Year</th>
                            <th>Price (USD)</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr></thead>
                        <tbody>
                        <?php if (count($cars) > 0): ?>
                        <?php foreach ($cars as $car): ?>
                        <tr>
                            <td>
                                <?php if ($car['image_path']): ?>
                                <img src="<?= htmlspecialchars($car['image_path']) ?>" alt="" class="adm-thumb">
                                <?php else: ?>
                                <div class="adm-thumb" style="display:flex;align-items:center;justify-content:center;color:#ccc;"><i class="fas fa-car"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($car['name']) ?></strong><br>
                                <small style="color:var(--adm-muted);"><?= htmlspecialchars($car['brand']) ?></small>
                            </td>
                            <td><span class="adm-badge adm-badge--grey"><?= htmlspecialchars($car['body_type']) ?></span></td>
                            <td><?= htmlspecialchars($car['fuel_type']) ?></td>
                            <td><?= htmlspecialchars($car['transmission']) ?></td>
                            <td><?= $car['year'] ?></td>
                            <td><strong style="color:var(--adm-blue);">$<?= number_format($car['price'],0) ?></strong></td>
                            <td>
                                <a href="?feature=<?= $car['id'] ?>" title="Toggle Featured">
                                    <span class="adm-badge <?= $car['is_featured'] ? 'adm-badge--featured' : 'adm-badge--grey' ?>">
                                        <?= $car['is_featured'] ? '★ Featured' : 'Normal' ?>
                                    </span>
                                </a>
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="car-edit.php?id=<?= $car['id'] ?>" class="adm-btn adm-btn--outline adm-btn--sm" title="Edit"><i class="fas fa-pen"></i> Edit</a>
                                <a href="car-images.php?car_id=<?= $car['id'] ?>" class="adm-btn adm-btn--ghost adm-btn--sm" title="Images"><i class="fas fa-images"></i> Images</a>
                                <a href="?delete=<?= $car['id'] ?>" class="adm-btn adm-btn--danger adm-btn--sm" onclick="return confirm('Delete <?= htmlspecialchars($car['name'], ENT_QUOTES) ?>? This cannot be undone.')" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--adm-muted);">No cars found. <a href="car-add.php" style="color:var(--adm-blue);">Add one?</a></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="adm-pagination" style="margin-top:16px;">
                <?php for ($i=1;$i<=$totalPages;$i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&body_type=<?= urlencode($btype) ?>"
                   class="adm-page <?= $i===$page?'active':'' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body></html>
