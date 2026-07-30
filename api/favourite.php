<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if (!is_logged_in()) { echo json_encode(['success' => false, 'message' => 'Login required.']); exit; }

require_once __DIR__ . '/../includes/models.php';
$userId = (int)$_SESSION['user_id'];

// GET ?action=count — returns favourite count for badge
if (($_GET['action'] ?? '') === 'count') {
    $count = count_user_favourites($userId);
    echo json_encode(['count' => $count]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['success' => false, 'message' => 'Invalid request.']); exit; }

$token = $_POST['csrf'] ?? '';
if (!verify_csrf($token)) { echo json_encode(['success' => false, 'message' => 'Invalid token.']); exit; }

$type = $_POST['type'] ?? '';
$mediaId = (int)($_POST['id'] ?? 0);

if (!in_array($type, ['song','video']) || $mediaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item.']); exit;
}

$state = toggle_favourite($userId, $type, $mediaId);
echo json_encode(['success' => true, 'favourite' => $state]);
