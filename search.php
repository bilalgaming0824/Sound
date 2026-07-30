<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$q = trim($_GET['q'] ?? '');
$type = trim($_GET['type'] ?? '');
$genreId = (int)($_GET['genre'] ?? 0);
$langId = (int)($_GET['language'] ?? 0);
$year = (int)($_GET['year'] ?? 0);

$genres = get_genres();
$languages = get_languages();
$years = [];
$pdo = db();
$yearRows = $pdo->query("SELECT DISTINCT year FROM songs WHERE year IS NOT NULL UNION SELECT DISTINCT year FROM videos WHERE year IS NOT NULL ORDER BY year DESC")->fetchAll();
foreach ($yearRows as $r) $years[] = (int)$r['year'];
$years = array_unique($years);
rsort($years);

$songs = []; $videos = []; $artists = []; $albums = [];

if ($q || $type || $genreId || $langId || $year) {
    if (!$type || $type === 'song') {
        $sql = "SELECT s.*, a.name AS artist_name, g.name AS genre_name, l.name AS language_name FROM songs s LEFT JOIN artists a ON s.artist_id=a.id LEFT JOIN genres g ON s.genre_id=g.id LEFT JOIN languages l ON s.language_id=l.id WHERE 1=1";
        $params = [];
        if ($q) { $sql .= " AND (s.title LIKE ? OR a.name LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
        if ($genreId) { $sql .= " AND s.genre_id=?"; $params[] = $genreId; }
        if ($langId) { $sql .= " AND s.language_id=?"; $params[] = $langId; }
        if ($year) { $sql .= " AND s.year=?"; $params[] = $year; }
        $sql .= " ORDER BY s.views DESC LIMIT 50";
        $stmt = $pdo->prepare($sql); $stmt->execute($params); $songs = $stmt->fetchAll();
    }
    if (!$type || $type === 'video') {
        $sql = "SELECT v.*, a.name AS artist_name, g.name AS genre_name, l.name AS language_name FROM videos v LEFT JOIN artists a ON v.artist_id=a.id LEFT JOIN genres g ON v.genre_id=g.id LEFT JOIN languages l ON v.language_id=l.id WHERE 1=1";
        $params = [];
        if ($q) { $sql .= " AND (v.title LIKE ? OR a.name LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
        if ($genreId) { $sql .= " AND v.genre_id=?"; $params[] = $genreId; }
        if ($langId) { $sql .= " AND v.language_id=?"; $params[] = $langId; }
        if ($year) { $sql .= " AND v.year=?"; $params[] = $year; }
        $sql .= " ORDER BY v.views DESC LIMIT 50";
        $stmt = $pdo->prepare($sql); $stmt->execute($params); $videos = $stmt->fetchAll();
    }
    if (!$type || $type === 'artist') {
        $sql = "SELECT * FROM artists WHERE 1=1";
        $params = [];
        if ($q) { $sql .= " AND name LIKE ?"; $params[] = "%$q%"; }
        $sql .= " ORDER BY name ASC LIMIT 20";
        $stmt = $pdo->prepare($sql); $stmt->execute($params); $artists = $stmt->fetchAll();
    }
    if (!$type || $type === 'album') {
        $sql = "SELECT al.*, a.name AS artist_name FROM albums al LEFT JOIN artists a ON al.artist_id=a.id WHERE 1=1";
        $params = [];
        if ($q) { $sql .= " AND al.title LIKE ?"; $params[] = "%$q%"; }
        if ($year) { $sql .= " AND al.year=?"; $params[] = $year; }
        $sql .= " ORDER BY al.title ASC LIMIT 20";
        $stmt = $pdo->prepare($sql); $stmt->execute($params); $albums = $stmt->fetchAll();
    }
}

render_header('Search' . ($q ? ' — ' . $q : ''), 'search', 'Search songs, videos, albums, artists by language, year, genre.');
?>
<div class="container-fluid px-3 px-lg-4 py-4">
    <ul class="breadcrumb-custom">
        <li><a href="<?= url('index.php') ?>"><i class="bi bi-house"></i> Home</a></li>
        <li class="sep"><i class="bi bi-chevron-right"></i></li>
        <li class="active">Search</li>
    </ul>
    <h1 class="section-title mb-4">Advanced Search</h1>

    <form method="get" action="" class="card-media p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Keyword</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-ink text-secondary"><i class="bi bi-search"></i></span>
                    <input type="search" name="q" value="<?= e($q) ?>" class="form-control" placeholder="Song, artist, album…">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Type</label>
                <select name="type" class="form-select">
                    <option value="">All</option>
                    <option value="song" <?= $type==='song'?'selected':'' ?>>Songs</option>
                    <option value="video" <?= $type==='video'?'selected':'' ?>>Videos</option>
                    <option value="artist" <?= $type==='artist'?'selected':'' ?>>Artists</option>
                    <option value="album" <?= $type==='album'?'selected':'' ?>>Albums</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Genre</label>
                <select name="genre" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($genres as $g): ?><option value="<?= $g['id'] ?>" <?= $genreId==$g['id']?'selected':'' ?>><?= e($g['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Language</label>
                <select name="language" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($languages as $l): ?><option value="<?= $l['id'] ?>" <?= $langId==$l['id']?'selected':'' ?>><?= e($l['name']) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Year</label>
                <select name="year" class="form-select">
                    <option value="">All</option>
                    <?php foreach ($years as $y): ?><option value="<?= $y ?>" <?= $year==$y?'selected':'' ?>><?= $y ?></option><?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
            <a href="<?= url('search.php') ?>" class="btn btn-ghost">Clear</a>
        </div>
    </form>

    <?php if (!$q && !$type && !$genreId && !$langId && !$year): ?>
        <div class="empty-state"><i class="bi bi-search"></i><p>Use the filters above to search across music, videos, artists and albums.</p></div>
    <?php elseif (empty($songs) && empty($videos) && empty($artists) && empty($albums)): ?>
        <div class="empty-state"><i class="bi bi-emoji-frown"></i><p>No results found. Try different filters.</p></div>
    <?php else: ?>
        <?php if ($songs): ?>
            <h2 class="section-title fs-5 mb-3"><i class="bi bi-music-note-beamed text-brand"></i> Songs (<?= count($songs) ?>)</h2>
            <div class="row g-3 g-lg-4 mb-4"><?php foreach ($songs as $s): render_media_card($s, 'song'); endforeach; ?></div>
        <?php endif; ?>
        <?php if ($videos): ?>
            <h2 class="section-title fs-5 mb-3"><i class="bi bi-play-btn text-brand"></i> Videos (<?= count($videos) ?>)</h2>
            <div class="row g-3 g-lg-4 mb-4"><?php foreach ($videos as $v): render_media_card($v, 'video'); endforeach; ?></div>
        <?php endif; ?>
        <?php if ($artists): ?>
            <h2 class="section-title fs-5 mb-3"><i class="bi bi-mic text-brand"></i> Artists (<?= count($artists) ?>)</h2>
            <div class="row g-3 g-lg-4 mb-4">
                <?php foreach ($artists as $a):
                    media_url($a['image_url']) ?>
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="<?= url('artist_detail.php?id=' . $a['id']) ?>" class="mc-artist-card">
                            <div class="mc-artist-avatar-wrap">
                                <img src="<?= e($aImg) ?>" alt="<?= e($a['name']) ?>" class="mc-artist-avatar" loading="lazy">
                                <span class="mc-artist-play"><i class="bi bi-play-fill"></i></span>
                            </div>
                            <h3 class="mc-artist-name"><?= e($a['name']) ?></h3>
                            <p class="mc-artist-role">Artist</p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if ($albums): ?>
            <h2 class="section-title fs-5 mb-3"><i class="bi bi-disc text-brand"></i> Albums (<?= count($albums) ?>)</h2>
            <div class="row g-3 g-lg-4">
                <?php foreach ($albums as $al):
                    media_url($al['image_url']) ?>
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
                                <p class="mc-album-artist"><?= e($al['artist_name'] ?? '') ?></p>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php render_footer(); ?>
