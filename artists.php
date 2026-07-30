<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

$artists = get_artists();
[$artists, $pagination] = paginate($artists, 12);
render_header('Artists', 'artists');
?>
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <h1 class="section-title">Artists</h1>
            <p class="text-secondary small mb-0"><?= count($artists) ?> artists</p>
        </div>
    </div>
    <?php if (empty($artists)): ?>
        <div class="empty-state"><i class="bi bi-mic"></i><p>No artists found.</p></div>
    <?php else: ?>
    <div class="row g-3 g-lg-4">
        <?php foreach ($artists as $a):
            $img = media_url($a['image_url']);
            $songCount = (int)($a['song_count'] ?? 0);
            $listeners = max($songCount * 340, 1200);
        ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <a href="<?= url('artist_detail.php?id=' . $a['id']) ?>" class="mc-artist-card">
                    <div class="mc-artist-avatar-wrap">
                        <img src="<?= e($img) ?>" alt="<?= e($a['name']) ?>" class="mc-artist-avatar" loading="lazy">
                        <span class="mc-artist-play"><i class="bi bi-play-fill"></i></span>
                    </div>
                    <h3 class="mc-artist-name"><?= e($a['name']) ?></h3>
                    <p class="mc-artist-role">Artist</p>
                    <div class="mc-artist-meta">
                        <span><i class="bi bi-music-note-beamed"></i> <?= $songCount ?></span>
                        <span><i class="bi bi-headphones"></i> <?= number_format($listeners) ?></span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?= $pagination ?>
    <?php endif; ?>
</div>
<?php render_footer(); ?>
