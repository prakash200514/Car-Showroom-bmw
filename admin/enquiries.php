<?php
session_start();
include '../config/database.php';
include '../includes/functions.php';
// if (!isAdmin()) { header('Location: login.php'); exit(); }

// View full enquiry
$viewId = intval($_GET['view'] ?? 0);
$viewEnq = null;
if ($viewId) {
    $s = $pdo->prepare("SELECT * FROM enquiries WHERE id=?");
    $s->execute([$viewId]); $viewEnq = $s->fetch();
}

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM enquiries WHERE id=?")->execute([$_GET['delete']]);
    $_SESSION['eflash'] = ['type'=>'success','msg'=>'Enquiry deleted.'];
    header('Location: enquiries.php'); exit();
}

$limit  = 15;
$page   = max(1, intval($_GET['page'] ?? 1));
$offset = ($page-1)*$limit;

$enq = $pdo->prepare("SELECT * FROM enquiries ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
$enq->execute(); $enq = $enq->fetchAll();

$total = $pdo->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
$totalPages = max(1, ceil($total/$limit));

$flash = $_SESSION['eflash'] ?? null;
unset($_SESSION['eflash']);
?>
<!DOCTYPE html>
<html lang="en">
<head><title>Enquiries — BMW Admin</title><?php include 'partials/head.php'; ?>
<style>
.enq-modal { display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center; }
.enq-modal.open { display:flex; }
.enq-modal__box { background:#fff;max-width:560px;width:90%;padding:28px;position:relative;border:1px solid #e0e0e0; }
.enq-modal__close { position:absolute;top:14px;right:16px;font-size:1.2rem;cursor:pointer;color:#999;background:none;border:none; }
</style>
</head>
<body>
<div class="adm-layout">
    <?php include 'partials/sidebar.php'; ?>
    <div class="adm-main">
        <div class="adm-topbar">
            <div class="adm-topbar__title"><i class="fas fa-envelope"></i> Enquiries</div>
            <span style="font-size:0.78rem;color:var(--adm-muted);"><?= number_format($total) ?> total</span>
        </div>
        <div class="adm-content">

            <?php if ($flash): ?>
            <div class="adm-alert adm-alert--<?= $flash['type'] ?>"><i class="fas fa-check-circle"></i> <?= $flash['msg'] ?></div>
            <?php endif; ?>

            <div class="adm-card" style="padding:0;overflow:hidden;">
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr>
                            <th>#</th><th>Name</th><th>Email</th><th>Subject</th><th>Message Preview</th><th>Date</th><th>Actions</th>
                        </tr></thead>
                        <tbody>
                        <?php if (count($enq) > 0): ?>
                        <?php foreach ($enq as $e): ?>
                        <tr>
                            <td style="color:var(--adm-muted);"><?= $e['id'] ?></td>
                            <td><strong><?= htmlspecialchars($e['name']) ?></strong></td>
                            <td style="color:var(--adm-muted);font-size:0.78rem;"><?= htmlspecialchars($e['email']) ?></td>
                            <td><?= htmlspecialchars($e['subject'] ?? '—') ?></td>
                            <td style="color:var(--adm-muted);font-size:0.78rem;"><?= htmlspecialchars(mb_strimwidth($e['message'] ?? '', 0, 70, '…')) ?></td>
                            <td style="color:var(--adm-muted);font-size:0.76rem;"><?= date('d M Y', strtotime($e['created_at'])) ?></td>
                            <td style="white-space:nowrap;">
                                <button class="adm-btn adm-btn--outline adm-btn--sm" onclick='openEnq(<?= htmlspecialchars(json_encode($e), ENT_QUOTES) ?>)'>
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <a href="?delete=<?= $e['id'] ?>" class="adm-btn adm-btn--danger adm-btn--sm"
                                   onclick="return confirm('Delete this enquiry?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--adm-muted);">No enquiries yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="adm-pagination" style="margin-top:12px;">
                <?php for ($i=1;$i<=$totalPages;$i++): ?>
                <a href="?page=<?= $i ?>" class="adm-page <?= $i===$page?'active':'' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal -->
<div class="enq-modal" id="enqModal">
    <div class="enq-modal__box">
        <button class="enq-modal__close" onclick="document.getElementById('enqModal').classList.remove('open')">&times;</button>
        <div style="font-size:0.65rem;color:var(--adm-muted);letter-spacing:0.1em;text-transform:uppercase;margin-bottom:4px;">ENQUIRY</div>
        <h3 id="mSubject" style="font-size:1.1rem;font-weight:800;margin-bottom:14px;"></h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;padding:12px;background:#fafafa;border:1px solid #eee;">
            <div><div style="font-size:0.65rem;text-transform:uppercase;color:var(--adm-muted);letter-spacing:0.1em;">FROM</div><strong id="mName"></strong></div>
            <div><div style="font-size:0.65rem;text-transform:uppercase;color:var(--adm-muted);letter-spacing:0.1em;">EMAIL</div><a id="mEmail" href="" style="color:var(--adm-blue);font-size:0.82rem;"></a></div>
        </div>
        <div style="font-size:0.78rem;line-height:1.7;white-space:pre-wrap;background:#f8f9fa;padding:14px;border:1px solid #eee;max-height:240px;overflow-y:auto;" id="mMsg"></div>
        <div style="margin-top:14px;font-size:0.7rem;color:#bbb;" id="mDate"></div>
    </div>
</div>
<script>
function openEnq(e) {
    document.getElementById('mSubject').textContent = e.subject || '(No Subject)';
    document.getElementById('mName').textContent = e.name;
    const emailEl = document.getElementById('mEmail');
    emailEl.textContent = e.email;
    emailEl.href = 'mailto:' + e.email;
    document.getElementById('mMsg').textContent = e.message || '(No message)';
    document.getElementById('mDate').textContent = 'Received: ' + e.created_at;
    document.getElementById('enqModal').classList.add('open');
}
document.getElementById('enqModal').addEventListener('click', function(ev) {
    if (ev.target === this) this.classList.remove('open');
});
</script>
</body></html>
