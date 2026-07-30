<?php
// Professional 500 error page — can be shown without header if DB is down
$showHeader = function_exists('render_header');
if ($showHeader) {
    render_header('Server Error');
} else {
    // Standalone fallback when database/functions are unavailable
    ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Server Error • SOUND</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
<style>
body{font-family:'Inter',sans-serif;background:#0d0d14;color:#9CA3AF;min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
body::before{content:'';position:absolute;inset:0;background:radial-gradient(600px 400px at 30% 20%,rgba(108,99,255,0.12),transparent 60%),radial-gradient(500px 400px at 70% 80%,rgba(255,77,109,0.08),transparent 60%);pointer-events:none}
.err-box{text-align:center;position:relative;z-index:1}
.err-icon{width:100px;height:100px;border-radius:50%;margin:0 auto 1.5rem;display:flex;align-items:center;justify-content:center;background:rgba(255,77,109,0.15);color:#FF4D6D;font-size:3rem;animation:pulse 2s ease-in-out infinite}
@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}
.err-code{font-family:'Sora',sans-serif;font-weight:800;font-size:5rem;color:#fff;line-height:1;margin-bottom:0.5rem}
.err-title{font-family:'Sora',sans-serif;font-weight:700;font-size:1.5rem;color:#fff;margin-bottom:0.5rem}
.err-desc{font-size:1rem;color:#9CA3AF;margin-bottom:2rem;max-width:400px;margin-left:auto;margin-right:auto}
.err-btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:12px;font-weight:600;font-size:0.95rem;background:linear-gradient(135deg,#6C63FF,#8B5CF6);color:#fff;text-decoration:none;transition:all 0.25s}
.err-btn:hover{transform:translateY(-2px);box-shadow:0 12px 40px -10px rgba(108,99,255,0.6);color:#fff}
</style>
</head>
<body>
<div class="err-box">
<?php } ?>
    <div class="text-center py-5">
        <div class="err-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="err-code">500</div>
        <h1 class="err-title">Oops! Something went wrong</h1>
        <p class="err-desc">An unexpected error occurred on our end. We've been notified and are working to fix it. Please try again later.</p>
        <a href="<?= $showHeader ? url('index.php') : '/' ?>" class="err-btn"><i class="bi bi-house"></i>Back to Home</a>
    </div>
<?php if ($showHeader) { ?>
<?php render_footer(); ?>
<?php } else { ?>
</div>
</body>
</html>
<?php } ?>
