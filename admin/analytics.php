<?php
$pageTitle = 'Analytics';
$section = 'analytics';
require_once __DIR__ . '/includes/header.php';
$mostPlayed = get_most_played_songs(10);
$topRated = get_top_rated_songs(10);
$mostViewed = get_most_viewed_videos(10);
$activeUsers = get_most_active_users(10);
?>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card-media p-4 mb-3">
            <h3 class="fw-bold text-white mb-1"><i class="bi bi-bar-chart-line text-brand me-2"></i>Most Played Songs</h3>
            <p class="text-secondary small mb-3">Top 10 by play count</p>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>Song</th><th>Artist</th><th>Plays</th></tr></thead>
                    <tbody>
                    <?php foreach ($mostPlayed as $i => $s): ?>
                        <tr>
                            <td class="text-secondary"><?= $i + 1 ?></td>
                            <td class="text-white fw-semibold small"><?= e($s['title']) ?></td>
                            <td class="small text-secondary"><?= e($s['artist_name'] ?? '—') ?></td>
                            <td><span class="chip chip-brand"><?= number_format($s['views']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-media p-4 mb-3">
            <h3 class="fw-bold text-white mb-1"><i class="bi bi-star-fill text-warning me-2"></i>Top Rated Songs</h3>
            <p class="text-secondary small mb-3">Top 10 by average rating</p>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>Song</th><th>Artist</th><th>Rating</th><th>Votes</th></tr></thead>
                    <tbody>
                    <?php foreach ($topRated as $i => $s): ?>
                        <tr>
                            <td class="text-secondary"><?= $i + 1 ?></td>
                            <td class="text-white fw-semibold small"><?= e($s['title']) ?></td>
                            <td class="small text-secondary"><?= e($s['artist_name'] ?? '—') ?></td>
                            <td><span class="text-warning"><i class="bi bi-star-fill"></i> <?= e($s['avg_rating']) ?></span></td>
                            <td class="small text-secondary"><?= $s['rating_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-media p-4 mb-3">
            <h3 class="fw-bold text-white mb-1"><i class="bi bi-eye text-danger me-2"></i>Most Viewed Videos</h3>
            <p class="text-secondary small mb-3">Top 10 by view count</p>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>Video</th><th>Artist</th><th>Views</th></tr></thead>
                    <tbody>
                    <?php foreach ($mostViewed as $i => $v): ?>
                        <tr>
                            <td class="text-secondary"><?= $i + 1 ?></td>
                            <td class="text-white fw-semibold small"><?= e($v['title']) ?></td>
                            <td class="small text-secondary"><?= e($v['artist_name'] ?? '—') ?></td>
                            <td><span class="chip chip-brand"><?= number_format($v['views']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-media p-4 mb-3">
            <h3 class="fw-bold text-white mb-1"><i class="bi bi-people text-primary me-2"></i>Most Active Users</h3>
            <p class="text-secondary small mb-3">Top 10 by comments + ratings</p>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>User</th><th>Comments</th><th>Ratings</th><th>Activity</th></tr></thead>
                    <tbody>
                    <?php foreach ($activeUsers as $i => $u): ?>
                        <tr>
                            <td class="text-secondary"><?= $i + 1 ?></td>
                            <td class="text-white fw-semibold small"><?= e($u['username']) ?></td>
                            <td class="small"><?= $u['comment_count'] ?></td>
                            <td class="small"><?= $u['rating_count'] ?></td>
                            <td><span class="chip chip-brand"><?= $u['activity'] ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
