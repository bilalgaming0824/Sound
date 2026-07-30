<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

if (is_logged_in()) { redirect('index.php'); }

$error = '';
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { $error = 'Invalid request.'; }
    else {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($identifier === '' || $password === '') { $error = 'Please enter your username/email and password.'; }
        elseif (!check_login_rate_limit($identifier)) { $error = 'Too many failed attempts. Please try again in 15 minutes.'; }
        else {
            $user = get_user_by_username($identifier);
            if (!$user || !password_verify($password, $user['password'])) {
                record_login_attempt($identifier);
                $error = 'Invalid username/email or password.';
            } else {
                clear_login_attempts($identifier);
                regenerate_session();
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                log_activity((int)$user['id'], 'login', 'User signed in');
                set_flash('success', 'Welcome back, ' . $user['username'] . '!');
                if ($user['role'] === 'admin') {
                    redirect('admin/index.php');
                } else {
                    redirect('index.php');
                }
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
<title>Sign In &bull; <?= e($siteName) ?></title>
<link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<style>
*,:before,:after{box-sizing:border-box}
:root{--primary:#6C63FF;--pink:#FF4D6D;--dark:#0d0d14;--card:#16161f;--border:rgba(255,255,255,.08);--text:#fff;--muted:#9CA3AF}
body{font-family:'Inter',sans-serif;background:var(--dark);color:var(--muted);min-height:100vh;margin:0}
.auth-wrap{display:flex;min-height:100vh}
/* LEFT PANEL */
.auth-left{
  flex:0 0 48%;width:48%;position:relative;overflow:hidden;
  display:flex;flex-direction:column;justify-content:space-between;padding:2.5rem;
  background:linear-gradient(145deg,#0d0d14 0%,#12102a 50%,#0d0d14 100%);
}
.auth-left::before{
  content:'';position:absolute;inset:0;
  background:
    radial-gradient(circle at 20% 20%,rgba(108,99,255,0.35) 0%,transparent 50%),
    radial-gradient(circle at 80% 80%,rgba(255,77,109,0.25) 0%,transparent 50%),
    radial-gradient(circle at 50% 50%,rgba(108,99,255,0.08) 0%,transparent 70%);
  pointer-events:none;
}
.auth-left-logo{display:flex;align-items:center;gap:10px;position:relative;z-index:1}
.brand-icon{
  width:40px;height:40px;border-radius:12px;
  background:linear-gradient(135deg,var(--primary),var(--pink));
  display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.3rem;
  box-shadow:0 0 30px -6px rgba(108,99,255,0.7);
}
.brand-name{font-family:'Sora',sans-serif;font-weight:800;font-size:1.4rem;color:#fff;letter-spacing:0.02em}
.auth-left-hero{position:relative;z-index:1;padding:1rem 0}
.auth-left-hero h2{
  font-family:'Sora',sans-serif;font-weight:800;
  font-size:2.6rem;line-height:1.15;color:#fff;margin-bottom:1.2rem;
}
.auth-left-hero h2 .grad{
  background:linear-gradient(90deg,var(--primary),var(--pink));
  -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
}
.auth-left-hero p{font-size:1rem;line-height:1.6;max-width:380px;margin-bottom:2rem}
.feature-list{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.8rem}
.feature-list li{display:flex;align-items:center;gap:12px;font-size:0.9rem;color:rgba(255,255,255,0.8)}
.feature-list li .fi{
  width:32px;height:32px;border-radius:10px;flex-shrink:0;
  display:flex;align-items:center;justify-content:center;font-size:0.9rem;
}
.fi-purple{background:rgba(108,99,255,0.2);color:var(--primary)}
.fi-pink{background:rgba(255,77,109,0.2);color:var(--pink)}
.fi-green{background:rgba(52,211,153,0.2);color:#34D399}
.fi-blue{background:rgba(59,130,246,0.2);color:#3B82F6}
.auth-left-footer{position:relative;z-index:1}
.auth-left-footer .stats{display:flex;gap:2rem}
.auth-left-footer .stat-val{font-family:'Sora',sans-serif;font-weight:700;font-size:1.4rem;color:#fff}
.auth-left-footer .stat-label{font-size:0.75rem;color:var(--muted);margin-top:2px}

/* FLOATING MUSIC NOTES */
.music-floats{position:absolute;inset:0;pointer-events:none;overflow:hidden;z-index:0}
.music-floats span{
  position:absolute;color:rgba(108,99,255,0.15);font-size:2rem;
  animation:floatUp 8s linear infinite;
}
.music-floats span:nth-child(2){left:20%;animation-delay:2s;font-size:1.4rem;color:rgba(255,77,109,0.12)}
.music-floats span:nth-child(3){left:60%;animation-delay:4s;font-size:2.5rem;color:rgba(108,99,255,0.1)}
.music-floats span:nth-child(4){left:80%;animation-delay:1s;font-size:1.2rem;color:rgba(255,77,109,0.1)}
.music-floats span:nth-child(5){left:40%;animation-delay:6s;font-size:3rem;color:rgba(108,99,255,0.08)}
@keyframes floatUp{0%{transform:translateY(110vh) rotate(0deg);opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translateY(-10vh) rotate(20deg);opacity:0}}

/* RIGHT PANEL */
.auth-right{
  flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;
  padding:2.5rem;background:var(--dark);position:relative;overflow:hidden;
}
.auth-right::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(800px 600px at 100% 0%,rgba(108,99,255,0.05),transparent 60%);
  pointer-events:none;
}
.auth-box{width:100%;max-width:420px;position:relative;z-index:1}
.auth-box-header{text-align:center;margin-bottom:2rem}
.auth-box-header h1{font-family:'Sora',sans-serif;font-weight:800;font-size:2rem;color:#fff;margin-bottom:0.4rem}
.auth-box-header p{font-size:0.9rem;color:var(--muted)}
.auth-card{
  background:var(--card);border:1px solid var(--border);border-radius:20px;
  padding:2rem;
  box-shadow:0 40px 80px -20px rgba(0,0,0,0.6),0 0 0 1px rgba(255,255,255,0.04) inset;
}
.form-label{color:#c0c0cc;font-size:0.85rem;font-weight:500;margin-bottom:6px}
.input-wrap{position:relative}
.input-wrap .input-icon{
  position:absolute;left:14px;top:50%;transform:translateY(-50%);
  color:var(--muted);font-size:1rem;pointer-events:none;z-index:2;
}
.input-wrap .form-control{
  background:#0d0d14;border:1px solid var(--border);color:#fff;
  padding-left:42px;padding-top:12px;padding-bottom:12px;
  border-radius:12px;font-size:0.9rem;transition:border-color 0.2s,box-shadow 0.2s;
}
.input-wrap .form-control:focus{
  background:#0d0d14;border-color:var(--primary);color:#fff;
  box-shadow:0 0 0 3px rgba(108,99,255,0.2);outline:none;
}
.input-wrap .form-control::placeholder{color:rgba(255,255,255,0.25)}
.input-wrap .toggle-pass{
  position:absolute;right:14px;top:50%;transform:translateY(-50%);
  background:none;border:none;color:var(--muted);padding:0;cursor:pointer;
  transition:color 0.2s;font-size:1rem;
}
.input-wrap .toggle-pass:hover{color:#fff}
.divider{display:flex;align-items:center;gap:1rem;margin:1.2rem 0}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
.divider span{font-size:0.78rem;color:var(--muted);white-space:nowrap}
.btn-auth{
  width:100%;padding:12px;border-radius:12px;font-weight:600;font-size:0.95rem;
  background:linear-gradient(135deg,var(--primary),#8B5CF6);border:none;color:#fff;
  transition:all 0.25s;position:relative;overflow:hidden;letter-spacing:0.01em;
}
.btn-auth::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent);
  opacity:0;transition:opacity 0.25s;
}
.btn-auth:hover{transform:translateY(-2px);box-shadow:0 12px 40px -10px rgba(108,99,255,0.6);color:#fff}
.btn-auth:hover::before{opacity:1}
.btn-auth:active{transform:translateY(0)}
.auth-links{text-align:center;margin-top:1.2rem;font-size:0.85rem}
.auth-links a{color:var(--primary);font-weight:500;text-decoration:none}
.auth-links a:hover{text-decoration:underline}
.demo-badge{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(108,99,255,0.1);border:1px solid rgba(108,99,255,0.25);
  border-radius:10px;padding:8px 14px;font-size:0.78rem;margin-top:1rem;width:100%;
  justify-content:center;color:rgba(255,255,255,0.7);
}
.demo-badge code{color:var(--primary);font-weight:600;background:none;font-size:0.78rem}
.alert-auth{
  border-radius:12px;padding:10px 14px;font-size:0.85rem;
  background:rgba(255,77,109,0.1);border:1px solid rgba(255,77,109,0.3);color:#ff8fa3;
  display:flex;align-items:center;gap:8px;margin-bottom:1.2rem;
}
@media(max-width:767px){
  .auth-left{display:none}
  .auth-right{padding:1.5rem}
}
</style>
</head>
<body>
<div class="auth-wrap">

  <!-- LEFT PANEL -->
  <div class="auth-left">
    <div class="music-floats">
      <span><i class="bi bi-music-note"></i></span>
      <span><i class="bi bi-headphones"></i></span>
      <span><i class="bi bi-music-note-beamed"></i></span>
      <span><i class="bi bi-soundwave"></i></span>
      <span><i class="bi bi-vinyl"></i></span>
    </div>
    <div class="auth-left-logo">
      <div class="brand-icon"><i class="bi bi-soundwave"></i></div>
      <span class="brand-name">SOUND</span>
    </div>
    <div class="auth-left-hero">
      <h2>Your music,<br>your <span class="grad">universe.</span></h2>
      <p>Stream thousands of songs and videos, discover new artists, build playlists, and share what you love.</p>
      <ul class="feature-list">
        <li><span class="fi fi-purple"><i class="bi bi-music-note-list"></i></span>Unlimited songs &amp; videos</li>
        <li><span class="fi fi-pink"><i class="bi bi-heart"></i></span>Save your favourites</li>
        <li><span class="fi fi-green"><i class="bi bi-collection-play"></i></span>Create &amp; manage playlists</li>
        <li><span class="fi fi-blue"><i class="bi bi-star"></i></span>Rate &amp; review tracks</li>
      </ul>
    </div>
    <div class="auth-left-footer">
      <div class="stats">
        <div><div class="stat-val">10K+</div><div class="stat-label">Songs &amp; Videos</div></div>
        <div><div class="stat-val">500+</div><div class="stat-label">Artists</div></div>
        <div><div class="stat-val">Free</div><div class="stat-label">Always</div></div>
      </div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="auth-right">
    <div class="auth-box">
      <div class="auth-box-header">
        <h1>Welcome back</h1>
        <p>Sign in to your SOUND account to continue.</p>
      </div>

      <?php if ($error): ?>
        <div class="alert-auth"><i class="bi bi-exclamation-circle-fill"></i><?= e($error) ?></div>
      <?php endif; ?>

      <div class="auth-card">
        <form method="post" action="" id="loginForm">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

          <div class="mb-3">
            <label class="form-label">Username or Email</label>
            <div class="input-wrap">
              <i class="bi bi-person input-icon"></i>
              <input type="text" name="identifier" value="<?= e($identifier) ?>" class="form-control" placeholder="Enter username or email" required autofocus autocomplete="username">
            </div>
          </div>

          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label mb-0">Password</label>
              <a href="<?= url('forgot_password.php') ?>" style="font-size:0.78rem;color:var(--muted)">Forgot password?</a>
            </div>
            <div class="input-wrap">
              <i class="bi bi-lock input-icon"></i>
              <input type="password" name="password" id="passField" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
              <button type="button" class="toggle-pass" onclick="togglePass('passField',this)"><i class="bi bi-eye"></i></button>
            </div>
          </div>

          <button type="submit" class="btn-auth">Sign In <i class="bi bi-arrow-right ms-1"></i></button>
        </form>

        <div class="divider"><span>or</span></div>
        <div class="auth-links">
          Don't have an account? <a href="<?= url('register.php') ?>">Create one free</a>
        </div>
      </div>

      <div class="demo-badge">
        <i class="bi bi-shield-lock" style="color:var(--primary)"></i>
        Demo admin: <code>admin</code> / <code>admin123</code>
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
</script>
</body>
</html>
