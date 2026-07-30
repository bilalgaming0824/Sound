<?php
$pageTitle = 'Activity Logs';
$section = 'logs';
require_once __DIR__ . '/includes/header.php';
$logs = get_activity_logs(100);
?>
<p class="text-secondary mb-3"><?= count($logs) ?> recent activities</p>
<div class="admin-table table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Details</th><th>IP Address</th></tr></thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td class="small text-secondary"><?= e($log['created_at']) ?></td>
                <td><?= $log['username'] ? e($log['username']) : '<span class="text-secondary">—</span>' ?></td>
                <td><span class="chip chip-brand"><?= e($log['action']) ?></span></td>
                <td class="small"><?= e($log['details'] ?? '—') ?></td>
                <td class="small text-secondary"><?= e($log['ip_address'] ?? '—') ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
            <tr><td colspan="5" class="text-center text-secondary py-4">No activity logged yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
