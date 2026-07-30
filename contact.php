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
<div class="container py-5 pb-4" style="max-width:900px">
    <span class="chip chip-brand mb-3"><i class="bi bi-envelope"></i> Get in touch</span>
    <h1 class="hero-title mb-3">Contact Us</h1>
    <p class="text-secondary mb-4" style="max-width:600px">Have a question, feedback, or need help? Send us a message and we'll respond as soon as possible.</p>

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show"><?= e($flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <form method="post" action="" class="card-media p-4">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <div class="mb-3"><label class="form-label">Your Name *</label><input name="name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Message *</label><textarea name="message" rows="5" class="form-control" required></textarea></div>
                <button class="btn btn-primary"><i class="bi bi-send me-2"></i>Send Message</button>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="card-media p-4 mb-3">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-geo-alt text-brand me-2"></i>Address</h3>
                <p class="text-secondary small mb-0">SOUND Entertainment<br>123 Music Avenue<br>Mumbai, India 400001</p>
            </div>
            <div class="card-media p-4 mb-3">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-envelope text-brand me-2"></i>Email</h3>
                <p class="text-secondary small mb-0">support@soundentertainment.com<br>info@soundentertainment.com</p>
            </div>
            <div class="card-media p-4">
                <h3 class="fw-bold text-white mb-3"><i class="bi bi-clock text-brand me-2"></i>Hours</h3>
                <p class="text-secondary small mb-0">Monday - Friday: 9am - 6pm<br>Saturday: 10am - 4pm<br>Sunday: Closed</p>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
