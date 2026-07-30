<?php
require_once __DIR__ . '/includes/functions.php';
render_header('About', 'about', 'Learn about SOUND — your home for music and video entertainment.');
?>
<div class="container-fluid px-3 px-lg-4 py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="section-title mb-4">About SOUND</h1>
            <div class="card-media p-4 p-lg-5">
                <p class="lead text-secondary">SOUND is a premium music and video streaming platform built for music lovers. Stream thousands of songs and videos across English and regional languages, discover new artists, build playlists, and share what you love.</p>
                <hr class="border-secondary my-4">
                <h3 class="fw-bold text-white mb-3">Our Mission</h3>
                <p class="text-secondary">To provide a seamless, enjoyable entertainment experience where users can discover, stream, and organize their favorite music and videos in one place — free, forever.</p>
                <h3 class="fw-bold text-white mb-3 mt-4">What We Offer</h3>
                <div class="row g-3">
                    <div class="col-md-6"><div class="d-flex align-items-start gap-3"><span class="stat-icon" style="background:rgba(108,99,255,0.15)"><i class="bi bi-music-note-beamed text-brand"></i></span><div><h5 class="text-white mb-1">Music Library</h5><p class="text-secondary small mb-0">Thousands of songs across genres and languages.</p></div></div></div>
                    <div class="col-md-6"><div class="d-flex align-items-start gap-3"><span class="stat-icon" style="background:rgba(255,77,109,0.15)"><i class="bi bi-play-btn text-danger"></i></span><div><h5 class="text-white mb-1">Video Streaming</h5><p class="text-secondary small mb-0">Watch music videos in high quality.</p></div></div></div>
                    <div class="col-md-6"><div class="d-flex align-items-start gap-3"><span class="stat-icon" style="background:rgba(34,197,94,0.15)"><i class="bi bi-collection-play text-success"></i></span><div><h5 class="text-white mb-1">Playlists</h5><p class="text-secondary small mb-0">Create and manage your own playlists.</p></div></div></div>
                    <div class="col-md-6"><div class="d-flex align-items-start gap-3"><span class="stat-icon" style="background:rgba(245,158,11,0.15)"><i class="bi bi-star text-warning"></i></span><div><h5 class="text-white mb-1">Ratings & Reviews</h5><p class="text-secondary small mb-0">Rate songs and share your opinions.</p></div></div></div>
                </div>
                <h3 class="fw-bold text-white mb-3 mt-4">Technologies</h3>
                <p class="text-secondary">Built with PHP, MySQL, Bootstrap 5, and vanilla JavaScript. Designed with a modern dark theme and smooth animations.</p>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
