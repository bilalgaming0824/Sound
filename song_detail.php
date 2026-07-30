<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$id = (int)($_GET['id'] ?? 0);
$song = get_song($id);
if (!$song) { redirect('music.php'); }

increment_song_views($id);
if (is_logged_in()) add_history((int)$_SESSION['user_id'], 'song', $id);

$related = get_related_songs($id, 5);
$comments = get_comments('song', $id);
$avg = get_avg_rating('song', $id);
$ratingCount = get_rating_count('song', $id);
$userRating = is_logged_in() ? get_user_rating((int)$_SESSION['user_id'], 'song', $id) : 0;
$isFav = is_logged_in() && is_favourite((int)$_SESSION['user_id'], 'song', $id);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect("song_detail.php?id=$id"); }
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if ($rating < 1 || $rating > 5) { set_flash('danger', 'Please select a rating.'); redirect("song_detail.php?id=$id"); }
    if ($comment === '') { set_flash('danger', 'Please write a comment.'); redirect("song_detail.php?id=$id"); }
    $stmt = db()->prepare("INSERT INTO comments (user_id, media_type, media_id, rating, comment) VALUES (?,?,?,?,?)");
    $stmt->execute([$_SESSION['user_id'], 'song', $id, $rating, $comment]);
    // also save a quick rating
    db()->prepare("INSERT INTO ratings (user_id, media_type, media_id, score) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE score=VALUES(score)")
        ->execute([$_SESSION['user_id'], 'song', $id, $rating]);
    set_flash('success', 'Review posted!');
    redirect("song_detail.php?id=$id");
}

$flash = get_flash();
render_header($song['title'], 'music');
?>

<div class="container-fluid px-3 px-lg-4 py-3">
    <a href="<?= url('music.php') ?>" class="link-underline small text-secondary"><i class="bi bi-arrow-left"></i> Back to Music</a>
</div>

<!-- HERO -->
<section class="position-relative overflow-hidden">
    <div class="position-absolute inset-0" style="z-index:-1">
        <img src="<?= e(media_url($song['image_url'])) ?>" alt="" class="w-100 h-100" style="object-fit:cover;opacity:0.2;filter:blur(24px)">
        <div class="position-absolute inset-0" style="background:linear-gradient(to bottom, rgba(7,7,13,0.7), var(--ink-950))"></div>
    </div>
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-4">
                <div class="position-relative rounded-4 overflow-hidden border border-ink" style="aspect-ratio:1/1;max-width:360px;margin:0 auto">
                    <img src="<?= e(media_url($song['image_url'])) ?>" alt="<?= e($song['title']) ?>" class="w-100 h-100" style="object-fit:cover">
                    <?php if ($song['is_new']): ?><span class="badge-new">NEW</span><?php endif; ?>
                </div>
            </div>
            <div class="col-lg-8">
                <span class="chip chip-brand mb-2"><i class="bi bi-music-note-beamed"></i> Song</span>
                <h1 class="hero-title text-white mb-2"><?= e($song['title']) ?></h1>
                <p class="lead text-secondary mb-3"><?= e($song['artist_name'] ?? 'Unknown Artist') ?></p>
                <?php if ($song['description']): ?><p class="text-secondary mb-3" style="max-width:600px"><?= e($song['description']) ?></p><?php endif; ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if ($song['year']): ?><span class="chip"><i class="bi bi-calendar3"></i> <?= e($song['year']) ?></span><?php endif; ?>
                    <?php if ($song['album_title']): ?><span class="chip"><i class="bi bi-disc"></i> <?= e($song['album_title']) ?></span><?php endif; ?>
                    <?php if ($song['genre_name']): ?><span class="chip"><i class="bi bi-tag"></i> <?= e($song['genre_name']) ?></span><?php endif; ?>
                    <?php if ($song['language_name']): ?><span class="chip"><i class="bi bi-globe"></i> <?= e($song['language_name']) ?></span><?php endif; ?>
                    <span class="chip"><i class="bi bi-clock"></i> <?= format_duration($song['duration']) ?></span>
                    <span class="chip"><i class="bi bi-eye"></i> <?= number_format($song['views']) ?> views</span>
                </div>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <?= render_star_rating($avg) ?>
                    <span class="small text-secondary"><?= $avg ? number_format($avg,1) : '—' ?> (<?= $ratingCount ?> rating<?= $ratingCount!=1?'s':'' ?>)</span>
                </div>

                <!-- Audio player -->
                <?php if ($song['audio_url']): ?>
                <div class="card-media p-3 mb-3" style="max-width:520px">
                    <audio controls class="w-100" src="<?= e(media_url($song['audio_url'])) ?>"></audio>
                </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-2">
                    <?php if (is_logged_in()): ?>
                        <button id="favBtn" class="btn btn-ghost" data-type="song" data-id="<?= $id ?>" data-csrf="<?= csrf_token() ?>">
                            <i class="bi <?= $isFav ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i> <?= $isFav ? 'Favourited' : 'Favourite' ?>
                        </button>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-ghost dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-plus-square"></i> Add to Playlist</button>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <?php $pl = get_playlists((int)$_SESSION['user_id']); ?>
                                <?php if (empty($pl)): ?>
                                    <li><span class="dropdown-item-text text-secondary small">No playlists. <a href="<?= url('playlists.php') ?>">Create one</a></span></li>
                                <?php else: foreach ($pl as $p): ?>
                                    <li><a class="dropdown-item add-to-pl" href="#" data-pl="<?= $p['id'] ?>" data-type="song" data-id="<?= $id ?>" data-csrf="<?= csrf_token() ?>"><?= e($p['name']) ?></a></li>
                                <?php endforeach; endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <button class="btn btn-ghost" onclick="copyShareLink()"><i class="bi bi-share"></i> Share</button>
                    <?php if ($song['audio_url']): ?>
                    <a href="<?= e(media_url($song['audio_url'])) ?>" download class="btn btn-ghost"><i class="bi bi-download"></i> Download</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rating + Comments -->
<section class="container-fluid px-3 px-lg-4 py-4">
    <div class="row g-4 review-row">
        <div class="col-lg-4">
            <h2 class="section-title mb-3"><i class="bi bi-chat-square-text"></i> Reviews</h2>
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show"><?= e($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (is_logged_in()): ?>
                <form method="post" action="" class="card-media p-3">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <label class="form-label small">Your rating</label>
                    <div data-star-input="reviewRating" data-max="5">
                        <?php for ($i=1;$i<=5;$i++): ?>
                        <button type="button" data-value="<?= $i ?>"><i class="bi <?= $i<=($userRating?:5)?'bi-star-fill':'bi-star' ?>"></i></button>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="reviewRating" value="<?= $userRating ?: 5 ?>">
                    <label class="form-label small mt-3">Your review</label>
                    <textarea name="comment" rows="3" class="form-control" placeholder="Share your thoughts…" required></textarea>
                    <button class="btn btn-primary w-100 mt-3"><i class="bi bi-send"></i> Post Review</button>
                </form>
            <?php else: ?>
                <div class="card-media p-3 d-flex align-items-center justify-content-between gap-2">
                    <p class="small text-secondary mb-0">Sign in to write a review and rate.</p>
                    <a href="<?= url('login.php') ?>" class="btn btn-primary btn-sm">Sign In</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-8">
            <h3 class="fw-bold text-white mb-3"><?= count($comments) ?> Review<?= count($comments)!=1?'s':'' ?></h3>
            <?php if (empty($comments)): ?>
                <div class="empty-state"><i class="bi bi-star"></i><p>No reviews yet. Be the first to review!</p></div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                <?php foreach ($comments as $c): ?>
                    <div class="card-media p-3">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar"><?= strtoupper(substr($c['username'],0,1)) ?></span>
                                <div>
                                    <div class="fw-semibold text-white"><?= e($c['username']) ?></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?= render_star_rating($c['rating']) ?>
                                        <span class="small text-secondary"><?= format_date($c['created_at']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-secondary mt-2 mb-0"><?= e($c['comment']) ?></p>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Related -->
<?php if (!empty($related)): ?>
<section class="container-fluid px-3 px-lg-4 py-4">
    <h2 class="section-title mb-3">Related Songs</h2>
    <div class="row g-3 g-lg-4">
        <?php foreach ($related as $r): render_media_card($r, 'song'); endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>
function copyShareLink(){navigator.clipboard.writeText(window.location.href).then(function(){alert('Link copied!')});}
var favBtn=document.getElementById('favBtn');
if(favBtn){favBtn.addEventListener('click',function(){
    var fd=new FormData();fd.append('type',favBtn.dataset.type);fd.append('id',favBtn.dataset.id);fd.append('csrf',favBtn.dataset.csrf);
    fetch(BASE_URL+'/api/favourite.php',{method:'POST',body:fd}).then(function(r){return r.json()}).then(function(d){
        if(d.success){var i=favBtn.querySelector('i');if(d.favourite){i.className='bi bi-heart-fill text-danger';favBtn.lastChild.textContent=' Favourited';}
        else{i.className='bi bi-heart';favBtn.lastChild.textContent=' Favourite';}}});
});}
</script>

<?php render_footer(); ?>
