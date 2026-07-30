<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

$flash = get_flash();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('contact.php'); }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name === '' || $email === '' || $message === '') { set_flash('danger', 'All fields are required.'); redirect('contact.php'); }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { set_flash('danger', 'Please enter a valid email.'); redirect('contact.php'); }
    $stmt = db()->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?,?,?)");
    $stmt->execute([$name, $email, $message]);
    set_flash('success', 'Thanks for reaching out! We will get back to you soon.');
    redirect('contact.php');
}
render_header('Contact', '');
?>
<div class="container-fluid px-3 px-lg-4 py-5 pb-4" style="max-width:1100px">
    <span class="lm-eyebrow d-inline-flex">WE&apos;D LOVE TO HEAR FROM YOU</span>
    <h1 class="lm-title mt-2 mb-2">Get In <span style="background:linear-gradient(90deg,#a78bfa,#ec4899);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">Touch</span></h1>
    <p class="lm-subtitle mb-4" style="max-width:600px">Have a question, feedback, or need help with something? Drop us a message and our team will respond as soon as possible.</p>

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show"><?= e($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <form method="post" action="" class="card-media p-4 p-lg-5">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person me-1"></i>Your Name *</label>
                    <input name="name" class="form-control form-control-lg" placeholder="Enter your full name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-envelope me-1"></i>Email Address *</label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="you@example.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-chat-dots me-1"></i>Your Message *</label>
                    <textarea name="message" rows="6" class="form-control form-control-lg" placeholder="Tell us how we can help…" required></textarea>
                </div>
                <button class="btn btn-primary btn-lg w-100"><i class="bi bi-send me-2"></i>Send Message</button>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="card-media p-4 mb-3">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="hero-stat-icon" style="width:48px;height:48px;font-size:1.4rem"><i class="bi bi-geo-alt-fill"></i></span>
                    <h3 class="fw-bold text-white mb-0" style="font-family:'Sora',sans-serif">Visit Us</h3>
                </div>
                <p class="text-secondary small mb-0 ps-1">SOUND Entertainment<br>123 Music Avenue<br>Mumbai, India 400001</p>
            </div>
            <div class="card-media p-4 mb-3">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="hero-stat-icon" style="width:48px;height:48px;font-size:1.4rem"><i class="bi bi-envelope-fill"></i></span>
                    <h3 class="fw-bold text-white mb-0" style="font-family:'Sora',sans-serif">Email Us</h3>
                </div>
                <p class="text-secondary small mb-0 ps-1">support@soundentertainment.com<br>info@soundentertainment.com</p>
            </div>
            <div class="card-media p-4 mb-3">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="hero-stat-icon" style="width:48px;height:48px;font-size:1.4rem"><i class="bi bi-clock-fill"></i></span>
                    <h3 class="fw-bold text-white mb-0" style="font-family:'Sora',sans-serif">Business Hours</h3>
                </div>
                <p class="text-secondary small mb-0 ps-1">Monday - Friday: 9am - 6pm<br>Saturday: 10am - 4pm<br>Sunday: Closed</p>
            </div>
            <div class="card-media p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="hero-stat-icon" style="width:48px;height:48px;font-size:1.4rem"><i class="bi bi-telephone-fill"></i></span>
                    <h3 class="fw-bold text-white mb-0" style="font-family:'Sora',sans-serif">Call Us</h3>
                </div>
                <p class="text-secondary small mb-0 ps-1">+91 98765 43210<br>+91 98765 43211</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="nav-icon-btn" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="nav-icon-btn" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="nav-icon-btn" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="nav-icon-btn" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
