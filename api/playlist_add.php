<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if (!verify_csrf($_POST['csrf'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$playlist_id = (int)($_POST['playlist_id'] ?? 0);
$media_type = $_POST['media_type'] ?? '';
$media_id = (int)($_POST['media_id'] ?? 0);

$playlist = get_playlist($playlist_id, $user_id);
if (!$playlist) {
    echo json_encode(['success' => false, 'error' => 'Playlist not found']);
    exit;
}

if ($action === 'add') {
    if ($media_type === 'song') {
        $check = db()->prepare("SELECT id FROM playlist_items WHERE playlist_id=? AND song_id=?");
        $check->execute([$playlist_id, $media_id]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Already in playlist']);
            exit;
        }
        db()->prepare("INSERT INTO playlist_items (playlist_id, song_id) VALUES (?,?)")->execute([$playlist_id, $media_id]);
    } elseif ($media_type === 'video') {
        $check = db()->prepare("SELECT id FROM playlist_items WHERE playlist_id=? AND video_id=?");
        $check->execute([$playlist_id, $media_id]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Already in playlist']);
            exit;
        }
        db()->prepare("INSERT INTO playlist_items (playlist_id, video_id) VALUES (?,?)")->execute([$playlist_id, $media_id]);
    }
    echo json_encode(['success' => true, 'message' => 'Added to playlist']);
} else {
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
}
