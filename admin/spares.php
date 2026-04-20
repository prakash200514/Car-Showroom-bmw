<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
if (!isAdmin()) { header('Location: login.php'); exit(); }

// DELETE spare part
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM spares WHERE id=?")->execute([$_GET['delete']]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Spare part deleted successfully.'];
    header('Location: spares.php'); exit();
}

$search = trim($_GET['search'] ?? '');
$categoryId  = intval($_GET['category_id'] ?? 0);
$where  = '1';
$params = [];
if ($search) { $where .= ' AND s.name LIKE ?'; $params[] = "%$search%"; }
if ($categoryId > 0)  { $where .= ' AND s.category_id=?'; $params[] = $categoryId; }

$limit  = 12;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

$stmt = $pdo->prepare("SELECT s.*, c.category_name FROM spares s LEFT JOIN spare_categories c ON s.category_id=c.id WHERE $where ORDER BY s.id DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$spares = $stmt->fetchAll();

$total = $pdo->prepare("SELECT COUNT(*) FROM spares s WHERE $where");
$total->execute($params);
$totalSpares  = $total->fetchColumn();
$totalPages = max(1, ceil($totalSpares/$limit));

// Fetch categories for filter
$categories = $pdo->query("SELECT * FROM spare_categories ORDER BY category_name")->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Manage Spares — BMW Admin</title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-cogs"></i> All Spare Parts</div>
            <a href="spare-add.php" class="adm-btn adm-btn--primary"><i class="fas fa-plus"></i> Add New Spare</a>
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
                        <input class="adm-input" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by part name or number…">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Category</label>
                        <select class="adm-select" name="category_id">
                            <option value="0">All Categories</option>
                            <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $categoryId === (int)$c['id'] ? 'selected':'' ?>><?= htmlspecialchars($c['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="adm-btn adm-btn--primary" type="submit"><i class="fas fa-search"></i> Search</button>
                    <?php if ($search || $categoryId > 0): ?>
                    <a href="spares.php" class="adm-btn adm-btn--ghost"><i class="fas fa-times"></i> Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Count -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <span style="font-size:0.78rem;color:var(--adm-muted);"><?= number_format($totalSpares) ?> spare<?= $totalSpares!=1?'s':'' ?> found</span>
            </div>

            <!-- Table -->
            <div class="adm-card" style="padding:0;overflow:hidden;">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr>
                            <th style="width:80px;">Image</th>
                            <th>Part Name</th>
                            <th>Part No.</th>
                            <th>Category</th>
                            <th>Stock Qty</th>
                            <th>Price (INR)</th>
                            <th>Actions</th>
                        </tr></thead>
                        <tbody>
                        <?php if (count($spares) > 0): ?>
                        <?php foreach ($spares as $spare): ?>
                        <tr>
                            <td>
                                <?php if ($spare['image']): ?>
                                <img src="<?= htmlspecialchars($spare['image']) ?>" alt="" class="adm-thumb">
                                <?php else: ?>
                                <div class="adm-thumb" style="display:flex;align-items:center;justify-content:center;color:#ccc;"><i class="fas fa-cogs"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($spare['name']) ?></strong>
                            </td>
                            <td><span style="color:#666; font-family:monospace;"><?= htmlspecialchars($spare['part_number'] ?: 'N/A') ?></span></td>
                            <td><span class="adm-badge adm-badge--grey"><?= htmlspecialchars($spare['category_name']) ?></span></td>
                            <td>
                                <?php if($spare['stock_qty'] > 0): ?>
                                    <span style="color:green; font-weight:bold;"><?= $spare['stock_qty'] ?> in stock</span>
                                <?php else: ?>
                                    <span style="color:red; font-weight:bold;">Out of stock</span>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color:var(--adm-blue);">₹<?= number_format($spare['price'], 2) ?></strong></td>
                            <td style="white-space:nowrap;">
                                <a href="spare-edit.php?id=<?= $spare['id'] ?>" class="adm-btn adm-btn--outline adm-btn--sm" title="Edit"><i class="fas fa-pen"></i> Edit</a>
                                <a href="?delete=<?= $spare['id'] ?>" class="adm-btn adm-btn--danger adm-btn--sm" onclick="return confirm('Delete <?= htmlspecialchars($spare['name'], ENT_QUOTES) ?>? This cannot be undone.')" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--adm-muted);">No spares found. <a href="spare-add.php" style="color:var(--adm-blue);">Add one?</a></td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="adm-pagination" style="margin-top:16px;">
                <?php for ($i=1;$i<=$totalPages;$i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category_id=<?= $categoryId ?>"
                   class="adm-page <?= $i===$page?'active':'' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body></html>
