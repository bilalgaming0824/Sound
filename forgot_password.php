<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) { redirect('index.php'); }

$step = 'request';
$token = trim($_GET['token'] ?? '');
$error = '';
$success = '';

if ($token !== '' && verify_password_reset($token)) {
    $step = 'reset';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { $error = 'Invalid request.'; }
    elseif ($step === 'reset' || ($_POST['action'] ?? '') === 'reset') {
        $token = trim($_POST['token'] ?? '');
        $userId = verify_password_reset($token);
        if (!$userId) { $error = 'Invalid or expired reset link.'; $step = 'request'; }
        else {
            $pass = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if (strlen($pass) < 6) { $error = 'Password must be at least 6 characters.'; }
            elseif ($pass !== $confirm) { $error = 'Passwords do not match.'; }
            else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                db()->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $userId]);
                consume_password_reset($token);
                log_activity($userId, 'password_reset', 'Password reset via email');
                set_flash('success', 'Password reset successfully. Please sign in.');
                redirect('login.php');
            }
        }
    } else {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') { $error = 'Please enter your email.'; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Please enter a valid email.'; }
        else {
            $stmt = db()->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user) {
                $token = create_password_reset((int)$user['id']);
                $resetLink = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL . '/forgot_password.php?token=' . $token;
                log_activity((int)$user['id'], 'password_reset_request', 'Reset requested for ' . $email);
                $success = "A password reset link has been generated. Click the link below to reset your password:\n\n" . $resetLink;
            } else {
                $success = "If an account exists for that email, a reset link has been sent.";
            }
        }
    }
}

$siteName = SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password &bull; <?= e($siteName) ?></title>
<link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<style>
*,:before,:after{box-sizing:border-box}
:root{--primary:#6C63FF;--pink:#FF4D6D;--dark:#0d0d14;--card:#16161f;--border:rgba(255,255,255,.08);--text:#fff;--muted:#9CA3AF}
body{font-family:'Inter',sans-serif;background:var(--dark);color:var(--muted);min-height:100vh;margin:0;display:flex;align-items:center;justify-content:center;padding:2rem;position:relative;overflow:hidden}
body::before{content:'';position:absolute;inset:0;background:radial-gradient(600px 400px at 30% 20%,rgba(108,99,255,0.12),transparent 60%),radial-gradient(500px 400px at 70% 80%,rgba(255,77,109,0.08),transparent 60%);pointer-events:none}
.forgot-box{width:100%;max-width:440px;position:relative;z-index:1}
.brand-row{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:2rem}
.brand-icon{width:44px;height:44px;border-radius:13px;background:linear-gradient(135deg,var(--primary),var(--pink));display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;box-shadow:0 0 30px -6px rgba(108,99,255,0.7)}
.brand-name{font-family:'Sora',sans-serif;font-weight:800;font-size:1.5rem;color:#fff}
.forgot-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:2.2rem;box-shadow:0 40px 80px -20px rgba(0,0,0,0.6),0 0 0 1px rgba(255,255,255,0.04) inset}
.forgot-icon{width:64px;height:64px;border-radius:50%;margin:0 auto 1.2rem;display:flex;align-items:center;justify-content:center;background:rgba(108,99,255,0.15);color:var(--primary);font-size:1.7rem}
.forgot-card h1{font-family:'Sora',sans-serif;font-weight:800;font-size:1.6rem;color:#fff;text-align:center;margin-bottom:0.4rem}
.forgot-card .sub{text-align:center;font-size:0.88rem;color:var(--muted);margin-bottom:1.5rem}
.form-label{color:#c0c0cc;font-size:0.85rem;font-weight:500;margin-bottom:6px}
.input-wrap{position:relative}
.input-wrap .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:1rem;pointer-events:none;z-index:2}
.input-wrap .form-control{background:#0d0d14;border:1px solid var(--border);color:#fff;padding:12px 14px 12px 42px;border-radius:12px;font-size:0.9rem;transition:border-color 0.2s,box-shadow 0.2s}
.input-wrap .form-control:focus{background:#0d0d14;border-color:var(--primary);color:#fff;box-shadow:0 0 0 3px rgba(108,99,255,0.2);outline:none}
.input-wrap .form-control::placeholder{color:rgba(255,255,255,0.25)}
.input-wrap .toggle-pass{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);padding:0;cursor:pointer;font-size:1rem}
.input-wrap .toggle-pass:hover{color:#fff}
.btn-auth{width:100%;padding:12px;border-radius:12px;font-weight:600;font-size:0.95rem;background:linear-gradient(135deg,var(--primary),#8B5CF6);border:none;color:#fff;transition:all 0.25s}
.btn-auth:hover{transform:translateY(-2px);box-shadow:0 12px 40px -10px rgba(108,99,255,0.6);color:#fff}
.alert-auth{border-radius:12px;padding:10px 14px;font-size:0.85rem;background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.3);color:#ff8fa3;display:flex;align-items:center;gap:8px;margin-bottom:1.2rem}
.alert-success{background:rgba(52,211,153,0.1);border-color:rgba(52,211,153,0.3);color:#6EE7B7;white-space:pre-wrap}
.back-link{text-align:center;margin-top:1.2rem;font-size:0.85rem}
.back-link a{color:var(--primary);font-weight:500;text-decoration:none}
.back-link a:hover{text-decoration:underline}
</style>
</head>
<body>
<div class="forgot-box">
  <div class="brand-row">
    <div class="brand-icon"><i class="bi bi-soundwave"></i></div>
    <span class="brand-name">SOUND</span>
  </div>

  <div class="forgot-card">
    <div class="forgot-icon"><i class="bi bi-shield-lock"></i></div>
    <h1><?= $step === 'reset' ? 'Reset Password' : 'Forgot Password' ?></h1>
    <p class="sub"><?= $step === 'reset' ? 'Enter your new password below.' : "Enter your email and we'll send you a reset link." ?></p>

    <?php if ($error): ?>
      <div class="alert-auth"><i class="bi bi-exclamation-circle-fill"></i><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert-auth alert-success"><i class="bi bi-check-circle-fill flex-shrink-0"></i><?= e($success) ?></div>
      <div class="back-link"><a href="<?= url('login.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back to Sign In</a></div>
    <?php else: ?>
      <form method="post" action="">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <?php if ($step === 'reset'): ?>
          <input type="hidden" name="action" value="reset">
          <input type="hidden" name="token" value="<?= e($token) ?>">
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <div class="input-wrap">
              <i class="bi bi-lock input-icon"></i>
              <input type="password" name="password" id="passField" class="form-control" placeholder="Min. 6 characters" required minlength="6" autofocus>
              <button type="button" class="toggle-pass" onclick="togglePass('passField',this)"><i class="bi bi-eye"></i></button>
            </div>
          </div>
          <div class="mb-4">
            <label class="form-label">Confirm Password</label>
            <div class="input-wrap">
              <i class="bi bi-lock-fill input-icon"></i>
              <input type="password" name="confirm_password" id="confirmField" class="form-control" placeholder="Repeat password" required minlength="6">
              <button type="button" class="toggle-pass" onclick="togglePass('confirmField',this)"><i class="bi bi-eye"></i></button>
            </div>
          </div>
        <?php else: ?>
          <div class="mb-4">
            <label class="form-label">Email Address</label>
            <div class="input-wrap">
              <i class="bi bi-envelope input-icon"></i>
              <input type="email" name="email" class="form-control" placeholder="you@email.com" required autofocus>
            </div>
          </div>
        <?php endif; ?>
        <button type="submit" class="btn-auth"><?= $step === 'reset' ? 'Reset Password' : 'Send Reset Link' ?> <i class="bi bi-arrow-right ms-1"></i></button>
      </form>
      <div class="back-link"><a href="<?= url('login.php') ?>"><i class="bi bi-arrow-left me-1"></i>Back to Sign In</a></div>
    <?php endif; ?>
  </div>
</div>
<script>
function togglePass(id, btn) {
  const f = document.getElementById(id);
  const show = f.type === 'password';
  f.type = show ? 'text' : 'password';
  btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
}
</script>
</body>
</html>
