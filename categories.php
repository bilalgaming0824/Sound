<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

$genres = get_genres();
render_header('Categories', 'categories');

$catImgs = [
    url('public/images/cat-1.webp'),
    url('public/images/cat-2.webp'),
    url('public/images/cat-3.webp'),
    url('public/images/cat-4.webp'),
    url('public/images/cat-5.webp'),
    url('public/images/cat-6.webp'),
];
$catIcons = ['bi-music-note-beamed', 'bi-globe-asia-australia', 'bi-people-fill', 'bi-fire', 'bi-heart-fill', 'bi-disc'];
$catGrads = [
    'linear-gradient(135deg,#6c63ff,#a855f7)',
    'linear-gradient(135deg,#ec4899,#f43f5e)',
    'linear-gradient(135deg,#0d9488,#06b6d4)',
    'linear-gradient(135deg,#f97316,#ef4444)',
    'linear-gradient(135deg,#2563eb,#818cf8)',
    'linear-gradient(135deg,#059669,#10b981)',
];
?>
<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <h1 class="section-title">Browse Categories</h1>
            <p class="text-secondary small mb-0"><?= count($genres) ?> genres</p>
        </div>
    </div>
    <div class="row g-3 g-lg-4">
        <?php $ci = 0; foreach ($genres as $g):
            $gi = $ci % 6;
            $img = $catImgs[$gi];
            $icon = $catIcons[$gi];
            $grad = $catGrads[$gi];
            $songCount = (int)($g['song_count'] ?? 0);
        ?>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <a href="<?= url('category.php?id=' . $g['id']) ?>" class="mc-cat-card">
                    <img src="<?= e($img) ?>" alt="<?= e($g['name']) ?>" loading="lazy">
                    <div class="mc-cat-overlay"></div>
                    <div class="mc-cat-icon">
                        <i class="bi <?= $icon ?>" style="background:<?= $grad ?>;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent"></i>
                    </div>
                    <div class="mc-cat-info">
                        <h3 class="mc-cat-name"><?= e($g['name']) ?></h3>
                        <p class="mc-cat-count"><?= $songCount ?> songs</p>
                    </div>
                    <span class="mc-cat-go"><i class="bi bi-arrow-right"></i></span>
                </a>
            </div>
        <?php $ci++; endforeach; ?>
    </div>
</div>
<?php render_footer(); ?>
