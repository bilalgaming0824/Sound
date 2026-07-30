<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

render_header('Sitemap', 'home');
?>

<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="text-center mb-5">
        <h1 class="section-title mb-2"><i class="bi bi-diagram-3"></i> Sitemap</h1>
        <p class="text-secondary">Explore all pages of SOUND Entertainment</p>
    </div>

    <div class="row g-4">
        <!-- Main Pages -->
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-house-door text-primary"></i> Main Pages</h3>
                <ul class="sitemap-list">
                    <li><a href="<?= url('index.php') ?>"><i class="bi bi-chevron-right"></i> Home</a></li>
                    <li><a href="<?= url('music.php') ?>"><i class="bi bi-chevron-right"></i> Music Library</a></li>
                    <li><a href="<?= url('videos.php') ?>"><i class="bi bi-chevron-right"></i> Videos</a></li>
                    <li><a href="<?= url('albums.php') ?>"><i class="bi bi-chevron-right"></i> Albums</a></li>
                    <li><a href="<?= url('artists.php') ?>"><i class="bi bi-chevron-right"></i> Artists</a></li>
                    <li><a href="<?= url('categories.php') ?>"><i class="bi bi-chevron-right"></i> Categories</a></li>
                    <li><a href="<?= url('search.php') ?>"><i class="bi bi-chevron-right"></i> Search</a></li>
                </ul>
            </div>
        </div>

        <!-- Account -->
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-person-circle text-success"></i> Account</h3>
                <ul class="sitemap-list">
                    <li><a href="<?= url('login.php') ?>"><i class="bi bi-chevron-right"></i> Sign In</a></li>
                    <li><a href="<?= url('register.php') ?>"><i class="bi bi-chevron-right"></i> Create Account</a></li>
                    <li><a href="<?= url('dashboard.php') ?>"><i class="bi bi-chevron-right"></i> Dashboard</a></li>
                    <li><a href="<?= url('profile.php') ?>"><i class="bi bi-chevron-right"></i> My Profile</a></li>
                    <li><a href="<?= url('playlists.php') ?>"><i class="bi bi-chevron-right"></i> Playlists</a></li>
                    <li><a href="<?= url('logout.php') ?>"><i class="bi bi-chevron-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Info Pages -->
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-info-circle text-warning"></i> Information</h3>
                <ul class="sitemap-list">
                    <li><a href="<?= url('about.php') ?>"><i class="bi bi-chevron-right"></i> About Us</a></li>
                    <li><a href="<?= url('contact.php') ?>"><i class="bi bi-chevron-right"></i> Contact</a></li>
                    <li><a href="<?= url('faq.php') ?>"><i class="bi bi-chevron-right"></i> FAQ</a></li>
                    <li><a href="<?= url('terms.php') ?>"><i class="bi bi-chevron-right"></i> Terms &amp; Privacy</a></li>
                    <li><a href="<?= url('sitemap.php') ?>"><i class="bi bi-chevron-right"></i> Sitemap</a></li>
                </ul>
            </div>
        </div>

        <!-- Browse by Genre -->
        <?php $genres = get_genres(); ?>
        <?php if (!empty($genres)): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-tags text-info"></i> Browse by Genre</h3>
                <ul class="sitemap-list">
                    <?php foreach ($genres as $g): ?>
                    <li><a href="<?= url('music.php?genre=' . $g['id']) ?>"><i class="bi bi-chevron-right"></i> <?= e($g['name']) ?> (<?= $g['song_count'] ?>)</a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Browse by Language -->
        <?php $languages = get_languages(); ?>
        <?php if (!empty($languages)): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-globe2 text-danger"></i> Browse by Language</h3>
                <ul class="sitemap-list">
                    <?php foreach ($languages as $l): ?>
                    <li><a href="<?= url('music.php?language=' . $l['id']) ?>"><i class="bi bi-chevron-right"></i> <?= e($l['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Featured Artists -->
        <?php $artists = get_artists(8); ?>
        <?php if (!empty($artists)): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-mic text-primary"></i> Featured Artists</h3>
                <ul class="sitemap-list">
                    <?php foreach ($artists as $a): ?>
                    <li><a href="<?= url('artist_detail.php?id=' . $a['id']) ?>"><i class="bi bi-chevron-right"></i> <?= e($a['name']) ?> (<?= $a['song_count'] ?> songs)</a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Featured Albums -->
        <?php $albums = get_albums(8); ?>
        <?php if (!empty($albums)): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-disc text-success"></i> Featured Albums</h3>
                <ul class="sitemap-list">
                    <?php foreach ($albums as $al): ?>
                    <li><a href="<?= url('album_detail.php?id=' . $al['id']) ?>"><i class="bi bi-chevron-right"></i> <?= e($al['title']) ?> — <?= e($al['artist_name'] ?? 'Unknown') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Latest Songs -->
        <?php $latestSongs = get_latest_songs(8); ?>
        <?php if (!empty($latestSongs)): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-music-note-beamed text-warning"></i> Latest Songs</h3>
                <ul class="sitemap-list">
                    <?php foreach ($latestSongs as $s): ?>
                    <li><a href="<?= url('song_detail.php?id=' . $s['id']) ?>"><i class="bi bi-chevron-right"></i> <?= e($s['title']) ?> — <?= e($s['artist_name'] ?? 'Unknown') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Latest Videos -->
        <?php $latestVideos = get_latest_videos(8); ?>
        <?php if (!empty($latestVideos)): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-media p-4 h-100">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-play-btn text-danger"></i> Latest Videos</h3>
                <ul class="sitemap-list">
                    <?php foreach ($latestVideos as $v): ?>
                    <li><a href="<?= url('video_detail.php?id=' . $v['id']) ?>"><i class="bi bi-chevron-right"></i> <?= e($v['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.sitemap-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.sitemap-list li {
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.sitemap-list li:last-child {
    border-bottom: none;
}
.sitemap-list a {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 4px;
    color: #b4bcd0;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.15s ease;
    border-radius: 6px;
}
.sitemap-list a:hover {
    color: #fff;
    background: rgba(255,255,255,0.04);
    padding-left: 10px;
}
.sitemap-list a i {
    font-size: 0.7rem;
    color: #6c7383;
    transition: color 0.15s ease;
}
.sitemap-list a:hover i {
    color: var(--accent, #6c63ff);
}
</style>

<?php render_footer(); ?>
