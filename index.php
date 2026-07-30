<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/models.php';
require_once __DIR__ . '/includes/media_card.php';

$latestSongs = get_latest_songs(12);
$latestVideos = get_latest_videos(12);
$trendingSongs = get_trending_songs(12);
$albums = get_albums(12);
$artists = get_artists(12);
$genres = get_genres();
$featured = $latestSongs[0] ?? null;

render_header('Home', 'home');
?>

<!-- HERO -->
<section class="hero-section">
    <!-- Full-bleed rotating background carousel -->
    <div class="hero-bg-carousel" id="heroBgCarousel">
        <div class="hero-bg-slide active" style="background-image:url('https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=1600')"></div>
        <div class="hero-bg-slide" style="background-image:url('https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=1600')"></div>
        <div class="hero-bg-slide" style="background-image:url('https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=1600')"></div>
        <div class="hero-bg-slide" style="background-image:url('https://images.pexels.com/photos/248963/pexels-photo-248963.jpeg?auto=compress&cs=tinysrgb&w=1600')"></div>
        <div class="hero-bg-slide" style="background-image:url('https://images.pexels.com/photos/7715782/pexels-photo-7715782.jpeg?auto=compress&cs=tinysrgb&w=1600')"></div>
    </div>
    <div class="hero-bg-overlay"></div>

    <div class="container-fluid px-3 px-lg-5 position-relative">
        <div class="row align-items-center" style="min-height:520px">

            <!-- LEFT: pill + headline + subtitle + buttons + dots -->
            <div class="col-lg-6 py-5 animate-fade-up">
                <span class="chip chip-outline mb-4">YOUR MUSIC, YOUR WORLD</span>

                <h1 class="hero-title text-white lh-1 mb-0">Discover.</h1>
                <h1 class="hero-title text-white lh-1 mb-0">Stream.</h1>
                <h1 class="hero-title hero-grad-text lh-1 mb-4">Feel.</h1>

                <p class="hero-subtitle">Millions of songs. Thousands of artists.<br>Endless emotions. All in one place.</p>

                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="<?= url('music.php') ?>" class="btn btn-primary btn-hero"><i class="bi bi-music-note-beamed me-2"></i>Explore Music</a>
                    <a href="<?= url('videos.php') ?>" class="btn btn-hero-outline"><i class="bi bi-play-circle-fill me-2"></i>Watch Videos</a>
                </div>

                <!-- Slide dots -->
                <div class="hero-dots mt-4" id="heroDots">
                    <span class="hero-dot active" data-slide="0"></span>
                    <span class="hero-dot" data-slide="1"></span>
                    <span class="hero-dot" data-slide="2"></span>
                    <span class="hero-dot" data-slide="3"></span>
                    <span class="hero-dot" data-slide="4"></span>
                </div>
            </div>

            <!-- RIGHT: compact stats card -->
            <div class="col-lg-3 ms-lg-auto py-5 animate-fade-up d-none d-lg-block">
                <div class="hero-stats-card">
                    <div class="hero-stat-row">
                        <span class="hero-stat-icon"><i class="bi bi-music-note-beamed"></i></span>
                        <div>
                            <div class="hero-stat-num">10K+</div>
                            <div class="hero-stat-lbl">Songs</div>
                        </div>
                    </div>
                    <div class="hero-stat-row">
                        <span class="hero-stat-icon"><i class="bi bi-people-fill"></i></span>
                        <div>
                            <div class="hero-stat-num">5K+</div>
                            <div class="hero-stat-lbl">Artists</div>
                        </div>
                    </div>
                    <div class="hero-stat-row">
                        <span class="hero-stat-icon"><i class="bi bi-disc"></i></span>
                        <div>
                            <div class="hero-stat-num">2K+</div>
                            <div class="hero-stat-lbl">Albums</div>
                        </div>
                    </div>
                    <div class="hero-stat-row last">
                        <span class="hero-stat-icon"><i class="bi bi-play-btn-fill"></i></span>
                        <div>
                            <div class="hero-stat-num">1K+</div>
                            <div class="hero-stat-lbl">Videos</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 4 FEATURE CARDS -->
<section class="hero-features-strip">
    <div class="container-fluid px-3 px-lg-5">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="hero-feature-card">
                    <span class="hero-feature-icon" style="--fc:#6c63ff"><i class="bi bi-lightning-charge-fill"></i></span>
                    <div>
                        <div class="text-white fw-600">Premium Quality</div>
                        <div class="text-secondary small">High resolution audio<br>for the best experience</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="hero-feature-card">
                    <span class="hero-feature-icon" style="--fc:#6c63ff"><i class="bi bi-infinity"></i></span>
                    <div>
                        <div class="text-white fw-600">Unlimited Streaming</div>
                        <div class="text-secondary small">Stream anytime,<br>anywhere</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="hero-feature-card">
                    <span class="hero-feature-icon" style="--fc:#6c63ff"><i class="bi bi-speedometer2"></i></span>
                    <div>
                        <div class="text-white fw-600">Fast &amp; Reliable</div>
                        <div class="text-secondary small">Lightning fast performance<br>you can trust</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="hero-feature-card">
                    <span class="hero-feature-icon" style="--fc:#6c63ff"><i class="bi bi-shield-check"></i></span>
                    <div>
                        <div class="text-white fw-600">Made for You</div>
                        <div class="text-secondary small">Personalized music<br>for every mood</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- LATEST MUSIC -->
<section class="lm-section container-fluid px-3 px-lg-5 py-5">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <span class="lm-eyebrow">FRESH TRACKS, RIGHT NOW</span>
            <h2 class="lm-title mt-1 mb-1">Latest Music</h2>
            <p class="lm-subtitle mb-0">Recently added songs you'll love.</p>
        </div>
        <a href="<?= url('music.php') ?>" class="lm-view-all-btn">View All <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

    <div class="lm-carousel-wrapper position-relative">
        <!-- Prev Arrow -->
        <button class="lm-arrow lm-arrow-prev" data-carousel-prev="latest-music" id="lmPrev" aria-label="Previous">
            <i class="bi bi-chevron-left"></i>
        </button>

        <div class="lm-track" id="lmTrack" data-carousel="latest-music">
            <?php foreach ($latestSongs as $s):
                $img = $s['image_url'] ?: 'https://images.pexels.com/photos/352505/pexels-photo-352505.jpeg?auto=compress&cs=tinysrgb&w=600';
                $dur = $s['duration'] ?? '0:00';
                $rawViews = intval($s['views'] ?? 0);
                $views = $rawViews >= 1000 ? round($rawViews/1000, 1) . 'K' : $rawViews;
            ?>
            <div class="lm-card">
                <a href="<?= url('song_detail.php?id=' . $s['id']) ?>" class="lm-card-inner text-decoration-none d-block">
                    <!-- Thumb -->
                    <div class="lm-thumb">
                        <img src="<?= e($img) ?>" alt="<?= e($s['title']) ?>" loading="lazy">
                        <div class="lm-thumb-overlay"></div>
                        <span class="lm-badge-new">NEW</span>
                        <!-- Heart -->
                        <button class="lm-heart-btn favourite-btn" data-id="<?= $s['id'] ?>" data-type="song" onclick="event.preventDefault(); toggleFavourite(this)" aria-label="Like">
                            <i class="bi bi-heart"></i>
                        </button>
                        <!-- Play -->
                        <button class="lm-play-btn" onclick="event.preventDefault(); event.stopPropagation(); window.location.href='<?= url('song_detail.php?id=' . $s['id']) ?>'" aria-label="Play">
                            <i class="bi bi-play-fill"></i>
                        </button>
                    </div>
                    <!-- Info -->
                    <div class="lm-info">
                        <p class="lm-song-title"><?= e($s['title']) ?></p>
                        <p class="lm-artist"><?= e($s['artist_name'] ?? 'Unknown') ?></p>
                        <div class="lm-meta">
                            <span class="lm-meta-item"><i class="bi bi-music-note-beamed"></i> <?= e($dur) ?></span>
                            <span class="lm-meta-item"><i class="bi bi-eye"></i> <?= $views ?></span>
                            <span class="lm-meta-item"><i class="bi bi-clock"></i> Today</span>
                            <button class="lm-more-btn" onclick="event.preventDefault()" aria-label="More options"><i class="bi bi-three-dots-vertical"></i></button>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Next Arrow -->
        <button class="lm-arrow lm-arrow-next" data-carousel-next="latest-music" id="lmNext" aria-label="Next">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>
</section>

<!-- LATEST VIDEOS -->
<section class="lm-section container-fluid py-4">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <span class="lm-eyebrow">WATCH THE LATEST DROPS</span>
            <h2 class="lm-title mt-2">Latest Videos</h2>
            <p class="lm-subtitle mt-1 mb-0">Fresh visuals and music videos you'll love.</p>
        </div>
        <a href="<?= url('videos.php') ?>" class="lm-view-all-btn">View All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="lm-carousel-wrapper">
        <button class="lm-arrow lv-arrow-prev" data-carousel-prev="latest-videos" id="lvPrev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
        <div class="lm-track" id="lvTrack" data-carousel="latest-videos">
            <?php foreach ($latestVideos as $v):
                $img = $v['image_url'] ?: 'https://images.pexels.com/photos/38170212/pexels-photo-38170212.jpeg?auto=compress&cs=tinysrgb&w=600';
                $dur = $v['duration'] ?? '0:00';
                $rawViews = intval($v['views'] ?? 0);
                $views = $rawViews >= 1000 ? round($rawViews/1000, 1) . 'K' : $rawViews;
            ?>
            <div class="lm-card lv-card">
                <a href="<?= url('video_detail.php?id=' . $v['id']) ?>" class="lm-card-inner text-decoration-none d-block">
                    <div class="lv-thumb">
                        <img src="<?= e($img) ?>" alt="<?= e($v['title']) ?>" loading="lazy">
                        <div class="lm-thumb-overlay"></div>
                        <span class="lm-badge-new">NEW</span>
                        <button class="lm-heart-btn favourite-btn" data-id="<?= $v['id'] ?>" data-type="video" onclick="event.preventDefault(); toggleFavourite(this)" aria-label="Like">
                            <i class="bi bi-heart"></i>
                        </button>
                        <button class="lm-play-btn" onclick="event.preventDefault(); window.location.href='<?= url('watch_video.php?id=' . $v['id']) ?>'" aria-label="Play">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <span class="lv-duration"><?= e($dur) ?></span>
                    </div>
                    <div class="lm-info">
                        <p class="lm-song-title"><?= e($v['title']) ?></p>
                        <p class="lm-artist"><?= e($v['artist_name'] ?? 'Unknown') ?></p>
                        <div class="lm-meta">
                            <span class="lm-meta-item"><i class="bi bi-eye"></i> <?= $views ?></span>
                            <span class="lm-meta-item"><i class="bi bi-clock"></i> Today</span>
                            <button class="lm-more-btn" onclick="event.preventDefault()" aria-label="More options"><i class="bi bi-three-dots-vertical"></i></button>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="lm-arrow lv-arrow-next" data-carousel-next="latest-videos" id="lvNext" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
    </div>
</section>

<!-- TRENDING TODAY -->
<section class="lm-section tt-section container-fluid py-5">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <span class="lm-eyebrow">TRENDING TODAY</span>
            <h2 class="lm-title mt-2">What's Hot Right Now</h2>
            <p class="lm-subtitle mt-1 mb-0">The most trending songs today, loved by millions around the world.</p>
        </div>
        <a href="<?= url('music.php') ?>" class="lm-view-all-btn">View All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="lm-carousel-wrapper">
        <button class="lm-arrow lm-arrow-prev" data-carousel-prev="trending" id="ttPrev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
        <div class="lm-track" id="ttTrack" data-carousel="trending">
            <?php
            $trendingDisplay = array_slice($trendingSongs, 0, 12);
            $rankColors = ['#6c63ff','#0d9488','#2563eb','#10b981','#f97316','#ec4899','#8b5cf6','#06b6d4'];
            foreach ($trendingDisplay as $rank => $s):
                $img = $s['image_url'] ?: 'https://images.pexels.com/photos/352505/pexels-photo-352505.jpeg?auto=compress&cs=tinysrgb&w=600';
                $dur = $s['duration'] ?? '0:00';
                $rawViews = intval($s['views'] ?? 0);
                $views = $rawViews >= 1000 ? round($rawViews/1000, 1) . 'K' : $rawViews;
                $rawLikes = intval($s['likes'] ?? rand(40000, 120000));
                $likes = $rawLikes >= 1000 ? round($rawLikes/1000, 1) . 'K' : $rawLikes;
                $rankColor = $rankColors[$rank] ?? '#6c63ff';
            ?>
            <div class="lm-card tt-card">
                <a href="<?= url('song_detail.php?id=' . $s['id']) ?>" class="lm-card-inner text-decoration-none d-block">
                    <div class="lm-thumb">
                        <img src="<?= e($img) ?>" alt="<?= e($s['title']) ?>" loading="lazy">
                        <div class="lm-thumb-overlay"></div>
                        <!-- Rank badge -->
                        <span class="tt-rank" style="background:<?= $rankColor ?>;box-shadow:0 4px 14px -4px <?= $rankColor ?>88"><?= $rank + 1 ?></span>
                        <!-- Heart -->
                        <button class="lm-heart-btn favourite-btn" data-id="<?= $s['id'] ?>" data-type="song" onclick="event.preventDefault(); toggleFavourite(this)" aria-label="Like">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    <div class="lm-info">
                        <p class="lm-song-title"><?= e($s['title']) ?></p>
                        <p class="lm-artist"><?= e($s['artist_name'] ?? 'Unknown') ?></p>
                        <div class="lm-meta">
                            <span class="lm-meta-item tt-fire"><i class="bi bi-fire"></i> <?= $likes ?></span>
                            <span class="lm-meta-item"><i class="bi bi-eye"></i> <?= $views ?></span>
                            <span class="lm-meta-item"><i class="bi bi-clock"></i> <?= e($dur) ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="lm-arrow lm-arrow-next" data-carousel-next="trending" id="ttNext" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
    </div>

    <!-- Feature strip -->
    <div class="tt-features mt-5">
        <div class="tt-feat">
            <div class="tt-feat-icon" style="background:linear-gradient(135deg,#f97316,#ef4444);box-shadow:0 6px 20px -6px rgba(249,115,22,0.5)"><i class="bi bi-fire"></i></div>
            <div>
                <div class="tt-feat-title">Trending Daily</div>
                <div class="tt-feat-sub">Discover what's hot</div>
            </div>
        </div>
        <div class="tt-feat">
            <div class="tt-feat-icon" style="background:linear-gradient(135deg,#6c63ff,#a855f7);box-shadow:0 6px 20px -6px rgba(108,99,255,0.5)"><i class="bi bi-music-note-beamed"></i></div>
            <div>
                <div class="tt-feat-title">Personalized For You</div>
                <div class="tt-feat-sub">Songs you'll love</div>
            </div>
        </div>
        <div class="tt-feat">
            <div class="tt-feat-icon" style="background:linear-gradient(135deg,#0d9488,#06b6d4);box-shadow:0 6px 20px -6px rgba(13,148,136,0.5)"><i class="bi bi-lightning-charge-fill"></i></div>
            <div>
                <div class="tt-feat-title">High Quality Audio</div>
                <div class="tt-feat-sub">Crystal clear sound</div>
            </div>
        </div>
        <div class="tt-feat">
            <div class="tt-feat-icon" style="background:linear-gradient(135deg,#2563eb,#818cf8);box-shadow:0 6px 20px -6px rgba(37,99,235,0.5)"><i class="bi bi-headphones"></i></div>
            <div>
                <div class="tt-feat-title">Made For Every Mood</div>
                <div class="tt-feat-sub">Music for every moment</div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED ALBUMS -->
<section class="container-fluid px-3 px-lg-4 py-4">
    <div class="fa-section">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4 fa-head">
            <div>
                <span class="fa-eyebrow">Curated Picks</span>
                <h2 class="fa-title">Featured <span class="accent">Albums</span></h2>
                <p class="fa-sub">Hand-picked albums from top artists — fresh drops and timeless classics.</p>
            </div>
            <a href="<?= url('albums.php') ?>" class="fa-viewall">View all <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="fa-grid">
            <?php foreach ($albums as $al):
                $img = $al['image_url'] ?: 'https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600';
                $songCount = (int)($al['song_count'] ?? 0);
                $year = !empty($al['year']) ? e($al['year']) : (date('Y'));
            ?>
                <a href="<?= url('album_detail.php?id=' . $al['id']) ?>" class="fa-card">
                    <div class="fa-thumb">
                        <img src="<?= e($img) ?>" alt="<?= e($al['title']) ?>" loading="lazy">
                        <div class="fa-thumb-overlay"></div>
                        <span class="fa-badge"><i class="bi bi-disc"></i> Album</span>
                        <span class="fa-count"><?= $songCount ?> tracks</span>
                        <span class="fa-play"><i class="bi bi-play-fill"></i></span>
                    </div>
                    <div class="fa-info">
                        <h3 class="fa-title-row"><?= e($al['title']) ?></h3>
                        <p class="fa-artist"><?= e($al['artist_name'] ?? 'Unknown Artist') ?></p>
                        <div class="fa-meta">
                            <span class="fa-meta-item"><i class="bi bi-music-note-beamed"></i> <?= $songCount ?> songs</span>
                            <span class="fa-meta-dot"></span>
                            <span class="fa-meta-item"><i class="bi bi-calendar3"></i> <?= $year ?></span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Feature pills -->
        <div class="fa-pills">
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#6c63ff,#a855f7);box-shadow:0 6px 20px -6px rgba(108,99,255,0.5)"><i class="bi bi-collection-play"></i></span>
                <div>
                    <div class="fa-pill-title">Curated Albums</div>
                    <div class="fa-pill-sub">Hand-picked collections</div>
                </div>
            </div>
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#0d9488,#06b6d4);box-shadow:0 6px 20px -6px rgba(13,148,136,0.5)"><i class="bi bi-lightning-charge-fill"></i></span>
                <div>
                    <div class="fa-pill-title">High Quality Audio</div>
                    <div class="fa-pill-sub">Crystal clear sound</div>
                </div>
            </div>
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#ec4899,#f97316);box-shadow:0 6px 20px -6px rgba(236,72,153,0.5)"><i class="bi bi-stars"></i></span>
                <div>
                    <div class="fa-pill-title">New Releases</div>
                    <div class="fa-pill-sub">Fresh every week</div>
                </div>
            </div>
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#2563eb,#818cf8);box-shadow:0 6px 20px -6px rgba(37,99,235,0.5)"><i class="bi bi-headphones"></i></span>
                <div>
                    <div class="fa-pill-title">Made For Every Mood</div>
                    <div class="fa-pill-sub">Music for every moment</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED ARTISTS -->
<section class="container-fluid px-3 px-lg-4 py-4">
    <div class="fart-section">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4 fart-head">
            <div>
                <span class="fart-eyebrow">Rising Stars</span>
                <h2 class="fart-title">Featured <span class="accent">Artists</span></h2>
                <p class="fart-sub">Discover talented creators shaping the sound of today and tomorrow.</p>
            </div>
            <a href="<?= url('artists.php') ?>" class="fart-viewall">View all <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="fart-grid">
            <?php foreach ($artists as $a):
                $img = $a['image_url'] ?: 'https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=400';
                $songCount = (int)($a['song_count'] ?? 0);
                $listeners = max($songCount * 340, 1200);
                $genreName = !empty($a['genre']) ? e($a['genre']) : 'Multi-genre';
            ?>
                <a href="<?= url('artist_detail.php?id=' . $a['id']) ?>" class="fart-card">
                    <div class="fart-avatar-wrap">
                        <span class="fart-avatar-ring"></span>
                        <img src="<?= e($img) ?>" alt="<?= e($a['name']) ?>" class="fart-avatar" loading="lazy">
                        <span class="fart-verified" title="Verified artist"><i class="bi bi-check-lg"></i></span>
                        <span class="fart-avatar-play"><i class="bi bi-play-fill"></i></span>
                    </div>
                    <h3 class="fart-name"><?= e($a['name']) ?></h3>
                    <p class="fart-genre">Artist</p>
                    <div class="fart-meta">
                        <span class="fart-meta-item"><i class="bi bi-fire text-warning"></i> <?= number_format($listeners) ?><br><span style="font-size:0.65rem;display:block;color:var(--text-light)">Followers</span></span>
                        <span class="fart-meta-dot"></span>
                        <span class="fart-meta-item"><i class="bi bi-music-note-beamed"></i> <?= $songCount ?><br><span style="font-size:0.65rem;display:block;color:var(--text-light)">Songs</span></span>
                    </div>
                    <button class="fart-follow" type="button" data-id="<?= (int)$a['id'] ?>"><i class="bi bi-plus-lg me-1"></i>Follow</button>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Feature pills -->
        <div class="fa-pills fa-pills-5">
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#0d9488,#06b6d4);box-shadow:0 6px 20px -6px rgba(13,148,136,0.5)"><i class="bi bi-lightning-charge-fill"></i></span>
                <div>
                    <div class="fa-pill-title">High Quality Audio</div>
                    <div class="fa-pill-sub">Crystal clear sound</div>
                </div>
            </div>
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#6c63ff,#a855f7);box-shadow:0 6px 20px -6px rgba(108,99,255,0.5)"><i class="bi bi-stars"></i></span>
                <div>
                    <div class="fa-pill-title">New Releases</div>
                    <div class="fa-pill-sub">Fresh music every day</div>
                </div>
            </div>
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#ec4899,#f43f5e);box-shadow:0 6px 20px -6px rgba(236,72,153,0.5)"><i class="bi bi-heart-fill"></i></span>
                <div>
                    <div class="fa-pill-title">Your Favorites</div>
                    <div class="fa-pill-sub">All in one place</div>
                </div>
            </div>
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#2563eb,#818cf8);box-shadow:0 6px 20px -6px rgba(37,99,235,0.5)"><i class="bi bi-download"></i></span>
                <div>
                    <div class="fa-pill-title">Offline Listening</div>
                    <div class="fa-pill-sub">Listen anytime</div>
                </div>
            </div>
            <div class="fa-pill">
                <span class="fa-pill-icon" style="background:linear-gradient(135deg,#059669,#10b981);box-shadow:0 6px 20px -6px rgba(5,150,105,0.5)"><i class="bi bi-shield-check"></i></span>
                <div>
                    <div class="fa-pill-title">Made for You</div>
                    <div class="fa-pill-sub">Personalized experience</div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- BROWSE BY CATEGORY -->
<section class="container-fluid px-3 px-lg-4 py-4">
    <div class="bc-section">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
            <div>
                <span class="bc-eyebrow">Explore Genres</span>
                <h2 class="bc-title">Browse by <span class="accent">Category</span></h2>
                <p class="bc-sub">Dive into your favourite genres and moods — everything from pop to classical.</p>
            </div>
            <a href="<?= url('categories.php') ?>" class="bc-viewall">View all <i class="bi bi-grid"></i></a>
        </div>

        <div class="bc-grid">
            <?php
            $bcImgs = [
                'https://images.pexels.com/photos/3755771/pexels-photo-3755771.png?auto=compress&cs=tinysrgb&w=500',
                'https://images.pexels.com/photos/1201112/pexels-photo-1201112.jpeg?auto=compress&cs=tinysrgb&w=500',
                'https://images.pexels.com/photos/3721941/pexels-photo-3721941.jpeg?auto=compress&cs=tinysrgb&w=500',
                'https://images.pexels.com/photos/48592/pexels-photo-48592.jpeg?auto=compress&cs=tinysrgb&w=500',
                'https://images.pexels.com/photos/1688259/pexels-photo-1688259.jpeg?auto=compress&cs=tinysrgb&w=500',
                'https://images.pexels.com/photos/4482872/pexels-photo-4482872.jpeg?auto=compress&cs=tinysrgb&w=500',
            ];
            $bcIcons = ['bi-music-note-beamed', 'bi-globe-asia-australia', 'bi-people-fill', 'bi-fire', 'bi-heart-fill', 'bi-disc'];
            $bcGrads = [
                'linear-gradient(135deg,#6c63ff,#a855f7)',
                'linear-gradient(135deg,#ec4899,#f43f5e)',
                'linear-gradient(135deg,#0d9488,#06b6d4)',
                'linear-gradient(135deg,#f97316,#ef4444)',
                'linear-gradient(135deg,#2563eb,#818cf8)',
                'linear-gradient(135deg,#059669,#10b981)',
            ];
            $bcI = 0;
            foreach ($genres as $g):
                $gi = $bcI % 6;
                $img = $bcImgs[$gi];
                $icon = $bcIcons[$gi];
                $grad = $bcGrads[$gi];
                $songCount = (int)($g['song_count'] ?? 0);
            ?>
                <a href="<?= url('category.php?id=' . $g['id']) ?>" class="bc-card">
                    <img src="<?= e($img) ?>" alt="<?= e($g['name']) ?>" class="bc-bg" loading="lazy">
                    <div class="bc-overlay"></div>
                    <div class="bc-icon-wrap">
                        <i class="bi <?= $icon ?>" style="background:<?= $grad ?>;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-size:1.8rem"></i>
                    </div>
                    <div class="bc-info">
                        <h3 class="bc-name"><?= e($g['name']) ?></h3>
                        <p class="bc-count"><?= $songCount ?> songs</p>
                        <span class="bc-go"><i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            <?php $bcI++; endforeach; ?>
        </div>

        <div class="bc-tags">
            <?php
            $tagIcons = ['bi-music-note', 'bi-globe2', 'bi-people', 'bi-fire', 'bi-heart', 'bi-disc', 'bi-boombox', 'bi-broadcast'];
            $bcI = 0;
            foreach ($genres as $g):
                $ti = $bcI % count($tagIcons);
            ?>
                <a href="<?= url('category.php?id=' . $g['id']) ?>" class="bc-tag"><i class="bi <?= $tagIcons[$ti] ?>"></i> <?= e($g['name']) ?></a>
            <?php $bcI++; endforeach; ?>
        </div>
    </div>
</section>

<!-- JOIN COMMUNITY (only for guests) -->
<?php if (!is_logged_in()): ?>
<section class="container-fluid px-3 px-lg-4 py-4">
    <div class="cta-band">
        <div class="cta-band-bg"></div>
        <div class="cta-band-overlay"></div>
        <div class="cta-band-glow"></div>
        <div class="cta-band-content d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 p-4 p-lg-5">
            <div>
                <h3 class="cta-band-title">Join the SOUND community</h3>
                <p class="cta-band-sub mb-0">Create a free account to rate your favorite tracks, build playlists, and keep up with the latest releases.</p>
            </div>
            <a href="<?= url('register.php') ?>" class="cta-band-btn"><i class="bi bi-rocket-takeoff"></i> Get Started</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php render_footer(); ?>

<script>
// Featured Artists follow toggle
(function() {
  document.querySelectorAll('.fart-follow').forEach(function(btn) {
    btn.addEventListener('click', function(ev) {
      ev.preventDefault();
      ev.stopPropagation();
      const followed = btn.classList.toggle('followed');
      btn.innerHTML = followed
        ? '<i class="bi bi-check-lg me-1"></i>Following'
        : '<i class="bi bi-plus-lg me-1"></i>Follow';
    });
  });
})();
</script>
