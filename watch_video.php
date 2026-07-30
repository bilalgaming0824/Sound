<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

$id = (int)($_GET['id'] ?? 0);
$video = get_video($id);
if (!$video) { redirect('videos.php'); }

increment_video_views($id);
if (is_logged_in()) add_history((int)$_SESSION['user_id'], 'video', $id);

$related = get_videos(8, 'RAND()');
render_header('Watch: ' . $video['title'], 'videos');
?>

<div class="container-fluid px-3 px-lg-4 py-3">
    <a href="<?= url('video_detail.php?id=' . $id) ?>" class="link-underline small text-secondary"><i class="bi bi-arrow-left"></i> Back to Video Details</a>
</div>

<div class="container-fluid px-3 px-lg-4 py-3">
    <div class="row g-4">
        <div class="col-lg-9">
            <div class="ratio ratio-16x9 rounded-4 overflow-hidden border border-ink" style="background:#000">
                <?php if ($video['video_url']): ?>
                    <video controls autoplay poster="<?= e(media_url($video['image_url'])) ?>" class="w-100 h-100" src="<?= e(media_url($video['video_url'])) ?>"></video>
                <?php else: ?>
                    <img src="<?= e($video['image_url']) ?>" alt="<?= e($video['title']) ?>" class="w-100 h-100" style="object-fit:contain">
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <span class="chip chip-brand mb-2"><i class="bi bi-play-btn"></i> Now Playing</span>
                <h1 class="fw-bold text-white mb-2"><?= e($video['title']) ?></h1>
                <p class="text-secondary mb-3"><?= e($video['artist_name'] ?? 'Unknown Artist') ?></p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if ($video['year']): ?><span class="chip"><i class="bi bi-calendar3"></i> <?= e($video['year']) ?></span><?php endif; ?>
                    <?php if ($video['genre_name']): ?><span class="chip"><i class="bi bi-tag"></i> <?= e($video['genre_name']) ?></span><?php endif; ?>
                    <span class="chip"><i class="bi bi-eye"></i> <?= number_format($video['views']) ?> views</span>
                </div>
                <?php if ($video['description']): ?>
                    <div class="card-media p-3"><p class="text-secondary mb-0"><?= e($video['description']) ?></p></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-3">
            <h3 class="fw-bold text-white mb-3">Up Next</h3>
            <div class="d-flex flex-column gap-3">
            <?php foreach ($related as $v):
                if ($v['id'] == $id) continue;
                $img = media_url($v['image_url']); ?>
                <a href="<?= url('watch_video.php?id=' . $v['id']) ?>" class="mc-side-card d-flex text-decoration-none">
                    <div class="mc-side-thumb" style="width:120px;min-width:120px"><img src="<?= e($img) ?>" alt="" class="w-100 h-100" style="object-fit:cover;aspect-ratio:16/9"></div>
                    <div class="mc-side-info">
                        <p class="mc-side-title"><?= e($v['title']) ?></p>
                        <p class="mc-side-artist"><?= e($v['artist_name'] ?? '') ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php render_footer(); ?>
