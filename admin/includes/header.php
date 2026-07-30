<?php
require_once __DIR__ . '/../../includes/functions.php';
require_admin();
require_once __DIR__ . '/../../includes/models.php';

$section = $section ?? 'dashboard';
$adminNav = [
    ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'url' => 'index.php'],
    ['key' => 'songs', 'label' => 'Songs', 'icon' => 'bi-music-note-beamed', 'url' => 'songs.php'],
    ['key' => 'videos', 'label' => 'Videos', 'icon' => 'bi-play-btn', 'url' => 'videos.php'],
    ['key' => 'albums', 'label' => 'Albums', 'icon' => 'bi-disc', 'url' => 'albums.php'],
    ['key' => 'artists', 'label' => 'Artists', 'icon' => 'bi-mic', 'url' => 'artists.php'],
    ['key' => 'categories', 'label' => 'Categories', 'icon' => 'bi-tag', 'url' => 'categories.php'],
    ['key' => 'users', 'label' => 'Users', 'icon' => 'bi-people', 'url' => 'users.php'],
    ['key' => 'comments', 'label' => 'Comments', 'icon' => 'bi-chat-square-text', 'url' => 'comments.php'],
    ['key' => 'analytics', 'label' => 'Analytics', 'icon' => 'bi-graph-up', 'url' => 'analytics.php'],
    ['key' => 'logs', 'label' => 'Activity Logs', 'icon' => 'bi-clock-history', 'url' => 'logs.php'],
    ['key' => 'newsletter', 'label' => 'Newsletter', 'icon' => 'bi-envelope', 'url' => 'newsletter.php'],
];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> • SOUND Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper d-flex">
    <aside class="admin-sidebar">
        <a href="<?= url('index.php') ?>" class="navbar-brand d-flex align-items-center gap-2 mb-4">
            <span class="brand-logo"><i class="bi bi-soundwave"></i></span>
            <span class="brand-name">SOUND</span>
        </a>
        <span class="text-secondary small text-uppercase px-2 mb-2 d-block">Admin Panel</span>
        <nav class="admin-nav">
            <?php foreach ($adminNav as $item): ?>
                <a href="<?= url('admin/' . $item['url']) ?>" class="admin-nav-link <?= $section === $item['key'] ? 'active' : '' ?>">
                    <i class="bi <?= $item['icon'] ?>"></i> <?= $item['label'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <hr class="border-ink">
        <a href="<?= url('index.php') ?>" class="admin-nav-link"><i class="bi bi-house"></i> Back to Site</a>
        <a href="<?= url('logout.php') ?>" class="admin-nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </aside>
    <main class="admin-main flex-grow-1">
        <header class="admin-topbar">
            <button class="btn btn-ghost d-lg-none" id="sidebarToggle"><i class="bi bi-list fs-4"></i></button>
            <h1 class="admin-page-title"><?= e($pageTitle ?? 'Dashboard') ?></h1>
            <div class="ms-auto d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-ghost position-relative" type="button" id="notifBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;display:none" id="notifCount">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width:320px;max-height:400px;overflow-y:auto" id="notifDropdown">
                        <h6 class="dropdown-header text-white fw-bold"><i class="bi bi-bell me-2"></i>Notifications</h6>
                        <div id="notifList">
                            <?php $notifs = get_admin_notifications(8); foreach ($notifs as $n): ?>
                                <div class="dropdown-item d-flex align-items-start gap-2 py-2">
                                    <span class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:8px;background:<?= $n['color'] ?>20;color:<?= $n['color'] ?>;font-size:0.85rem"><i class="bi <?= $n['icon'] ?>"></i></span>
                                    <div class="flex-grow-1"><div class="small text-white"><?= e($n['text']) ?></div><div class="text-secondary" style="font-size:0.7rem"><?= time_ago($n['time']) ?></div></div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($notifs)): ?><div class="dropdown-item-text text-center text-secondary small py-3">No notifications</div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <span class="avatar"><?= strtoupper(substr($_SESSION['username'],0,1)) ?></span>
                <span class="d-none d-md-inline text-secondary small"><?= e($_SESSION['username']) ?></span>
            </div>
        </header>
        <div class="admin-content">
