<?php
$pageTitle = 'Newsletter';
$section = 'newsletter';
require_once __DIR__ . '/includes/header.php';
$subs = db()->query("SELECT * FROM newsletter ORDER BY subscribed_at DESC")->fetchAll();
?>
<p class="text-secondary mb-3"><?= count($subs) ?> subscribers</p>
<div class="admin-table table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Email</th><th>Subscribed</th></tr></thead>
        <tbody>
        <?php foreach ($subs as $s): ?>
            <tr><td class="text-white"><?= e($s['email']) ?></td><td class="small text-secondary"><?= format_date($s['subscribed_at']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
