<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
// if (!isAdmin()) { header('Location: login.php'); exit(); }

// Change role
if (isset($_POST['change_role'])) {
    $uid = intval($_POST['user_id']);
    $rid = intval($_POST['role_id']);
    $pdo->prepare("UPDATE users SET role_id=? WHERE id=?")->execute([$rid,$uid]);
    $_SESSION['uflash'] = ['type'=>'success','msg'=>'User role updated.'];
    header('Location: users.php'); exit();
}

// Delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM users WHERE id=? AND role_id != 1")->execute([$_GET['delete']]);
    $_SESSION['uflash'] = ['type'=>'success','msg'=>'User deleted.'];
    header('Location: users.php'); exit();
}

$search = trim($_GET['search'] ?? '');
$where  = '1';
$params = [];
if ($search) { $where .= ' AND (name LIKE ? OR email LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }

$users = $pdo->prepare("SELECT u.*, r.role_name AS role_name FROM users u LEFT JOIN roles r ON r.id=u.role_id WHERE $where ORDER BY u.id DESC");
$users->execute($params); $users = $users->fetchAll();

$roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();
$flash = $_SESSION['uflash'] ?? null;
unset($_SESSION['uflash']);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Users — BMW Admin</title><?php include 'partials/head.php'; ?></head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-users"></i> Users</div>
            <span style="font-size:0.78rem;color:var(--adm-muted);"><?= count($users) ?> users</span>
        </div>
        <div class="adm-content">

            <?php if ($flash): ?>
            <div class="adm-alert adm-alert--<?= $flash['type'] ?>"><i class="fas fa-check-circle"></i> <?= $flash['msg'] ?></div>
            <?php endif; ?>

            <!-- Search -->
            <div class="adm-card" style="padding:14px 20px;margin-bottom:16px;">
                <form method="GET" style="display:flex;gap:10px;align-items:flex-end;">
                    <div class="adm-field" style="flex:1;">
                        <label class="adm-label">Search by Name or Email</label>
                        <input class="adm-input" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search…">
                    </div>
                    <button class="adm-btn adm-btn--primary" type="submit"><i class="fas fa-search"></i> Search</button>
                    <?php if ($search): ?><a href="users.php" class="adm-btn adm-btn--ghost"><i class="fas fa-times"></i> Clear</a><?php endif; ?>
                </form>
            </div>

            <div class="adm-card" style="padding:0;overflow:hidden;">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr>
                            <th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th><th>Actions</th>
                        </tr></thead>
                        <tbody>
                        <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td style="color:var(--adm-muted);"><?= $u['id'] ?></td>
                            <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
                            <td style="color:var(--adm-muted);"><?= htmlspecialchars($u['email']) ?></td>
                            <td style="color:var(--adm-muted);"><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                            <td>
                                <form method="POST" style="display:inline-flex;gap:6px;align-items:center;">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <select class="adm-select" name="role_id" style="padding:5px 8px;font-size:0.75rem;width:auto;" onchange="this.form.submit()">
                                        <?php foreach ($roles as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= $r['id']==$u['role_id']?'selected':'' ?>><?= htmlspecialchars($r['role_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="change_role" value="1">
                                </form>
                            </td>
                            <td style="color:var(--adm-muted);font-size:0.76rem;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <?php if ($u['role_id'] != 1): ?>
                                <a href="?delete=<?= $u['id'] ?>" class="adm-btn adm-btn--danger adm-btn--sm"
                                   onclick="return confirm('Delete <?= htmlspecialchars($u['name'], ENT_QUOTES) ?>?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php else: ?>
                                <span class="adm-badge adm-badge--blue">Super Admin</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--adm-muted);">No users found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
</body></html>
