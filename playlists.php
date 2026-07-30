<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$userId = (int)$_SESSION['user_id'];
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('playlists.php'); }
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name === '') { set_flash('danger', 'Playlist name is required.'); redirect('playlists.php'); }
        db()->prepare("INSERT INTO playlists (user_id, name, description) VALUES (?,?,?)")->execute([$userId, $name, $desc]);
        set_flash('success', 'Playlist created.');
    } elseif ($action === 'delete') {
        $pid = (int)($_POST['playlist_id'] ?? 0);
        db()->prepare("DELETE FROM playlists WHERE id = ? AND user_id = ?")->execute([$pid, $userId]);
        set_flash('success', 'Playlist deleted.');
    } elseif ($action === 'rename') {
        $pid = (int)($_POST['playlist_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name === '') { set_flash('danger', 'Name is required.'); redirect('playlists.php'); }
        db()->prepare("UPDATE playlists SET name=?, description=? WHERE id=? AND user_id=?")->execute([$name, $desc, $pid, $userId]);
        set_flash('success', 'Playlist updated.');
    }
    redirect('playlists.php');
}

$playlists = get_playlists($userId);
render_header('My Playlists', '');
?>
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="section-title mb-0">My Playlists</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal"><i class="bi bi-plus-lg"></i> Create Playlist</button>
    </div>
    <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <?php if (empty($playlists)): ?>
        <div class="empty-state"><i class="bi bi-music-note-list"></i><p>No playlists yet. Create one to get started.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4">
            <?php foreach ($playlists as $p): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card-media p-4">
                        <div class="d-flex align-items-center gap-3">
                            <span class="chip chip-brand fs-3"><i class="bi bi-music-note-list"></i></span>
                            <div class="flex-grow-1">
                                <a href="<?= url('playlist_detail.php?id=' . $p['id']) ?>" class="fw-semibold text-white text-decoration-none fs-5"><?= e($p['name']) ?></a>
                                <div class="small text-secondary"><?= $p['item_count'] ?> items</div>
                            </div>
                        </div>
                        <?php if ($p['description']): ?><p class="text-secondary small mt-2 mb-0"><?= e($p['description']) ?></p><?php endif; ?>
                        <div class="d-flex gap-2 mt-3">
                            <a href="<?= url('playlist_detail.php?id=' . $p['id']) ?>" class="btn btn-ghost btn-sm">Open</a>
                            <button class="btn btn-ghost btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?= $p['id'] ?>" data-name="<?= e($p['name']) ?>" data-desc="<?= e($p['description'] ?? '') ?>"><i class="bi bi-pencil"></i></button>
                            <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this playlist?')">
                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="playlist_id" value="<?= $p['id'] ?>">
                                <button class="btn btn-ghost btn-sm text-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="" class="modal-content card-media">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="create">
            <div class="modal-header border-0"><h5 class="modal-title text-white">Create Playlist</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" rows="2" class="form-control"></textarea></div>
            </div>
            <div class="modal-footer border-0"><button class="btn btn-primary">Create</button></div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="" class="modal-content card-media">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="rename">
            <input type="hidden" name="playlist_id" id="editId">
            <div class="modal-header border-0"><h5 class="modal-title text-white">Edit Playlist</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" id="editName" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="editDesc" rows="2" class="form-control"></textarea></div>
            </div>
            <div class="modal-footer border-0"><button class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>
<script>
var editModal=document.getElementById('editModal');
if(editModal){editModal.addEventListener('show.bs.modal',function(e){var b=e.relatedTarget;document.getElementById('editId').value=b.dataset.id;document.getElementById('editName').value=b.dataset.name;document.getElementById('editDesc').value=b.dataset.desc;});}
</script>
<?php render_footer(); ?>
