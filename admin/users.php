<?php
$pageTitle = 'Manage Users';
$section = 'users';
require_once __DIR__ . '/includes/header.php';
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('admin/users.php'); }
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle_role') {
        $uid = (int)$_POST['id'];
        $user = get_user_by_id($uid);
        if ($user) {
            $newRole = $user['role'] === 'admin' ? 'user' : 'admin';
            if ($uid === (int)$_SESSION['user_id'] && $newRole === 'user') {
                set_flash('danger', 'You cannot remove your own admin access here.');
            } else {
                db()->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$newRole, $uid]);
                set_flash('success', 'User role updated.');
            }
        }
    } elseif ($action === 'delete') {
        $uid = (int)$_POST['id'];
        if ($uid === (int)$_SESSION['user_id']) { set_flash('danger', 'You cannot delete your own account.'); }
        else { db()->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]); set_flash('success', 'User deleted.'); }
    }
    redirect('admin/users.php');
}
$users = get_all_users();
?>
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<p class="text-secondary mb-3"><?= count($users) ?> users</p>
<div class="admin-table table-responsive">
    <table class="table table-hover">
        <thead><tr><th>User</th><th>Email</th><th>Name</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><span class="avatar me-2"><?= strtoupper(substr($u['username'],0,1)) ?></span><?= e($u['username']) ?> <?= $u['id']==$_SESSION['user_id']?'<span class="text-secondary small">(you)</span>':'' ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e($u['full_name']) ?></td>
                <td><span class="badge <?= $u['role']==='admin'?'bg-warning':'bg-secondary' ?>"><?= e($u['role']) ?></span></td>
                <td class="small text-secondary"><?= format_date($u['created_at']) ?></td>
                <td>
                    <form method="post" action="" class="d-inline">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="toggle_role">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button class="btn btn-sm btn-ghost">Toggle role</button>
                    </form>
                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this user?')">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button class="btn btn-sm btn-ghost text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
