<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$artists = get_artists();
$genres = get_genres();
$languages = get_languages();
$albums = get_albums();

$filters = [
    'q' => trim($_GET['q'] ?? ''),
    'artist' => $_GET['artist'] ?? '',
    'genre' => $_GET['genre'] ?? '',
    'language' => $_GET['language'] ?? '',
    'album' => $_GET['album'] ?? '',
    'year' => $_GET['year'] ?? '',
    'sort' => $_GET['sort'] ?? 'newest',
];
$songs = filter_songs($filters);
$years = array_unique(array_filter(array_column($songs, 'year')));
rsort($years);
[$songs, $pagination] = paginate($songs, 12);

render_header('Music', 'music');
?>

<div class="container-fluid px-3 px-lg-4 py-4">
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="section-title">Music Library</h1>
            <p class="text-secondary small mb-0"><?= count($songs) ?> tracks</p>
        </div>
        <form class="d-flex gap-2" method="get" action="">
            <div class="input-group" style="min-width:240px">
                <span class="input-group-text bg-transparent border-ink text-secondary"><i class="bi bi-search"></i></span>
                <input type="search" name="q" value="<?= e($filters['q']) ?>" class="form-control" placeholder="Search music…">
            </div>
            <button type="button" class="btn btn-ghost" data-bs-toggle="collapse" data-bs-target="#filterPanel"><i class="bi bi-funnel"></i> Filters</button>
        </form>
    </div>

    <div class="collapse" id="filterPanel">
        <form method="get" action="" class="card-media p-3 mb-4">
            <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label small">Artist</label>
                    <select name="artist" class="form-select form-select-sm"><option value="">All artists</option>
                    <?php foreach ($artists as $a): ?><option value="<?= $a['id'] ?>" <?= $filters['artist']==$a['id']?'selected':'' ?>><?= e($a['name']) ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-md-3"><label class="form-label small">Genre</label>
                    <select name="genre" class="form-select form-select-sm"><option value="">All genres</option>
                    <?php foreach ($genres as $g): ?><option value="<?= $g['id'] ?>" <?= $filters['genre']==$g['id']?'selected':'' ?>><?= e($g['name']) ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-md-3"><label class="form-label small">Language</label>
                    <select name="language" class="form-select form-select-sm"><option value="">All languages</option>
                    <?php foreach ($languages as $l): ?><option value="<?= $l['id'] ?>" <?= $filters['language']==$l['id']?'selected':'' ?>><?= e($l['name']) ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-md-3"><label class="form-label small">Album</label>
                    <select name="album" class="form-select form-select-sm"><option value="">All albums</option>
                    <?php foreach ($albums as $al): ?><option value="<?= $al['id'] ?>" <?= $filters['album']==$al['id']?'selected':'' ?>><?= e($al['title']) ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-md-3"><label class="form-label small">Year</label>
                    <select name="year" class="form-select form-select-sm"><option value="">All years</option>
                    <?php foreach ($years as $y): ?><option value="<?= $y ?>" <?= $filters['year']==$y?'selected':'' ?>><?= $y ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-md-3"><label class="form-label small">Sort by</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="newest" <?= $filters['sort']=='newest'?'selected':'' ?>>Newest</option>
                        <option value="title" <?= $filters['sort']=='title'?'selected':'' ?>>Title (A-Z)</option>
                        <option value="year" <?= $filters['sort']=='year'?'selected':'' ?>>Year (newest)</option>
                        <option value="views" <?= $filters['sort']=='views'?'selected':'' ?>>Most viewed</option>
                    </select></div>
                <div class="col-md-3 d-flex align-items-end"><button class="btn btn-outline-light w-100">Apply</button></div>
                <div class="col-md-3 d-flex align-items-end"><a href="<?= url('music.php') ?>" class="btn btn-ghost w-100">Reset</a></div>
            </div>
        </form>
    </div>

    <?php if (empty($songs)): ?>
        <div class="empty-state"><i class="bi bi-music-note-beamed"></i><p>No music matches your filters.</p></div>
    <?php else: ?>
        <div class="row g-3 g-lg-4">
            <?php foreach ($songs as $s): render_media_card($s, 'song'); endforeach; ?>
        </div>
        <?= $pagination ?>
    <?php endif; ?>
</div>

<?php render_footer(); ?>
