<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$id = (int)($_GET['id'] ?? 0);
$genre = get_genre($id);
if (!$genre) { redirect('categories.php'); }

$pdo = db();
$stmt = $pdo->prepare("SELECT s.*, a.name AS artist_name FROM songs s LEFT JOIN artists a ON s.artist_id = a.id WHERE s.genre_id = ? ORDER BY s.created_at DESC");
$stmt->execute([$id]);
$songs = $stmt->fetchAll();

$vStmt = $pdo->prepare("SELECT v.*, a.name AS artist_name FROM videos v LEFT JOIN artists a ON v.artist_id = a.id WHERE v.genre_id = ? ORDER BY v.created_at DESC");
$vStmt->execute([$id]);
$videos = $vStmt->fetchAll();

render_header($genre['name'], 'categories');
?>
<div class="container-fluid px-3 px-lg-4 py-3">
    <a href="<?= url('categories.php') ?>" class="link-underline small text-secondary"><i class="bi bi-arrow-left"></i> Back to Categories</a>
</div>
<div class="container-fluid px-3 px-lg-4 py-4">
    <span class="chip chip-brand mb-3"><i class="bi bi-tag"></i> Category</span>
    <h1 class="section-title mb-4"><?= e($genre['name']) ?></h1>

    <?php if ($songs): ?>
    <h2 class="section-title fs-5 mb-3"><i class="bi bi-music-note-beamed text-brand"></i> Songs (<?= count($songs) ?>)</h2>
    <div class="row g-3 g-lg-4 mb-4">
        <?php foreach ($songs as $s): render_media_card($s, 'song'); endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($videos): ?>
    <h2 class="section-title fs-5 mb-3"><i class="bi bi-play-btn text-brand"></i> Videos (<?= count($videos) ?>)</h2>
    <div class="row g-3 g-lg-4">
        <?php foreach ($videos as $v): render_media_card($v, 'video'); endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($songs) && empty($videos)): ?>
        <div class="empty-state"><i class="bi bi-music-note"></i><p>No songs or videos in this category yet.</p></div>
    <?php endif; ?>
</div>
<?php render_footer(); ?>
