<?php
$pageTitle = 'Manage Artists';
$section = 'artists';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../includes/models.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('admin/artists.php'); }
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        db()->prepare("INSERT INTO artists (name, bio, image_url, social_facebook, social_twitter, social_instagram) VALUES (?,?,?,?,?,?)")
            ->execute([trim($_POST['name']), trim($_POST['bio'] ?? ''), trim($_POST['image_url'] ?? ''), trim($_POST['social_facebook'] ?? ''), trim($_POST['social_twitter'] ?? ''), trim($_POST['social_instagram'] ?? '')]);
        set_flash('success', 'Artist added.');
    } elseif ($action === 'delete') {
        db()->prepare("DELETE FROM artists WHERE id = ?")->execute([(int)$_POST['id']]);
        set_flash('success', 'Artist deleted.');
    }
    redirect('admin/artists.php');
}

require_once __DIR__ . '/includes/header.php';
$flash = get_flash();
$artists = get_artists();
?>
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-secondary mb-0"><?= count($artists) ?> artists</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Artist</button>
</div>
<div class="admin-table table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Photo</th><th>Name</th><th>Bio</th><th>Songs</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($artists as $a): ?>
            <tr>
                <td><img src="<?= e(media_url($a['image_url'])) ?>" alt="" class="thumb-sm rounded-circle"></td>
                <td class="fw-semibold text-white"><?= e($a['name']) ?></td>
                <td class="small text-secondary" style="max-width:300px"><?= e($a['bio'] ?? '—') ?></td>
                <td><?= $a['song_count'] ?></td>
                <td>
                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this artist?')">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <button class="btn btn-sm btn-ghost text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="" class="modal-content card-media">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="add">
            <div class="modal-header border-0"><h5 class="modal-title text-white">Add Artist</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
                <div class="col-md-6"><label class="form-label">Name *</label><input name="name" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Image URL</label><input name="image_url" class="form-control" placeholder="https://…"></div>
                <div class="col-12"><label class="form-label">Bio</label><textarea name="bio" rows="2" class="form-control"></textarea></div>
                <div class="col-md-4"><label class="form-label">Facebook</label><input name="social_facebook" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Twitter/X</label><input name="social_twitter" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Instagram</label><input name="social_instagram" class="form-control"></div>
            </div>
            <div class="modal-footer border-0"><button class="btn btn-primary">Add Artist</button></div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
