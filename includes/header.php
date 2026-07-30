<?php
require_once __DIR__ . '/../includes/functions.php';

/**
 * Render the top <head>, header, and navbar. Call render_header($title) at the top of each page.
 */
function render_header(string $title = '', string $active = '', string $description = ''): void {
    $siteName = SITE_NAME;
    $pageTitle = $title ? $title . ' • ' . $siteName : $siteName . ' • ' . SITE_TAGLINE;
    $desc = $description ?: 'SOUND — stream the latest music and videos across English and regional languages. Browse by album, artist, year, genre and language.';
    $ogUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');
    $user = current_user();
    $cartCount = 0;
    $navItems = [
        ['url' => 'index.php',      'label' => 'Home',       'key' => 'home',       'icon' => 'bi-house-fill'],
        ['url' => 'music.php',      'label' => 'Music',      'key' => 'music',      'icon' => 'bi-music-note-beamed'],
        ['url' => 'videos.php',     'label' => 'Videos',     'key' => 'videos',     'icon' => 'bi-play-btn'],
        ['url' => 'albums.php',     'label' => 'Albums',     'key' => 'albums',     'icon' => 'bi-disc'],
        ['url' => 'artists.php',    'label' => 'Artists',    'key' => 'artists',    'icon' => 'bi-person-bounding-box'],
        ['url' => 'categories.php', 'label' => 'Categories', 'key' => 'categories', 'icon' => 'bi-grid-fill'],
    ];
    ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($desc) ?>">
    <meta name="keywords" content="music, video, streaming, songs, albums, artists, playlists, entertainment">
    <meta name="author" content="SOUND Entertainment">
    <meta name="robots" content="index, follow">
    <!-- OpenGraph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($desc) ?>">
    <meta property="og:url" content="<?= e($ogUrl) ?>">
    <meta property="og:site_name" content="SOUND">
    <meta property="og:image" content="<?= e(url('public/images/default-cover.webp')) ?>">
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= e($desc) ?>">
    <meta name="twitter:image" content="<?= e(url('public/images/default-cover.webp')) ?>">
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
<!-- PAGE LOADER -->
<div id="pageLoader" class="page-loader">
    <div class="loader-logo"><i class="bi bi-soundwave"></i></div>
    <div class="loader-text">Loading SOUND…</div>
</div>
<!-- FLASH DATA (for toast notifications) -->
<script id="flashData" type="application/json"><?= json_encode(get_all_flashes()) ?></script>
<a class="visually-hidden-focusable skip-link" href="#main">Skip to content</a>

<!-- HEADER / NAVBAR -->
<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-3 px-lg-4 d-flex align-items-center gap-2 gap-lg-3">

            <!-- BRAND -->
            <a class="navbar-brand d-flex align-items-center gap-2 flex-shrink-0" href="<?= url('index.php') ?>">
                <span class="brand-logo"><i class="bi bi-soundwave"></i></span>
                <span class="d-flex flex-column lh-1">
                    <span class="brand-name">SOUND</span>
                    <span class="brand-tagline">Feel The Music</span>
                </span>
            </a>

            <button class="navbar-toggler border-0 ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-3 text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <!-- NAV ITEMS: icon on top, label below -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <?php foreach ($navItems as $item): ?>
                        <li class="nav-item">
                            <a class="nav-vpill <?= $active === $item['key'] ? 'active' : '' ?>" href="<?= url($item['url']) ?>">
                                <i class="bi <?= $item['icon'] ?>"></i>
                                <span><?= e($item['label']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- SEARCH -->
                <form class="d-flex my-2 my-lg-0 search-form" action="<?= url('search.php') ?>" method="get" role="search">
                    <div class="input-group">
                        <input class="form-control srch-input" type="search" name="q" id="globalSearch" placeholder="Search songs, artists, albums…" autocomplete="off" aria-label="Search">
                        <button class="btn btn-search-submit" type="submit"><i class="bi bi-search"></i></button>
                        <div id="searchSuggestions" class="search-suggestions" role="listbox"></div>
                    </div>
                </form>

                <!-- RIGHT ACTIONS -->
                <ul class="navbar-nav align-items-lg-center gap-1 flex-row flex-wrap ms-lg-2">
                    <!-- Favourites -->
                    <li class="nav-item">
                        <a href="<?= url($user ? 'dashboard.php?tab=favourites' : 'login.php') ?>" class="nav-icon-btn position-relative" aria-label="Favourites">
                            <i class="bi bi-heart-fill text-pink"></i>
                            <?php if ($user): ?>
                                <span class="nav-badge" id="favBadge">0</span>
                            <?php endif; ?>
                        </a>
                    </li>


                    <?php if ($user): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link d-flex align-items-center gap-2 ps-1 py-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="--bs-dropdown-toggle-icon-display:none">
                                <span class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
                                <i class="bi bi-chevron-down text-secondary small d-none d-xl-inline"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="px-3 py-2"><span class="text-white fw-600 small"><?= e($user['username']) ?></span><br><span class="text-secondary" style="font-size:0.72rem"><?= e($user['email'] ?? '') ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= url('dashboard.php') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= url('playlists.php') ?>"><i class="bi bi-music-note-list me-2"></i>Playlists</a></li>
                                <li><a class="dropdown-item" href="<?= url('profile.php') ?>"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning" href="<?= url('admin/index.php') ?>"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger-soft" href="<?= url('logout.php') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-outline-light btn-sm me-1" href="<?= url('login.php') ?>">Sign In</a></li>
                        <li class="nav-item"><a class="btn btn-primary btn-sm" href="<?= url('register.php') ?>">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main id="main">
<?php
}
