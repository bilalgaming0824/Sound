<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

$flash = get_flash();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('contact.php'); }
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name === '' || $email === '' || $message === '') { set_flash('danger', 'All fields are required.'); redirect('contact.php'); }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { set_flash('danger', 'Please enter a valid email address.'); redirect('contact.php'); }
    $stmt = db()->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?,?,?)");
    $stmt->execute([$name, $email, $message]);
    set_flash('success', 'Thanks for reaching out! We will get back to you soon.');
    redirect('contact.php');
}
render_header('Contact', '');
?>
<style>
.contact-wrap {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
}
@media (min-width: 992px) {
    .contact-wrap { grid-template-columns: 7fr 5fr; }
}
.contact-info-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 22px;
    margin-bottom: 16px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
}
.contact-info-card:last-child { margin-bottom: 0; }
.cic-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 48px; height: 48px; border-radius: 14px; flex-shrink: 0;
    background: linear-gradient(135deg, rgba(108,99,255,0.25), rgba(168,85,247,0.15));
    border: 1px solid rgba(108,99,255,0.3);
    color: #a78bfa; font-size: 1.25rem;
}
.cic-body { flex: 1; min-width: 0; }
.cic-title { font-family: 'Sora', sans-serif; font-size: 0.95rem; font-weight: 700; color: #fff; margin: 0 0 4px; }
.cic-text { font-size: 0.83rem; color: var(--text-light); line-height: 1.6; margin: 0; }
</style>

<div class="container-fluid px-3 px-lg-4 py-5" style="max-width:1200px;margin:0 auto;">
    <span class="lm-eyebrow d-inline-flex mb-2">WE&apos;D LOVE TO HEAR FROM YOU</span>
    <h1 class="lm-title mb-2">Get In <span style="background:linear-gradient(90deg,#a78bfa,#ec4899);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">Touch</span></h1>
    <p class="lm-subtitle mb-4" style="max-width:600px">Have a question, feedback, or need help? Drop us a message and our team will respond as soon as possible.</p>

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show mb-4"><?= e($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="contact-wrap">
        <!-- Contact Form -->
        <div>
            <div class="card-media p-4 p-lg-5">
                <h2 class="fw-bold text-white mb-1" style="font-family:'Sora',sans-serif;font-size:1.35rem">Send a Message</h2>
                <p class="text-secondary small mb-4">We read every message and reply within 1-2 business days.</p>
                <form method="post" action="">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="mb-3">
                        <label class="form-label fw-500"><i class="bi bi-person me-1 text-brand"></i>Your Name *</label>
                        <input name="name" class="form-control form-control-lg" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500"><i class="bi bi-envelope me-1 text-brand"></i>Email Address *</label>
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="you@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-500"><i class="bi bi-chat-dots me-1 text-brand"></i>Your Message *</label>
                        <textarea name="message" rows="6" class="form-control form-control-lg" placeholder="Tell us how we can help…" required></textarea>
                    </div>
                    <button class="btn btn-primary btn-lg w-100"><i class="bi bi-send me-2"></i>Send Message</button>
                </form>
            </div>
        </div>

        <!-- Info Cards -->
        <div>
            <div class="contact-info-card">
                <span class="cic-icon"><i class="bi bi-geo-alt-fill"></i></span>
                <div class="cic-body">
                    <p class="cic-title">Visit Us</p>
                    <p class="cic-text">SOUND Entertainment<br>123 Music Avenue<br>Mumbai, India 400001</p>
                </div>
            </div>
            <div class="contact-info-card">
                <span class="cic-icon"><i class="bi bi-envelope-fill"></i></span>
                <div class="cic-body">
                    <p class="cic-title">Email Us</p>
                    <p class="cic-text">support@soundentertainment.com<br>info@soundentertainment.com</p>
                </div>
            </div>
            <div class="contact-info-card">
                <span class="cic-icon"><i class="bi bi-clock-fill"></i></span>
                <div class="cic-body">
                    <p class="cic-title">Business Hours</p>
                    <p class="cic-text">Monday – Friday: 9 am – 6 pm<br>Saturday: 10 am – 4 pm<br>Sunday: Closed</p>
                </div>
            </div>
            <div class="contact-info-card">
                <span class="cic-icon"><i class="bi bi-telephone-fill"></i></span>
                <div class="cic-body">
                    <p class="cic-title">Call Us</p>
                    <p class="cic-text">+91 98765 43210<br>+91 98765 43211</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="nav-icon-btn" style="width:36px;height:36px;border-radius:10px" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="nav-icon-btn" style="width:36px;height:36px;border-radius:10px" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="nav-icon-btn" style="width:36px;height:36px;border-radius:10px" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="nav-icon-btn" style="width:36px;height:36px;border-radius:10px" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
