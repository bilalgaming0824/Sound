<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

$albums = get_albums();
[$albums, $pagination] = paginate($albums, 12);
render_header('Albums', 'albums');
?>
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <h1 class="section-title">Albums</h1>
            <p class="text-secondary small mb-0"><?= count($albums) ?> albums</p>
        </div>
    </div>
    <div class="row g-3 g-lg-4">
        <?php foreach ($albums as $al):
            $img = media_url($al['image_url']);
            $songCount = (int)($al['song_count'] ?? 0);
            $year = !empty($al['year']) ? e($al['year']) : '';
        ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <a href="<?= url('album_detail.php?id=' . $al['id']) ?>" class="mc-album-card">
                    <div class="mc-album-thumb">
                        <img src="<?= e($img) ?>" alt="<?= e($al['title']) ?>" loading="lazy">
                        <div class="mc-album-overlay"></div>
                        <span class="mc-album-badge"><i class="bi bi-disc"></i> Album</span>
                        <span class="mc-album-count"><?= $songCount ?> tracks</span>
                        <span class="mc-album-play"><i class="bi bi-play-fill"></i></span>
                    </div>
                    <div class="mc-album-info">
                        <h3 class="mc-album-title"><?= e($al['title']) ?></h3>
                        <p class="mc-album-artist"><?= e($al['artist_name'] ?? 'Unknown Artist') ?></p>
                        <div class="mc-album-meta">
                            <span class="mc-album-meta-item"><i class="bi bi-music-note-beamed"></i> <?= $songCount ?> songs</span>
                            <?php if ($year): ?><span class="mc-album-meta-item"><i class="bi bi-calendar3"></i> <?= $year ?></span><?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?= $pagination ?>
</div>
<?php render_footer(); ?>
