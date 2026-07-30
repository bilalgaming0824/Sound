<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$id = (int)($_GET['id'] ?? 0);
$video = get_video($id);
if (!$video) { redirect('videos.php'); }

increment_video_views($id);
if (is_logged_in()) add_history((int)$_SESSION['user_id'], 'video', $id);

$comments = get_comments('video', $id);
$avg = get_avg_rating('video', $id);
$ratingCount = get_rating_count('video', $id);
$userRating = is_logged_in() ? get_user_rating((int)$_SESSION['user_id'], 'video', $id) : 0;
$isFav = is_logged_in() && is_favourite((int)$_SESSION['user_id'], 'video', $id);
$suggested = get_videos(6, 'RAND()');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect("video_detail.php?id=$id"); }
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if ($rating < 1 || $rating > 5) { set_flash('danger', 'Please select a rating.'); redirect("video_detail.php?id=$id"); }
    if ($comment === '') { set_flash('danger', 'Please write a comment.'); redirect("video_detail.php?id=$id"); }
    db()->prepare("INSERT INTO comments (user_id, media_type, media_id, rating, comment) VALUES (?,?,?,?,?)")
        ->execute([$_SESSION['user_id'], 'video', $id, $rating, $comment]);
    db()->prepare("INSERT INTO ratings (user_id, media_type, media_id, score) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE score=VALUES(score)")
        ->execute([$_SESSION['user_id'], 'video', $id, $rating]);
    set_flash('success', 'Review posted!');
    redirect("video_detail.php?id=$id");
}

$flash = get_flash();
render_header($video['title'], 'videos');

$ytId = null;
if ($video['video_url']) {
    if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{11})#', $video['video_url'], $m)) {
        $ytId = $m[1];
    }
}
?>
<style>
.yt-embed { width:100%; height:100%; border:0; }
</style>

<div class="container-fluid px-3 px-lg-4 py-3">
    <a href="<?= url('videos.php') ?>" class="link-underline small text-secondary"><i class="bi bi-arrow-left"></i> Back to Videos</a>
</div>

<section class="container-fluid px-3 px-lg-4 py-3">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ratio ratio-16x9 rounded-4 overflow-hidden border border-ink" style="background:#000">
                <?php if ($ytId): ?>
                    <iframe class="yt-embed" src="https://www.youtube.com/embed/<?= e($ytId) ?>" title="<?= e($video['title']) ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <?php elseif ($video['video_url']): ?>
                    <video controls poster="<?= e(media_url($video['image_url'])) ?>" class="w-100 h-100" src="<?= e(media_url($video['video_url'])) ?>"></video>
                <?php else: ?>
                    <img src="<?= e(media_url($video['image_url'])) ?>" alt="<?= e($video['title']) ?>" class="w-100 h-100" style="object-fit:cover">
                <?php endif; ?>
            </div>
            <div class="mt-3">
                <span class="chip chip-brand mb-2"><i class="bi bi-play-btn"></i> Video</span>
                <?php if (!empty($video['is_new'])): ?><span class="badge-new" style="position:static;display:inline-block">NEW</span><?php endif; ?>
                <h1 class="fw-bold text-white mb-2"><?= e($video['title']) ?></h1>
                <p class="text-secondary mb-2"><?= e($video['artist_name'] ?? 'Unknown Artist') ?></p>
                <?php if ($video['description']): ?><p class="text-secondary mb-3"><?= e($video['description']) ?></p><?php endif; ?>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php if ($video['year']): ?><span class="chip"><i class="bi bi-calendar3"></i> <?= e($video['year']) ?></span><?php endif; ?>
                    <?php if ($video['genre_name']): ?><span class="chip"><i class="bi bi-tag"></i> <?= e($video['genre_name']) ?></span><?php endif; ?>
                    <?php if ($video['language_name']): ?><span class="chip"><i class="bi bi-globe"></i> <?= e($video['language_name']) ?></span><?php endif; ?>
                    <?php if ($video['duration']): ?><span class="chip"><i class="bi bi-clock"></i> <?= format_duration($video['duration']) ?></span><?php endif; ?>
                    <span class="chip"><i class="bi bi-eye"></i> <?= number_format($video['views']) ?> views</span>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?= render_star_rating($avg) ?>
                    <span class="small text-secondary"><?= $avg ? number_format($avg,1) : '—' ?> (<?= $ratingCount ?>)</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (is_logged_in()): ?>
                        <button id="favBtn" class="btn btn-ghost" data-type="video" data-id="<?= $id ?>" data-csrf="<?= csrf_token() ?>">
                            <i class="bi <?= $isFav ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i> <?= $isFav ? 'Favourited' : 'Favourite' ?>
                        </button>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-ghost dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-plus-square"></i> Add to Playlist</button>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <?php $pl = get_playlists((int)$_SESSION['user_id']); ?>
                                <?php if (empty($pl)): ?>
                                    <li><span class="dropdown-item-text text-secondary small">No playlists. <a href="<?= url('playlists.php') ?>">Create one</a></span></li>
                                <?php else: foreach ($pl as $p): ?>
                                    <li><a class="dropdown-item add-to-pl" href="#" data-pl="<?= $p['id'] ?>" data-type="video" data-id="<?= $id ?>" data-csrf="<?= csrf_token() ?>"><?= e($p['name']) ?></a></li>
                                <?php endforeach; endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <a href="<?= url('watch_video.php?id=' . $id) ?>" class="btn btn-ghost"><i class="bi bi-fullscreen"></i> Watch Fullscreen</a>
                    <button class="btn btn-ghost" onclick="copyShareLink()"><i class="bi bi-share"></i> Share</button>
                </div>
            </div>

            <!-- Comments -->
            <div class="mt-4">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-chat-square-text"></i> Reviews (<?= count($comments) ?>)</h3>
                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show"><?= e($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if (is_logged_in()): ?>
                    <form method="post" action="" class="card-media p-3 mb-3">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <label class="form-label small">Your rating</label>
                        <div data-star-input="reviewRating" data-max="5">
                            <?php for ($i=1;$i<=5;$i++): ?>
                            <button type="button" data-value="<?= $i ?>"><i class="bi <?= $i<=($userRating?:5)?'bi-star-fill':'bi-star' ?>"></i></button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="reviewRating" value="<?= $userRating ?: 5 ?>">
                        <textarea name="comment" rows="2" class="form-control mt-3" placeholder="Share your thoughts…" required></textarea>
                        <button class="btn btn-primary mt-2"><i class="bi bi-send"></i> Post Review</button>
                    </form>
                <?php else: ?>
                    <div class="card-media p-3 d-flex align-items-center justify-content-between gap-2 mb-3">
                        <p class="small text-secondary mb-0">Sign in to write a review and rate.</p>
                        <a href="<?= url('login.php') ?>" class="btn btn-primary btn-sm">Sign In</a>
                    </div>
                <?php endif; ?>
                <?php if (empty($comments)): ?>
                    <div class="empty-state"><i class="bi bi-star"></i><p>No reviews yet. Be the first!</p></div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                    <?php foreach ($comments as $c): ?>
                        <div class="card-media p-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="avatar"><?= strtoupper(substr($c['username'],0,1)) ?></span>
                                <div>
                                    <div class="fw-semibold text-white"><?= e($c['username']) ?></div>
                                    <div class="d-flex align-items-center gap-2">
                                        <?= render_star_rating($c['rating']) ?>
                                        <span class="small text-secondary"><?= format_date($c['created_at']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-secondary mb-0"><?= e($c['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Suggested -->
        <div class="col-lg-4">
            <h3 class="fw-bold text-white mb-3">Suggested Videos</h3>
            <div class="d-flex flex-column gap-3">
            <?php foreach ($suggested as $v):
                if ($v['id'] == $id) continue;
                $img = media_url($v['image_url']); ?>
                <a href="<?= url('video_detail.php?id=' . $v['id']) ?>" class="mc-side-card d-flex text-decoration-none">
                    <div class="mc-side-thumb" style="width:140px;min-width:140px"><img src="<?= e($img) ?>" alt="" class="w-100 h-100" style="object-fit:cover;aspect-ratio:16/9"></div>
                    <div class="mc-side-info">
                        <p class="mc-side-title"><?= e($v['title']) ?></p>
                        <p class="mc-side-artist"><?= e($v['artist_name'] ?? '') ?></p>
                        <span class="mc-side-views"><i class="bi bi-eye"></i> <?= number_format($v['views']) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

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
