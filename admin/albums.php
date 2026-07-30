<?php
$pageTitle = 'Manage Albums';
$section = 'albums';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../includes/models.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('admin/albums.php'); }
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        db()->prepare("INSERT INTO albums (title, artist_id, year, image_url) VALUES (?,?,?,?)")
            ->execute([trim($_POST['title']), (int)($_POST['artist_id'] ?? 0) ?: null, (int)($_POST['year'] ?? 0) ?: null, trim($_POST['image_url'] ?? '')]);
        set_flash('success', 'Album added.');
    } elseif ($action === 'delete') {
        db()->prepare("DELETE FROM albums WHERE id = ?")->execute([(int)$_POST['id']]);
        set_flash('success', 'Album deleted.');
    }
    redirect('admin/albums.php');
}

require_once __DIR__ . '/includes/header.php';
$artists = get_artists();
$flash = get_flash();
$albums = get_albums();
?>
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-secondary mb-0"><?= count($albums) ?> albums</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Album</button>
</div>
<div class="admin-table table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Cover</th><th>Title</th><th>Artist</th><th>Year</th><th>Songs</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($albums as $al): ?>
            <tr>
                <td><img src="<?= e(media_url($al['image_url'])) ?>" alt="" class="thumb-sm"></td>
                <td class="fw-semibold text-white"><?= e($al['title']) ?></td>
                <td><?= e($al['artist_name'] ?? '—') ?></td>
                <td><?= e($al['year'] ?? '—') ?></td>
                <td><?= $al['song_count'] ?></td>
                <td>
                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this album?')">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $al['id'] ?>">
                        <button class="btn btn-sm btn-ghost text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="" class="modal-content card-media">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="add">
            <div class="modal-header border-0"><h5 class="modal-title text-white">Add Album</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
                <div class="col-12"><label class="form-label">Title *</label><input name="title" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Artist</label><select name="artist_id" class="form-select"><option value="">—</option><?php foreach ($artists as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Year</label><input type="number" name="year" class="form-control"></div>
                <div class="col-12"><label class="form-label">Image URL</label><input name="image_url" class="form-control" placeholder="https://…"></div>
            </div>
            <div class="modal-footer border-0"><button class="btn btn-primary">Add Album</button></div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
