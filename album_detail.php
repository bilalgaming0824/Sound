<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$id = (int)($_GET['id'] ?? 0);
$album = get_album($id);
if (!$album) { redirect('albums.php'); }
$songs = get_album_songs($id);
render_header($album['title'], 'albums');
media_url($album['image_url'])
?>
<div class="container-fluid px-3 px-lg-4 py-3">
    <a href="<?= url('albums.php') ?>" class="link-underline small text-secondary"><i class="bi bi-arrow-left"></i> Back to Albums</a>
</div>
<section class="position-relative overflow-hidden">
    <div class="position-absolute inset-0" style="z-index:-1">
        <img src="<?= e($img) ?>" alt="" class="w-100 h-100" style="object-fit:cover;opacity:0.2;filter:blur(24px)">
        <div class="position-absolute inset-0" style="background:linear-gradient(to bottom, rgba(7,7,13,0.7), var(--ink-950))"></div>
    </div>
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-3">
                <div class="rounded-4 overflow-hidden border border-ink" style="aspect-ratio:1/1;max-width:280px;margin:0 auto">
                    <img src="<?= e($img) ?>" alt="<?= e($album['title']) ?>" class="w-100 h-100" style="object-fit:cover">
                </div>
            </div>
            <div class="col-lg-9">
                <span class="chip chip-brand mb-2"><i class="bi bi-disc"></i> Album</span>
                <h1 class="hero-title text-white mb-2"><?= e($album['title']) ?></h1>
                <p class="lead text-secondary mb-3"><?= e($album['artist_name'] ?? 'Unknown') ?></p>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($album['year']): ?><span class="chip"><i class="bi bi-calendar3"></i> <?= e($album['year']) ?></span><?php endif; ?>
                    <span class="chip"><i class="bi bi-music-note-beamed"></i> <?= count($songs) ?> songs</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid px-3 px-lg-4 py-4">
    <h2 class="section-title mb-3">Songs in this album</h2>
    <?php if (empty($songs)): ?>
        <div class="empty-state"><i class="bi bi-music-note"></i><p>No songs in this album yet.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4">
            <?php foreach ($songs as $s): render_media_card($s, 'song'); endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php render_footer(); ?>
