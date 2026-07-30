<?php
$pageTitle = 'Manage Songs';
$section = 'songs';
require_once __DIR__ . '/includes/header.php';

$artists = get_artists();
$genres = get_genres();
$languages = get_languages();
$albums = get_albums();
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('admin/songs.php'); }
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $imageUrl = handle_upload('image_file', 'image') ?? trim($_POST['image_url'] ?? '');
        $audioUrl = handle_upload('audio_file', 'audio') ?? trim($_POST['audio_url'] ?? '');
        $stmt = db()->prepare("INSERT INTO songs (title, description, image_url, audio_url, artist_id, album_id, genre_id, language_id, year, duration, is_new) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            trim($_POST['title']), trim($_POST['description'] ?? ''), $imageUrl, $audioUrl,
            (int)($_POST['artist_id'] ?? 0) ?: null, (int)($_POST['album_id'] ?? 0) ?: null,
            (int)($_POST['genre_id'] ?? 0) ?: null, (int)($_POST['language_id'] ?? 0) ?: null,
            (int)($_POST['year'] ?? 0) ?: null, (int)($_POST['duration'] ?? 0) ?: null, isset($_POST['is_new']) ? 1 : 0,
        ]);
        set_flash('success', 'Song added.');
    } elseif ($action === 'delete') {
        db()->prepare("DELETE FROM songs WHERE id = ?")->execute([(int)$_POST['id']]);
        set_flash('success', 'Song deleted.');
    }
    redirect('admin/songs.php');
}

$songs = get_songs();
?>
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-secondary mb-0"><?= count($songs) ?> songs</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Song</button>
</div>
<div class="admin-table table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Thumb</th><th>Title</th><th>Artist</th><th>Album</th><th>Year</th><th>Views</th><th>New</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($songs as $s): ?>
            <tr>
                <td><img src="<?= e($s['image_url']) ?>" alt="" class="thumb-sm"></td>
                <td class="fw-semibold text-white"><?= e($s['title']) ?></td>
                <td><?= e($s['artist_name'] ?? '—') ?></td>
                <td><?= e($s['album_title'] ?? '—') ?></td>
                <td><?= e($s['year'] ?? '—') ?></td>
                <td><?= number_format($s['views']) ?></td>
                <td><?= $s['is_new'] ? '<span class="badge bg-warning">NEW</span>' : '—' ?></td>
                <td>
                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this song?')">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
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
        <form method="post" action="" enctype="multipart/form-data" class="modal-content card-media">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="add">
            <div class="modal-header border-0"><h5 class="modal-title text-white">Add Song</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
                <div class="col-md-8"><label class="form-label">Title *</label><input name="title" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Year</label><input type="number" name="year" class="form-control"></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="2" class="form-control"></textarea></div>
                <div class="col-md-6"><label class="form-label">Cover Image (JPG/PNG only)</label><input type="file" name="image_file" accept=".jpg,.jpeg,.png" class="form-control"><div class="form-text">or paste URL:</div><input name="image_url" class="form-control" placeholder="https://…"></div>
                <div class="col-md-6"><label class="form-label">Audio File (MP3 only)</label><input type="file" name="audio_file" accept=".mp3,audio/mpeg" class="form-control"><div class="form-text">or paste URL:</div><input name="audio_url" class="form-control" placeholder="https://…"></div>
                <div class="col-md-3"><label class="form-label">Artist</label><select name="artist_id" class="form-select"><option value="">—</option><?php foreach ($artists as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Album</label><select name="album_id" class="form-select"><option value="">—</option><?php foreach ($albums as $al): ?><option value="<?= $al['id'] ?>"><?= e($al['title']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Genre</label><select name="genre_id" class="form-select"><option value="">—</option><?php foreach ($genres as $g): ?><option value="<?= $g['id'] ?>"><?= e($g['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Language</label><select name="language_id" class="form-select"><option value="">—</option><?php foreach ($languages as $l): ?><option value="<?= $l['id'] ?>"><?= e($l['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Duration (sec)</label><input type="number" name="duration" class="form-control"></div>
                <div class="col-md-6 d-flex align-items-end"><label class="d-flex align-items-center gap-2"><input type="checkbox" name="is_new" class="form-check-input"> Mark as NEW</label></div>
            </div>
            <div class="modal-footer border-0"><button class="btn btn-primary">Add Song</button></div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
