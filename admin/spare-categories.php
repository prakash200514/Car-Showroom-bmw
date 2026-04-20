<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
if (!isAdmin()) { header('Location: login.php'); exit(); }

// ADD New Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name'] ?? '');
    if ($name !== '') {
        $stmt = $pdo->prepare("SELECT id FROM spare_categories WHERE category_name=?");
        $stmt->execute([$name]);
        if (!$stmt->fetch()) {
            $pdo->prepare("INSERT INTO spare_categories (category_name) VALUES (?)")->execute([$name]);
            $_SESSION['flash'] = ['type'=>'success','msg'=>"Category '$name' added successfully."];
        } else {
            $_SESSION['flash'] = ['type'=>'error','msg'=>"Category '$name' already exists."];
        }
    }
    header('Location: spare-categories.php'); exit();
}

// DELETE Category
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    // Check if category is used
    $check = $pdo->prepare("SELECT COUNT(*) FROM spares WHERE category_id=?");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        $_SESSION['flash'] = ['type'=>'error','msg'=>'Cannot delete category because it is being used by existing spare parts.'];
    } else {
        $pdo->prepare("DELETE FROM spare_categories WHERE id=?")->execute([$id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Category deleted successfully.'];
    }
    header('Location: spare-categories.php'); exit();
}

// Fetch all categories with part counts
$stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM spares s WHERE s.category_id = c.id) as part_count 
                     FROM spare_categories c 
                     ORDER BY c.category_name");
$categories = $stmt->fetchAll();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Manage Categories — BMW Admin</title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-tags"></i> Spare Part Categories</div>
        </div>
        <div class="adm-content">

            <?php if ($flash): ?>
            <div class="adm-alert adm-alert--<?= $flash['type'] ?>"><i class="fas fa-info-circle"></i> <?= $flash['msg'] ?></div>
            <?php endif; ?>

            <div style="display:flex; gap:30px; flex-wrap:wrap; align-items:flex-start;">
                
                <!-- Add Form -->
                <div class="adm-card" style="flex:1; min-width:300px; padding:25px;">
                    <h3 style="margin-bottom:15px; font-weight:700;"><i class="fas fa-plus"></i> Add New Category</h3>
                    <form method="POST">
                        <div class="adm-field">
                            <label class="adm-label">Category Name</label>
                            <input type="text" name="category_name" class="adm-input" required placeholder="e.g. Engine Parts">
                        </div>
                        <button type="submit" name="add_category" class="adm-btn adm-btn--primary" style="margin-top:10px;"><i class="fas fa-save"></i> Save Category</button>
                    </form>
                </div>

                <!-- Categories List -->
                <div class="adm-card" style="flex:2; min-width:400px; padding:0; overflow:hidden;">
                    <div class="adm-table-wrap">
                        <table class="adm-table">
                            <thead><tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Associated Parts</th>
                                <th>Actions</th>
                            </tr></thead>
                            <tbody>
                            <?php if (count($categories) > 0): ?>
                            <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= $cat['id'] ?></td>
                                <td><strong><?= htmlspecialchars($cat['category_name']) ?></strong></td>
                                <td>
                                    <span class="adm-badge <?= $cat['part_count'] > 0 ? 'adm-badge--featured' : 'adm-badge--grey' ?>">
                                        <?= $cat['part_count'] ?> part(s)
                                    </span>
                                </td>
                                <td>
                                    <?php if($cat['part_count'] == 0): ?>
                                    <a href="?delete=<?= $cat['id'] ?>" class="adm-btn adm-btn--danger adm-btn--sm" onclick="return confirm('Are you sure you want to delete this category?')"><i class="fas fa-trash"></i></a>
                                    <?php else: ?>
                                    <span style="font-size:0.8rem; color:#888;">In Use</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--adm-muted);">No categories found. Start by adding one.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
</body></html>
