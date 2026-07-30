<?php
require_once __DIR__ . '/includes/functions.php';
render_header('Terms & Privacy', 'terms', 'Terms of service and privacy policy for SOUND.');
?>
<div class="container-fluid px-3 px-lg-4 py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="section-title mb-4">Terms &amp; Privacy Policy</h1>
            <div class="card-media p-4 p-lg-5">
                <h3 class="fw-bold text-white mb-3">Terms of Service</h3>
                <p class="text-secondary small">By using SOUND, you agree to these terms:</p>
                <ul class="text-secondary small">
                    <li class="mb-2">SOUND is provided for personal, non-commercial entertainment use.</li>
                    <li class="mb-2">You are responsible for your account and activity.</li>
                    <li class="mb-2">Do not upload copyrighted content you do not own.</li>
                    <li class="mb-2">Admins reserve the right to remove inappropriate content or accounts.</li>
                    <li class="mb-2">The service is provided "as is" without warranty.</li>
                </ul>
                <hr class="border-secondary my-4">
                <h3 class="fw-bold text-white mb-3">Privacy Policy</h3>
                <p class="text-secondary small">We respect your privacy:</p>
                <ul class="text-secondary small">
                    <li class="mb-2">We collect your name, email, and phone for account management only.</li>
                    <li class="mb-2">Passwords are stored as bcrypt hashes — never in plain text.</li>
                    <li class="mb-2">We do not sell or share your data with third parties.</li>
                    <li class="mb-2">Activity logs are kept for security and analytics purposes only.</li>
                    <li class="mb-2">You can request account deletion at any time via the Contact page.</li>
                </ul>
                <hr class="border-secondary my-4">
                <p class="text-secondary small mb-0">Last updated: <?= date('F j, Y') ?></p>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
