<?php
$pageTitle = 'Dashboard';
$section = 'dashboard';
require_once __DIR__ . '/includes/header.php';
$stats = admin_stats();
$cards = [
    ['label' => 'Songs', 'value' => $stats['songs'], 'icon' => 'bi-music-note-beamed', 'color' => 'rgba(108,99,255,0.15)', 'text' => 'text-brand'],
    ['label' => 'Videos', 'value' => $stats['videos'], 'icon' => 'bi-play-btn', 'color' => 'rgba(255,77,109,0.15)', 'text' => 'text-danger'],
    ['label' => 'Artists', 'value' => $stats['artists'], 'icon' => 'bi-mic', 'color' => 'rgba(34,197,94,0.15)', 'text' => 'text-success'],
    ['label' => 'Albums', 'value' => $stats['albums'], 'icon' => 'bi-disc', 'color' => 'rgba(168,85,247,0.15)', 'text' => 'text-purple'],
    ['label' => 'Genres', 'value' => $stats['genres'], 'icon' => 'bi-tag', 'color' => 'rgba(236,72,153,0.15)', 'text' => 'text-pink'],
    ['label' => 'Languages', 'value' => $stats['languages'], 'icon' => 'bi-globe', 'color' => 'rgba(20,184,166,0.15)', 'text' => 'text-teal'],
    ['label' => 'Users', 'value' => $stats['users'], 'icon' => 'bi-people', 'color' => 'rgba(59,130,246,0.15)', 'text' => 'text-primary'],
    ['label' => 'Reviews', 'value' => $stats['comments'], 'icon' => 'bi-chat-square-text', 'color' => 'rgba(245,158,11,0.15)', 'text' => 'text-warning'],
    ['label' => 'Ratings', 'value' => $stats['ratings'], 'icon' => 'bi-star', 'color' => 'rgba(249,115,22,0.15)', 'text' => 'text-orange'],
];
// chart functions removed by user request
?>
<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <?php foreach ($cards as $c): ?>
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="stat-card d-flex align-items-center gap-3">
                <span class="stat-icon" style="background:<?= $c['color'] ?>"><i class="bi <?= $c['icon'] ?> <?= $c['text'] ?>"></i></span>
                <div><div class="fw-bold text-white fs-4"><?= $c['value'] ?></div><div class="small text-secondary"><?= $c['label'] ?></div></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Quick Actions + Recent -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-media p-4">
            <h3 class="fw-bold text-white mb-3">Quick Actions</h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= url('admin/songs.php') ?>" class="btn btn-ghost"><i class="bi bi-music-note-beamed"></i> Manage Songs</a>
                <a href="<?= url('admin/videos.php') ?>" class="btn btn-ghost"><i class="bi bi-play-btn"></i> Manage Videos</a>
                <a href="<?= url('admin/albums.php') ?>" class="btn btn-ghost"><i class="bi bi-disc"></i> Albums</a>
                <a href="<?= url('admin/artists.php') ?>" class="btn btn-ghost"><i class="bi bi-mic"></i> Artists</a>
                <a href="<?= url('admin/users.php') ?>" class="btn btn-ghost"><i class="bi bi-people"></i> Users</a>
                <a href="<?= url('admin/comments.php') ?>" class="btn btn-ghost"><i class="bi bi-chat-square-text"></i> Reviews</a>
                <a href="<?= url('admin/analytics.php') ?>" class="btn btn-ghost"><i class="bi bi-graph-up"></i> Analytics</a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-media p-4">
            <h3 class="fw-bold text-white mb-3">Recent Songs</h3>
            <?php $recent = get_latest_songs(5); foreach ($recent as $s): ?>
                <div class="d-flex align-items-center gap-3 py-2 border-bottom border-ink">
                    <img src="<?= e($s['image_url']) ?>" alt="" class="thumb-sm">
                    <div class="flex-grow-1"><div class="fw-semibold text-white small"><?= e($s['title']) ?></div><div class="text-secondary" style="font-size:0.75rem"><?= e($s['artist_name'] ?? '') ?></div></div>
                    <span class="small text-secondary"><?= number_format($s['views']) ?> views</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
