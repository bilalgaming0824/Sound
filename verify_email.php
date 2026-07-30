<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) { redirect('index.php'); }

$token = trim($_GET['token'] ?? '');
$message = '';
$success = false;

if ($token) {
    $stmt = db()->prepare("SELECT user_id, used FROM email_verifications WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        $message = 'Invalid or expired verification link.';
    } elseif ($row['used']) {
        $message = 'This email has already been verified.';
        $success = true;
    } else {
        db()->prepare("UPDATE email_verifications SET used = 1 WHERE token = ?")->execute([$token]);
        db()->prepare("UPDATE users SET email_verified = 1 WHERE id = ?")->execute([$row['user_id']]);
        $success = true;
        $message = 'Your email has been verified! You can now sign in.';
    }
}

$siteName = SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Verification • <?= e($siteName) ?></title>
<link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh">
    <div class="card-media p-5 text-center" style="max-width:480px">
        <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;border-radius:50%;background:rgba(108,99,255,0.15);color:var(--primary);font-size:2.5rem">
            <i class="bi bi-envelope-check<?= $success ? '-fill' : '' ?>"></i>
        </div>
        <h1 class="fw-bold text-white mb-2" style="font-family:'Sora',sans-serif">Email Verification</h1>
        <?php if ($message): ?>
            <p class="text-secondary mb-3"><?= e($message) ?></p>
        <?php else: ?>
            <p class="text-secondary mb-3">Please check your email for a verification link to activate your account.</p>
        <?php endif; ?>
        <a href="<?= url('login.php') ?>" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Go to Sign In</a>
    </div>
</div>
</body>
</html>
