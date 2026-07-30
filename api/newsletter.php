<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email.']);
    exit;
}

require_once __DIR__ . '/../includes/models.php';
$ok = add_newsletter($email);
echo json_encode([
    'success' => true,
    'message' => $ok ? 'Subscribed successfully!' : 'You are already subscribed.',
]);
