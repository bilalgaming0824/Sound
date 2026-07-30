<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');
if ($q === '' || strlen($q) < 2) { echo json_encode([]); exit; }

require_once __DIR__ . '/../includes/models.php';
$like = "%$q%";

$songs = db()->prepare("SELECT s.id, s.title, s.image_url, a.name AS artist
        FROM songs s LEFT JOIN artists a ON s.artist_id = a.id
        WHERE s.title LIKE ? OR a.name LIKE ? LIMIT 4");
$songs->execute([$like, $like]);

$videos = db()->prepare("SELECT v.id, v.title, v.image_url, a.name AS artist
        FROM videos v LEFT JOIN artists a ON v.artist_id = a.id
        WHERE v.title LIKE ? OR a.name LIKE ? LIMIT 3");
$videos->execute([$like, $like]);

$out = [];
foreach ($songs->fetchAll() as $s) {
    $out[] = ['type' => 'song', 'id' => $s['id'], 'title' => $s['title'], 'image' => $s['image_url'], 'meta' => $s['artist'] ?: 'Unknown'];
}
foreach ($videos->fetchAll() as $v) {
    $out[] = ['type' => 'video', 'id' => $v['id'], 'title' => $v['title'], 'image' => $v['image_url'], 'meta' => $v['artist'] ?: 'Unknown'];
}
echo json_encode($out);
