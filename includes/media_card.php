<?php
/**
 * Render a media card matching the home page premium card design.
 * $item must have: id, title, image_url, artist_name, year, duration, is_new, views
 * $type = 'song' | 'video'
 */
function render_media_card(array $item, string $type): void {
    $img = media_url($item['image_url']);
    $link = $type === 'video' ? 'video_detail.php?id=' . $item['id'] : 'song_detail.php?id=' . $item['id'];
    $icon = $type === 'video' ? 'bi-play-btn' : 'bi-music-note-beamed';
    $label = $type === 'video' ? 'Video' : 'Song';
    $dur = isset($item['duration']) ? format_duration($item['duration']) : '';
    $rawViews = intval($item['views'] ?? 0);
    $views = $rawViews >= 1000 ? round($rawViews / 1000, 1) . 'K' : $rawViews;
    $year = $item['year'] ?? '';
    ?>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <a href="<?= url($link) ?>" class="mc-card text-decoration-none d-block">
            <div class="mc-thumb">
                <img src="<?= e($img) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                <div class="mc-thumb-overlay"></div>
                <?php if (!empty($item['is_new'])): ?><span class="mc-badge-new">NEW</span><?php endif; ?>
                <span class="mc-badge-type"><i class="bi <?= $icon ?>"></i> <?= $label ?></span>
                <span class="mc-play"><i class="bi bi-play-fill"></i></span>
                <?php if ($dur): ?><span class="mc-duration"><?= e($dur) ?></span><?php endif; ?>
            </div>
            <div class="mc-info">
                <p class="mc-title"><?= e($item['title']) ?></p>
                <p class="mc-artist"><?= e($item['artist_name'] ?? 'Unknown') ?></p>
                <div class="mc-meta">
                    <?php if ($year): ?><span class="mc-meta-item"><i class="bi bi-calendar3"></i> <?= e($year) ?></span><?php endif; ?>
                    <span class="mc-meta-item"><i class="bi bi-eye"></i> <?= $views ?></span>
                </div>
            </div>
        </a>
    </div>
    <?php
}

function render_star_rating(float $value): string {
    $html = '<span class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        $icon = $i <= round($value) ? 'bi-star-fill' : 'bi-star';
        $html .= '<i class="bi ' . $icon . '"></i>';
    }
    $html .= '</span>';
    return $html;
}
