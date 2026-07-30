<?php
require_once __DIR__ . '/includes/functions.php';
render_header('Page Not Found');
?>
<div class="container-fluid px-3 px-lg-4 py-5">
    <div class="text-center py-5">
        <div class="d-inline-flex align-items-center justify-content-center mb-4" style="width:100px;height:100px;border-radius:50%;background:rgba(108,99,255,0.15);color:var(--primary);font-size:3rem;animation:pulse 2s ease-in-out infinite">
            <i class="bi bi-compass"></i>
        </div>
        <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:5rem;color:#fff;line-height:1;margin-bottom:0.5rem">404</div>
        <h1 style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.5rem;color:#fff;margin-bottom:0.5rem">Page Not Found</h1>
        <p class="lead text-secondary mb-4" style="max-width:400px;margin-left:auto;margin-right:auto">The page you're looking for doesn't exist or has been moved. Let's get you back on track.</p>
        <a href="<?= url('index.php') ?>" class="btn btn-primary btn-lg"><i class="bi bi-house me-2"></i>Back to Home</a>
    </div>
</div>
<?php render_footer(); ?>
