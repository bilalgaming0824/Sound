<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if (!is_logged_in()) { echo json_encode(['success' => false, 'message' => 'Login required.']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success' => false, 'message' => 'Invalid request.']); exit; }

$token = $_POST['csrf'] ?? '';
if (!verify_csrf($token)) { echo json_encode(['success' => false, 'message' => 'Invalid token.']); exit; }

require_once __DIR__ . '/../includes/models.php';
$type = $_POST['type'] ?? '';
$mediaId = (int)($_POST['id'] ?? 0);
$score = (int)($_POST['score'] ?? 0);
$userId = (int)$_SESSION['user_id'];

if (!in_array($type, ['song','video']) || $mediaId <= 0 || $score < 1 || $score > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid rating.']); exit;
}

db()->prepare("INSERT INTO ratings (user_id, media_type, media_id, score) VALUES (?,?,?,?)
        ON DUPLICATE KEY UPDATE score = VALUES(score)")->execute([$userId, $type, $mediaId, $score]);

$avg = get_avg_rating($type, $mediaId);
$count = get_rating_count($type, $mediaId);
echo json_encode(['success' => true, 'avg' => $avg, 'count' => $count]);
