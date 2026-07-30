<?php
header('Content-Type: application/xml');
$base = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL;
$pages = ['index.php', 'music.php', 'videos.php', 'albums.php', 'artists.php', 'categories.php', 'search.php', 'contact.php', 'about.php', 'faq.php', 'terms.php', 'login.php', 'register.php'];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($pages as $p) {
    echo "  <url><loc>$base/" . htmlspecialchars($p) . "</loc><lastmod>" . date('Y-m-d') . "</lastmod><priority>0.8</priority></url>\n";
}
echo '</urlset>';
