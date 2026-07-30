<?php
$pageTitle = 'Manage Videos';
$section = 'videos';
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../includes/models.php';

// Handle POST before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('admin/videos.php'); }
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $imageUrl = handle_upload('image_file', 'image') ?? trim($_POST['image_url'] ?? '');
        $videoUrl = handle_upload('video_file', 'video') ?? trim($_POST['video_url'] ?? '');
        $stmt = db()->prepare("INSERT INTO videos (title, description, image_url, video_url, artist_id, album_id, genre_id, language_id, year, duration, is_new) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            trim($_POST['title']), trim($_POST['description'] ?? ''), $imageUrl, $videoUrl,
            (int)($_POST['artist_id'] ?? 0) ?: null, (int)($_POST['album_id'] ?? 0) ?: null,
            (int)($_POST['genre_id'] ?? 0) ?: null, (int)($_POST['language_id'] ?? 0) ?: null,
            (int)($_POST['year'] ?? 0) ?: null, (int)($_POST['duration'] ?? 0) ?: null, isset($_POST['is_new']) ? 1 : 0,
        ]);
        set_flash('success', 'Video added.');
    } elseif ($action === 'edit') {
        $vid = (int)$_POST['id'];
        $imageUrl = handle_upload('image_file', 'image') ?? trim($_POST['image_url'] ?? '');
        $videoUrl = handle_upload('video_file', 'video') ?? trim($_POST['video_url'] ?? '');
        $stmt = db()->prepare("UPDATE videos SET title=?, description=?, image_url=?, video_url=?, artist_id=?, album_id=?, genre_id=?, language_id=?, year=?, duration=?, is_new=? WHERE id=?");
        $stmt->execute([
            trim($_POST['title']), trim($_POST['description'] ?? ''), $imageUrl, $videoUrl,
            (int)($_POST['artist_id'] ?? 0) ?: null, (int)($_POST['album_id'] ?? 0) ?: null,
            (int)($_POST['genre_id'] ?? 0) ?: null, (int)($_POST['language_id'] ?? 0) ?: null,
            (int)($_POST['year'] ?? 0) ?: null, (int)($_POST['duration'] ?? 0) ?: null, isset($_POST['is_new']) ? 1 : 0, $vid,
        ]);
        set_flash('success', 'Video updated.');
    } elseif ($action === 'delete') {
        db()->prepare("DELETE FROM videos WHERE id = ?")->execute([(int)$_POST['id']]);
        set_flash('success', 'Video deleted.');
    }
    redirect('admin/videos.php');
}

require_once __DIR__ . '/includes/header.php';

$artists = get_artists();
$genres = get_genres();
$languages = get_languages();
$albums = get_albums();
$flash = get_flash();
$videos = get_videos();
?>
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-secondary mb-0"><?= count($videos) ?> videos</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Upload Video</button>
</div>
<div class="admin-table table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Thumb</th><th>Title</th><th>Artist</th><th>Year</th><th>Views</th><th>New</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($videos as $v): ?>
            <tr>
                <td><img src="<?= e(media_url($v['image_url'])) ?>" alt="" class="thumb-sm"></td>
                <td class="fw-semibold text-white"><?= e($v['title']) ?></td>
                <td><?= e($v['artist_name'] ?? '—') ?></td>
                <td><?= e($v['year'] ?? '—') ?></td>
                <td><?= number_format($v['views']) ?></td>
                <td><?= $v['is_new'] ? '<span class="badge bg-warning">NEW</span>' : '—' ?></td>
                <td>
                    <button class="btn btn-sm btn-ghost" data-bs-toggle="modal" data-bs-target="#editModal"
                        data-id="<?= $v['id'] ?>" data-title="<?= e($v['title']) ?>"
                        data-desc="<?= e($v['description'] ?? '') ?>"
                        data-image="<?= e($v['image_url'] ?? '') ?>"
                        data-video="<?= e($v['video_url'] ?? '') ?>"
                        data-artist="<?= $v['artist_id'] ?? '' ?>"
                        data-album="<?= $v['album_id'] ?? '' ?>"
                        data-genre="<?= $v['genre_id'] ?? '' ?>"
                        data-language="<?= $v['language_id'] ?? '' ?>"
                        data-year="<?= $v['year'] ?? '' ?>"
                        data-duration="<?= $v['duration'] ?? '' ?>"
                        data-isnew="<?= $v['is_new'] ?>"><i class="bi bi-pencil"></i></button>
                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this video?')">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $v['id'] ?>">
                        <button class="btn btn-sm btn-ghost text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="" enctype="multipart/form-data" class="modal-content card-media">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="add">
            <div class="modal-header border-0"><h5 class="modal-title text-white">Upload Video</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
                <div class="col-md-8"><label class="form-label">Title *</label><input name="title" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Year</label><input type="number" name="year" class="form-control"></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="2" class="form-control"></textarea></div>
                <div class="col-md-6"><label class="form-label">Thumbnail Image (JPG/PNG)</label><input type="file" name="image_file" accept=".jpg,.jpeg,.png" class="form-control"><div class="form-text">or paste URL:</div><input name="image_url" class="form-control" placeholder="https://…"></div>
                <div class="col-md-6"><label class="form-label">Video File (MP4/WebM)</label><input type="file" name="video_file" accept=".mp4,.webm,video/mp4,video/webm" class="form-control"><div class="form-text">or paste URL:</div><input name="video_url" class="form-control" placeholder="https://…/video.mp4"></div>
                <div class="col-md-3"><label class="form-label">Artist</label><select name="artist_id" class="form-select"><option value="">—</option><?php foreach ($artists as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Album</label><select name="album_id" class="form-select"><option value="">—</option><?php foreach ($albums as $al): ?><option value="<?= $al['id'] ?>"><?= e($al['title']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Genre</label><select name="genre_id" class="form-select"><option value="">—</option><?php foreach ($genres as $g): ?><option value="<?= $g['id'] ?>"><?= e($g['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Language</label><select name="language_id" class="form-select"><option value="">—</option><?php foreach ($languages as $l): ?><option value="<?= $l['id'] ?>"><?= e($l['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Duration (sec)</label><input type="number" name="duration" class="form-control"></div>
                <div class="col-md-6 d-flex align-items-end"><label class="d-flex align-items-center gap-2"><input type="checkbox" name="is_new" class="form-check-input"> Mark as NEW</label></div>
            </div>
            <div class="modal-footer border-0"><button class="btn btn-primary">Add Video</button></div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="post" action="" enctype="multipart/form-data" class="modal-content card-media">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div class="modal-header border-0"><h5 class="modal-title text-white">Edit Video</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body row g-3">
                <div class="col-md-8"><label class="form-label">Title *</label><input name="title" id="editTitle" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Year</label><input type="number" name="year" id="editYear" class="form-control"></div>
                <div class="col-12"><label class="form-label">Description</label><textarea name="description" id="editDesc" rows="2" class="form-control"></textarea></div>
                <div class="col-md-6"><label class="form-label">Thumbnail Image (JPG/PNG)</label><input type="file" name="image_file" accept=".jpg,.jpeg,.png" class="form-control"><div class="form-text">or paste URL:</div><input name="image_url" id="editImage" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Video File (MP4/WebM)</label><input type="file" name="video_file" accept=".mp4,.webm,video/mp4,video/webm" class="form-control"><div class="form-text">or paste URL:</div><input name="video_url" id="editVideo" class="form-control"></div>
                <div class="col-md-3"><label class="form-label">Artist</label><select name="artist_id" id="editArtist" class="form-select"><option value="">—</option><?php foreach ($artists as $a): ?><option value="<?= $a['id'] ?>"><?= e($a['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Album</label><select name="album_id" id="editAlbum" class="form-select"><option value="">—</option><?php foreach ($albums as $al): ?><option value="<?= $al['id'] ?>"><?= e($al['title']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Genre</label><select name="genre_id" id="editGenre" class="form-select"><option value="">—</option><?php foreach ($genres as $g): ?><option value="<?= $g['id'] ?>"><?= e($g['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-3"><label class="form-label">Language</label><select name="language_id" id="editLanguage" class="form-select"><option value="">—</option><?php foreach ($languages as $l): ?><option value="<?= $l['id'] ?>"><?= e($l['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label class="form-label">Duration (sec)</label><input type="number" name="duration" id="editDuration" class="form-control"></div>
                <div class="col-md-6 d-flex align-items-end"><label class="d-flex align-items-center gap-2"><input type="checkbox" name="is_new" id="editIsNew" class="form-check-input"> Mark as NEW</label></div>
            </div>
            <div class="modal-footer border-0"><button class="btn btn-primary">Save Changes</button></div>
        </form>
    </div>
</div>

<script>
var editModal=document.getElementById('editModal');
if(editModal){editModal.addEventListener('show.bs.modal',function(e){
    var b=e.relatedTarget;
    document.getElementById('editId').value=b.dataset.id;
    document.getElementById('editTitle').value=b.dataset.title||'';
    document.getElementById('editDesc').value=b.dataset.desc||'';
    document.getElementById('editImage').value=b.dataset.image||'';
    document.getElementById('editVideo').value=b.dataset.video||'';
    document.getElementById('editYear').value=b.dataset.year||'';
    document.getElementById('editDuration').value=b.dataset.duration||'';
    document.getElementById('editArtist').value=b.dataset.artist||'';
    document.getElementById('editAlbum').value=b.dataset.album||'';
    document.getElementById('editGenre').value=b.dataset.genre||'';
    document.getElementById('editLanguage').value=b.dataset.language||'';
    document.getElementById('editIsNew').checked=b.dataset.isnew==1;
});}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
