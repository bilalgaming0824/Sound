<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) { redirect('index.php'); }

$errors = [];
$form = ['username' => '', 'full_name' => '', 'address' => '', 'phone' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { $errors[] = 'Invalid request.'; }
    else {
        $form = [
            'username' => trim($_POST['username'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
        ];
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        if ($form['username'] === '') $errors[] = 'Username is required.';
        elseif (strlen($form['username']) < 3) $errors[] = 'Username must be at least 3 characters.';
        elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $form['username'])) $errors[] = 'Username: letters, numbers and underscores only.';
        if ($form['full_name'] === '') $errors[] = 'Full name is required.';
        if ($form['address'] === '') $errors[] = 'Address is required.';
        if ($form['phone'] === '') $errors[] = 'Phone number is required.';
        elseif (!preg_match('/^[0-9+\-\s()]{7,16}$/', $form['phone'])) $errors[] = 'Enter a valid phone number.';
        if ($form['email'] === '') $errors[] = 'Email is required.';
        elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
        if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';
        if (empty($errors)) {
            $stmt = db()->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$form['username'], $form['email']]);
            if ($stmt->fetch()) { $errors[] = 'Username or email already exists.'; }
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = db()->prepare("INSERT INTO users (username, full_name, address, phone, email, password, role, email_verified) VALUES (?,?,?,?,?,?,'user',0)");
                $stmt->execute([$form['username'], $form['full_name'], $form['address'], $form['phone'], $form['email'], $hash]);
                $userId = (int)db()->lastInsertId();

                // Generate email verification token
                $vToken = bin2hex(random_bytes(32));
                $vExpires = date('Y-m-d H:i:s', time() + 86400); // 24 hours
                db()->prepare("INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?,?,?)")->execute([$userId, $vToken, $vExpires]);

                // Build verification link
                $baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL;
                $verifyLink = $baseUrl . '/verify_email.php?token=' . $vToken;

                // Send verification email
                $subject = 'SOUND — Verify Your Email';
                $body = "Hi " . $form['username'] . ",\n\nWelcome to SOUND! Please verify your email by clicking the link below:\n\n" . $verifyLink . "\n\nThis link expires in 24 hours.\n\n— SOUND Team";
                $headers = "From: noreply@sound.test\r\n";
                @mail($form['email'], $subject, $body, $headers);

                log_activity($userId, 'register', 'New user registered');
                // Auto-login the user immediately after signup
                regenerate_session();
                $_SESSION['user_id'] = (int)$userId;
                $_SESSION['username'] = $form['username'];
                $_SESSION['full_name'] = $form['full_name'];
                $_SESSION['email'] = $form['email'];
                $_SESSION['user_role'] = 'user';
                set_flash('success', 'Welcome to SOUND, ' . $form['username'] . '! Your account is ready.');
                redirect('index.php');
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
<title>Create Account &bull; <?= e($siteName) ?></title>
<link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<style>
*,:before,:after{box-sizing:border-box}
:root{--primary:#6C63FF;--pink:#FF4D6D;--dark:#0d0d14;--card:#16161f;--border:rgba(255,255,255,.08);--text:#fff;--muted:#9CA3AF}
body{font-family:'Inter',sans-serif;background:var(--dark);color:var(--muted);min-height:100vh;margin:0}
.auth-wrap{display:flex;min-height:100vh}
.auth-left{
  flex:0 0 42%;width:42%;position:relative;overflow:hidden;
  display:flex;flex-direction:column;justify-content:space-between;padding:2.5rem;
  background:linear-gradient(145deg,#0d0d14 0%,#12102a 50%,#0d0d14 100%);
}
.auth-left::before{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(circle at 30% 20%,rgba(108,99,255,0.3) 0%,transparent 50%),
    radial-gradient(circle at 70% 85%,rgba(255,77,109,0.22) 0%,transparent 50%);
  pointer-events:none;
}
.auth-left-logo{display:flex;align-items:center;gap:10px;position:relative;z-index:1}
.brand-icon{
  width:40px;height:40px;border-radius:12px;
  background:linear-gradient(135deg,var(--primary),var(--pink));
  display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.3rem;
  box-shadow:0 0 30px -6px rgba(108,99,255,0.7);
}
.brand-name{font-family:'Sora',sans-serif;font-weight:800;font-size:1.4rem;color:#fff}
.auth-left-hero{position:relative;z-index:1}
.auth-left-hero h2{font-family:'Sora',sans-serif;font-weight:800;font-size:2.2rem;line-height:1.2;color:#fff;margin-bottom:1rem}
.auth-left-hero h2 .grad{background:linear-gradient(90deg,var(--primary),var(--pink));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
.steps{display:flex;flex-direction:column;gap:1.2rem;margin-top:1.5rem}
.step{display:flex;gap:14px;align-items:flex-start}
.step-num{
  width:32px;height:32px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--primary),#8B5CF6);
  display:flex;align-items:center;justify-content:center;
  font-family:'Sora',sans-serif;font-size:0.8rem;font-weight:700;color:#fff;
}
.step-text{padding-top:4px}
.step-text strong{color:#fff;font-size:0.9rem}
.step-text p{margin:2px 0 0;font-size:0.78rem;color:var(--muted);line-height:1.4}
.auth-left-footer{position:relative;z-index:1}
.trust-badges{display:flex;gap:1rem;flex-wrap:wrap}
.trust-badge{
  display:flex;align-items:center;gap:6px;font-size:0.75rem;color:rgba(255,255,255,0.6);
  background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:8px;padding:6px 10px;
}
.music-floats{position:absolute;inset:0;pointer-events:none;overflow:hidden;z-index:0}
.music-floats span{position:absolute;animation:floatUp 10s linear infinite}
.music-floats span:nth-child(1){left:10%;color:rgba(108,99,255,0.12);font-size:1.8rem}
.music-floats span:nth-child(2){left:35%;animation-delay:3s;color:rgba(255,77,109,0.1);font-size:1.3rem}
.music-floats span:nth-child(3){left:65%;animation-delay:6s;color:rgba(108,99,255,0.09);font-size:2.2rem}
.music-floats span:nth-child(4){left:85%;animation-delay:1.5s;color:rgba(255,77,109,0.08);font-size:1rem}
@keyframes floatUp{0%{transform:translateY(110vh);opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translateY(-10vh);opacity:0}}

.auth-right{
  flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;
  padding:2rem 2.5rem;background:var(--dark);position:relative;overflow-y:auto;
}
.auth-right::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(700px 500px at 100% 100%,rgba(108,99,255,0.06),transparent 60%);
  pointer-events:none;
}
.auth-box{width:100%;max-width:520px;position:relative;z-index:1;padding:1rem 0}
.auth-box-header{text-align:center;margin-bottom:1.5rem}
.auth-box-header h1{font-family:'Sora',sans-serif;font-weight:800;font-size:1.85rem;color:#fff;margin-bottom:0.3rem}
.auth-box-header p{font-size:0.88rem;color:var(--muted)}
.auth-card{
  background:var(--card);border:1px solid var(--border);border-radius:20px;padding:1.8rem;
  box-shadow:0 40px 80px -20px rgba(0,0,0,0.6),0 0 0 1px rgba(255,255,255,0.04) inset;
}
.form-label{color:#c0c0cc;font-size:0.82rem;font-weight:500;margin-bottom:5px}
.input-wrap{position:relative}
.input-wrap .input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:0.95rem;pointer-events:none;z-index:2}
.input-wrap .form-control{
  background:#0d0d14;border:1px solid var(--border);color:#fff;
  padding:10px 14px 10px 38px;border-radius:11px;font-size:0.88rem;
  transition:border-color 0.2s,box-shadow 0.2s;
}
.input-wrap .form-control:focus{background:#0d0d14;border-color:var(--primary);color:#fff;box-shadow:0 0 0 3px rgba(108,99,255,0.18);outline:none}
.input-wrap .form-control::placeholder{color:rgba(255,255,255,0.22)}
.input-wrap .toggle-pass{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);padding:0;cursor:pointer;transition:color 0.2s;font-size:0.95rem}
.input-wrap .toggle-pass:hover{color:#fff}
.section-label{font-size:0.72rem;font-weight:600;color:var(--primary);letter-spacing:0.08em;text-transform:uppercase;margin-bottom:0.75rem;padding-bottom:0.5rem;border-bottom:1px solid var(--border)}
.password-strength{height:3px;border-radius:2px;margin-top:6px;background:var(--border);overflow:hidden;transition:all 0.3s}
.password-strength-bar{height:100%;border-radius:2px;width:0%;transition:all 0.4s ease}
.strength-text{font-size:0.72rem;margin-top:4px}
.btn-auth{
  width:100%;padding:11px;border-radius:12px;font-weight:600;font-size:0.92rem;
  background:linear-gradient(135deg,var(--primary),#8B5CF6);border:none;color:#fff;
  transition:all 0.25s;letter-spacing:0.01em;
}
.btn-auth:hover{transform:translateY(-2px);box-shadow:0 12px 40px -10px rgba(108,99,255,0.6);color:#fff}
.btn-auth:active{transform:translateY(0)}
.auth-links{text-align:center;margin-top:1rem;font-size:0.83rem}
.auth-links a{color:var(--primary);font-weight:500;text-decoration:none}
.auth-links a:hover{text-decoration:underline}
.alert-auth{border-radius:12px;padding:10px 14px;font-size:0.83rem;background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.3);color:#ff8fa3;display:flex;align-items:flex-start;gap:8px;margin-bottom:1.2rem}
.alert-auth ul{margin:0;padding-left:1rem}
@media(max-width:767px){.auth-left{display:none}.auth-right{padding:1.5rem}}
</style>
</head>
<body>
<div class="auth-wrap">

  <!-- LEFT PANEL -->
  <div class="auth-left">
    <div class="music-floats">
      <span><i class="bi bi-music-note"></i></span>
      <span><i class="bi bi-vinyl"></i></span>
      <span><i class="bi bi-music-note-beamed"></i></span>
      <span><i class="bi bi-headphones"></i></span>
    </div>
    <div class="auth-left-logo">
      <div class="brand-icon"><i class="bi bi-soundwave"></i></div>
      <span class="brand-name">SOUND</span>
    </div>
    <div class="auth-left-hero">
      <h2>Join the <span class="grad">SOUND</span> community.</h2>
      <p style="font-size:0.9rem;line-height:1.6;max-width:320px;margin-top:0.5rem">Create your free account in seconds and start exploring thousands of songs and videos.</p>
      <div class="steps">
        <div class="step"><div class="step-num">1</div><div class="step-text"><strong>Create your account</strong><p>Choose a username and set your password.</p></div></div>
        <div class="step"><div class="step-num">2</div><div class="step-text"><strong>Explore &amp; discover</strong><p>Browse music, videos, albums and artists.</p></div></div>
        <div class="step"><div class="step-num">3</div><div class="step-text"><strong>Build your library</strong><p>Favourite tracks, create playlists, rate songs.</p></div></div>
      </div>
    </div>
    <div class="auth-left-footer">
      <div class="trust-badges">
        <div class="trust-badge"><i class="bi bi-shield-check" style="color:#34D399"></i> Secure</div>
        <div class="trust-badge"><i class="bi bi-gift" style="color:var(--primary)"></i> Free forever</div>
        <div class="trust-badge"><i class="bi bi-lock" style="color:var(--pink)"></i> Private</div>
      </div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="auth-right">
    <div class="auth-box">
      <div class="auth-box-header">
        <div class="brand-icon mx-auto mb-2" style="width:44px;height:44px"><i class="bi bi-soundwave" style="font-size:1.3rem"></i></div>
        <h1>Create your account</h1>
        <p>Free forever &mdash; no credit card required.</p>
      </div>

      <?php if ($errors): ?>
        <div class="alert-auth">
          <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-1"></i>
          <?php if (count($errors) === 1): ?>
            <?= e($errors[0]) ?>
          <?php else: ?>
            <ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="auth-card">
        <form method="post" action="" id="regForm">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

          <div class="section-label"><i class="bi bi-person-badge me-1"></i>Account Info</div>
          <div class="row g-2 mb-3">
            <div class="col-sm-6">
              <label class="form-label">Username <span style="color:var(--pink)">*</span></label>
              <div class="input-wrap">
                <i class="bi bi-at input-icon"></i>
                <input type="text" name="username" value="<?= e($form['username']) ?>" class="form-control" placeholder="e.g. john_doe" required autocomplete="username">
              </div>
              <div style="font-size:0.7rem;color:var(--muted);margin-top:4px">Letters, numbers, underscores</div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Full Name <span style="color:var(--pink)">*</span></label>
              <div class="input-wrap">
                <i class="bi bi-person input-icon"></i>
                <input type="text" name="full_name" value="<?= e($form['full_name']) ?>" class="form-control" placeholder="Your full name" required>
              </div>
            </div>
          </div>

          <div class="section-label mt-2"><i class="bi bi-envelope me-1"></i>Contact Details</div>
          <div class="row g-2 mb-3">
            <div class="col-12">
              <label class="form-label">Address <span style="color:var(--pink)">*</span></label>
              <div class="input-wrap">
                <i class="bi bi-geo-alt input-icon"></i>
                <input type="text" name="address" value="<?= e($form['address']) ?>" class="form-control" placeholder="Street, City, Country" required>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Phone <span style="color:var(--pink)">*</span></label>
              <div class="input-wrap">
                <i class="bi bi-telephone input-icon"></i>
                <input type="text" name="phone" value="<?= e($form['phone']) ?>" class="form-control" placeholder="+92 300 0000000" required>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Email <span style="color:var(--pink)">*</span></label>
              <div class="input-wrap">
                <i class="bi bi-envelope input-icon"></i>
                <input type="email" name="email" value="<?= e($form['email']) ?>" class="form-control" placeholder="you@email.com" required autocomplete="email">
              </div>
            </div>
          </div>

          <div class="section-label mt-2"><i class="bi bi-lock me-1"></i>Security</div>
          <div class="row g-2 mb-3">
            <div class="col-sm-6">
              <label class="form-label">Password <span style="color:var(--pink)">*</span></label>
              <div class="input-wrap">
                <i class="bi bi-lock input-icon"></i>
                <input type="password" name="password" id="passField" class="form-control" placeholder="Min. 6 characters" required minlength="6" autocomplete="new-password" oninput="checkStrength(this.value)">
                <button type="button" class="toggle-pass" onclick="togglePass('passField',this)"><i class="bi bi-eye"></i></button>
              </div>
              <div class="password-strength"><div class="password-strength-bar" id="strengthBar"></div></div>
              <div class="strength-text" id="strengthText" style="color:var(--muted)"></div>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Confirm Password <span style="color:var(--pink)">*</span></label>
              <div class="input-wrap">
                <i class="bi bi-lock-fill input-icon"></i>
                <input type="password" name="confirm" id="confirmField" class="form-control" placeholder="Repeat password" required autocomplete="new-password">
                <button type="button" class="toggle-pass" onclick="togglePass('confirmField',this)"><i class="bi bi-eye"></i></button>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-auth mt-1">Create Account <i class="bi bi-arrow-right ms-1"></i></button>
        </form>

        <div class="auth-links mt-3">
          Already have an account? <a href="<?= url('login.php') ?>">Sign in</a>
        </div>
      </div>

      <p class="text-center mt-3" style="font-size:0.78rem;color:rgba(255,255,255,0.3)">
        <a href="<?= url('index.php') ?>" style="color:rgba(255,255,255,0.35);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to SOUND</a>
      </p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(id, btn) {
  const f = document.getElementById(id);
  const show = f.type === 'password';
  f.type = show ? 'text' : 'password';
  btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
}
function checkStrength(val) {
  const bar = document.getElementById('strengthBar');
  const txt = document.getElementById('strengthText');
  let score = 0;
  if (val.length >= 6) score++;
  if (val.length >= 10) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const levels = [
    {w:'0%',c:'transparent',t:''},
    {w:'25%',c:'#FF4D6D',t:'Weak'},
    {w:'50%',c:'#F59E0B',t:'Fair'},
    {w:'75%',c:'#3B82F6',t:'Good'},
    {w:'100%',c:'#34D399',t:'Strong'},
  ];
  const l = levels[Math.min(score, 4)];
  bar.style.width = l.w; bar.style.background = l.c;
  txt.textContent = l.t; txt.style.color = l.c;
}
</script>
</body>
</html>
