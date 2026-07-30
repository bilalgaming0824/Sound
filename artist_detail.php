<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$id = (int)($_GET['id'] ?? 0);
$artist = get_artist($id);
if (!$artist) { redirect('artists.php'); }
$songs = get_artist_songs($id);
$videos = get_artist_videos($id);
$albums = get_artist_albums($id);
render_header($artist['name'], 'artists');
$img = media_url($artist['image_url']);
?>
<div class="container-fluid px-3 px-lg-4 py-3">
    <a href="<?= url('artists.php') ?>" class="link-underline small text-secondary"><i class="bi bi-arrow-left"></i> Back to Artists</a>
</div>
<section class="position-relative overflow-hidden">
    <div class="position-absolute inset-0" style="z-index:-1">
        <img src="<?= e($img) ?>" alt="" class="w-100 h-100" style="object-fit:cover;opacity:0.2;filter:blur(24px)">
        <div class="position-absolute inset-0" style="background:linear-gradient(to bottom, rgba(7,7,13,0.7), var(--secondary))"></div>
    </div>
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-3">
                <div class="rounded-circle overflow-hidden border border-ink" style="aspect-ratio:1/1;max-width:240px;margin:0 auto">
                    <img src="<?= e($img) ?>" alt="<?= e($artist['name']) ?>" class="w-100 h-100" style="object-fit:cover">
                </div>
            </div>
            <div class="col-lg-9">
                <span class="chip chip-brand mb-2"><i class="bi bi-mic"></i> Artist</span>
                <h1 class="hero-title text-white mb-2"><?= e($artist['name']) ?></h1>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="chip"><i class="bi bi-music-note-beamed"></i> <?= count($songs) ?> songs</span>
                    <span class="chip"><i class="bi bi-play-btn"></i> <?= count($videos) ?> videos</span>
                    <span class="chip"><i class="bi bi-disc"></i> <?= count($albums) ?> albums</span>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($artist['social_facebook']): ?><a href="<?= e($artist['social_facebook']) ?>" class="social-btn"><i class="bi bi-facebook"></i></a><?php endif; ?>
                    <?php if ($artist['social_twitter']): ?><a href="<?= e($artist['social_twitter']) ?>" class="social-btn"><i class="bi bi-twitter-x"></i></a><?php endif; ?>
                    <?php if ($artist['social_instagram']): ?><a href="<?= e($artist['social_instagram']) ?>" class="social-btn"><i class="bi bi-instagram"></i></a><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($artist['bio']): ?>
<section class="container-fluid px-3 px-lg-4 py-4">
    <h2 class="section-title mb-3">Biography</h2>
    <div class="card-media p-4"><p class="text-secondary mb-0"><?= e($artist['bio']) ?></p></div>
</section>
<?php endif; ?>

<section class="container-fluid px-3 px-lg-4 py-4">
    <h2 class="section-title mb-3">Popular Songs</h2>
    <?php if (empty($songs)): ?>
        <div class="empty-state"><i class="bi bi-music-note"></i><p>No songs yet.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4">
            <?php foreach ($songs as $s): render_media_card($s, 'song'); endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php if (!empty($videos)): ?>
<section class="container-fluid px-3 px-lg-4 py-4">
    <h2 class="section-title mb-3">Videos</h2>
    <div class="row g-3 g-lg-4">
        <?php foreach ($videos as $v): render_media_card($v, 'video'); endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($albums)): ?>
<section class="container-fluid px-3 px-lg-4 py-4">
    <h2 class="section-title mb-3">Albums</h2>
    <div class="row g-3 g-lg-4">
        <?php foreach ($albums as $al):
            $alImg = $al['image_url'] ?: $img; ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <a href="<?= url('album_detail.php?id=' . $al['id']) ?>" class="mc-album-card">
                    <div class="mc-album-thumb">
                        <img src="<?= e($alImg) ?>" alt="<?= e($al['title']) ?>" loading="lazy">
                        <div class="mc-album-overlay"></div>
                        <span class="mc-album-badge"><i class="bi bi-disc"></i> Album</span>
                        <span class="mc-album-play"><i class="bi bi-play-fill"></i></span>
                    </div>
                    <div class="mc-album-info">
                        <h3 class="mc-album-title"><?= e($al['title']) ?></h3>
                        <p class="mc-album-artist"><?= e($al['year'] ?? '') ?></p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php render_footer(); ?>
