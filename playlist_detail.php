<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$userId = (int)$_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);
$playlist = get_playlist($id, $userId);
if (!$playlist) { redirect('playlists.php'); }

$items = get_playlist_items($id);
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect("playlist_detail.php?id=$id"); }
    $action = $_POST['action'] ?? '';
    if ($action === 'remove_song') {
        db()->prepare("DELETE FROM playlist_items WHERE playlist_id=? AND song_id=?")->execute([$id, (int)$_POST['item_id']]);
    } elseif ($action === 'remove_video') {
        db()->prepare("DELETE FROM playlist_items WHERE playlist_id=? AND video_id=?")->execute([$id, (int)$_POST['item_id']]);
    }
    redirect("playlist_detail.php?id=$id");
}

render_header($playlist['name'], '');
?>
<div class="container-fluid px-3 px-lg-4 py-3">
    <a href="<?= url('playlists.php') ?>" class="link-underline small text-secondary"><i class="bi bi-arrow-left"></i> Back to Playlists</a>
</div>
<div class="container-fluid px-3 px-lg-4 py-3">
    <span class="chip chip-brand mb-2"><i class="bi bi-music-note-list"></i> Playlist</span>
    <h1 class="section-title mb-2"><?= e($playlist['name']) ?></h1>
    <?php if ($playlist['description']): ?><p class="text-secondary"><?= e($playlist['description']) ?></p><?php endif; ?>
    <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>

    <h2 class="section-title fs-5 mt-4 mb-3">Songs (<?= count($items['songs']) ?>)</h2>
    <?php if (empty($items['songs'])): ?>
        <div class="empty-state"><i class="bi bi-music-note"></i><p>No songs in this playlist.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4 mb-4">
            <?php foreach ($items['songs'] as $s): ?>
                <div class="col-6 col-md-3 col-lg-2 position-relative">
                    <?php render_media_card($s, 'song', true); ?>
                    <form method="post" action="" class="position-absolute top-0 end-0 p-2" onsubmit="return confirm('Remove from playlist?')">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="remove_song">
                        <input type="hidden" name="item_id" value="<?= $s['id'] ?>">
                        <button class="btn btn-sm btn-danger rounded-circle p-1 lh-1" style="width:24px;height:24px"><i class="bi bi-x"></i></button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="section-title fs-5 mb-3">Videos (<?= count($items['videos']) ?>)</h2>
    <?php if (empty($items['videos'])): ?>
        <div class="empty-state"><i class="bi bi-play-btn"></i><p>No videos in this playlist.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4">
            <?php foreach ($items['videos'] as $v): ?>
                <div class="col-6 col-md-3 col-lg-2 position-relative">
                    <?php render_media_card($v, 'video', true); ?>
                    <form method="post" action="" class="position-absolute top-0 end-0 p-2" onsubmit="return confirm('Remove from playlist?')">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="remove_video">
                        <input type="hidden" name="item_id" value="<?= $v['id'] ?>">
                        <button class="btn btn-sm btn-danger rounded-circle p-1 lh-1" style="width:24px;height:24px"><i class="bi bi-x"></i></button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php render_footer(); ?>
