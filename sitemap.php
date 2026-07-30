<?php
ob_start();
require_once __DIR__ . '/config/config.php';
ob_end_clean();

header('Content-Type: application/xml; charset=UTF-8');

$base = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL;
$pages = [
    ['url' => 'index.php',      'priority' => '1.0'],
    ['url' => 'music.php',      'priority' => '0.9'],
    ['url' => 'videos.php',     'priority' => '0.9'],
    ['url' => 'albums.php',     'priority' => '0.8'],
    ['url' => 'artists.php',    'priority' => '0.8'],
    ['url' => 'categories.php', 'priority' => '0.8'],
    ['url' => 'search.php',     'priority' => '0.7'],
    ['url' => 'contact.php',    'priority' => '0.6'],
    ['url' => 'about.php',      'priority' => '0.6'],
    ['url' => 'faq.php',        'priority' => '0.5'],
    ['url' => 'terms.php',      'priority' => '0.4'],
    ['url' => 'login.php',      'priority' => '0.3'],
    ['url' => 'register.php',   'priority' => '0.3'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($pages as $p) {
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($base . '/' . $p['url']) . '</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    echo '    <priority>' . $p['priority'] . '</priority>' . "\n";
    echo '  </url>' . "\n";
}
echo '</urlset>';
