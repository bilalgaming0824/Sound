<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$userId = (int)$_SESSION['user_id'];
$favSongs = get_favourite_songs($userId);
$favVideos = get_favourite_videos($userId);
$playlists = get_playlists($userId);
$history = get_history($userId, 12);

render_header('My Dashboard', '');
?>
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <span class="avatar" style="width:56px;height:56px;font-size:1.5rem"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></span>
        <div>
            <h1 class="section-title mb-0">Hi, <?= e($_SESSION['username']) ?></h1>
            <p class="text-secondary small mb-0">Welcome to your dashboard</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="card-media p-3 text-center"><i class="bi bi-heart-fill text-danger fs-3"></i><div class="fw-bold text-white"><?= count($favSongs) ?></div><div class="small text-secondary">Fav. Songs</div></div></div>
        <div class="col-6 col-md-3"><div class="card-media p-3 text-center"><i class="bi bi-play-btn text-brand fs-3"></i><div class="fw-bold text-white"><?= count($favVideos) ?></div><div class="small text-secondary">Fav. Videos</div></div></div>
        <div class="col-6 col-md-3"><div class="card-media p-3 text-center"><i class="bi bi-music-note-list text-brand fs-3"></i><div class="fw-bold text-white"><?= count($playlists) ?></div><div class="small text-secondary">Playlists</div></div></div>
        <div class="col-6 col-md-3"><div class="card-media p-3 text-center"><i class="bi bi-clock-history text-brand fs-3"></i><div class="fw-bold text-white"><?= count($history) ?></div><div class="small text-secondary">Recently Played</div></div></div>
    </div>

    <h2 class="section-title fs-5 mb-3"><i class="bi bi-heart-fill text-danger"></i> Favourite Songs</h2>
    <?php if (empty($favSongs)): ?>
        <div class="empty-state"><i class="bi bi-heart"></i><p>No favourite songs yet.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4 mb-4">
            <?php foreach ($favSongs as $s): render_media_card($s, 'song'); endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="section-title fs-5 mb-3"><i class="bi bi-play-btn text-brand"></i> Favourite Videos</h2>
    <?php if (empty($favVideos)): ?>
        <div class="empty-state"><i class="bi bi-heart"></i><p>No favourite videos yet.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4 mb-4">
            <?php foreach ($favVideos as $v): render_media_card($v, 'video'); endforeach; ?>
        </div>
    <?php endif; ?>

    <h2 class="section-title fs-5 mb-3"><i class="bi bi-clock-history text-brand"></i> Recently Played</h2>
    <?php if (empty($history)): ?>
        <div class="empty-state"><i class="bi bi-clock-history"></i><p>No history yet.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4">
            <?php foreach ($history as $h):
                $img = media_url($h['image_url']);
                $link = $h['media_type'] === 'video' ? 'video_detail.php?id=' . $h['media_id'] : 'song_detail.php?id=' . $h['media_id']; ?>
                <div class="col-6 col-md-3 col-lg-2">
                    <a href="<?= url($link) ?>" class="card-media d-block text-decoration-none">
                        <div class="media-thumb"><img src="<?= e($img) ?>" alt="" loading="lazy"><div class="media-overlay"></div>
                        <span class="badge-type"><i class="bi <?= $h['media_type']==='video'?'bi-play-btn':'bi-music-note-beamed' ?>"></i></span></div>
                        <div class="card-body"><p class="card-title"><?= e($h['title']) ?></p><p class="card-subtitle"><?= format_date($h['played_at']) ?></p></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php render_footer(); ?>
