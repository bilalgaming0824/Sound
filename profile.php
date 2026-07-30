<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/models.php';

if (!is_logged_in()) { redirect('login.php'); }

$user_id = (int)$_SESSION['user_id'];
$user = get_user_by_id($user_id);

$errors = [];
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request.';
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $avatar = trim($_POST['avatar_url'] ?? '');
        $new_pass = trim($_POST['new_password'] ?? '');

        if ($full_name === '') $errors[] = 'Full name is required.';
        if ($address === '') $errors[] = 'Address is required.';
        if ($phone === '') $errors[] = 'Phone is required.';
        if (!preg_match('/^[0-9+\-\s()]{7,16}$/', $phone)) $errors[] = 'Enter a valid phone number.';
        if ($new_pass !== '' && strlen($new_pass) < 6) $errors[] = 'New password must be at least 6 characters.';

        if (empty($errors)) {
            $avatar_url = $avatar ?: null;
            if ($new_pass !== '') {
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                db()->prepare("UPDATE users SET full_name=?, address=?, phone=?, bio=?, avatar_url=?, password=? WHERE id=?")
                    ->execute([$full_name, $address, $phone, $bio, $avatar_url, $hash, $user_id]);
            } else {
                db()->prepare("UPDATE users SET full_name=?, address=?, phone=?, bio=?, avatar_url=? WHERE id=?")
                    ->execute([$full_name, $address, $phone, $bio, $avatar_url, $user_id]);
            }
            $_SESSION['full_name'] = $full_name;
            set_flash('success', 'Profile updated successfully.');
            redirect('profile.php');
        }
    }
}

$fav_count = count_user_favourites($user_id);
$playlists = get_playlists($user_id);

render_header('My Profile');
?>

<div class="container-fluid px-3 px-lg-4 py-4">

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="profile-cover rounded-top-4 position-relative overflow-hidden mb-0">
        <div class="profile-cover-gradient position-absolute inset-0"></div>
    </div>

    <div class="card-media px-4 pb-4 pt-0 rounded-bottom-4 border-top-0">

        <div class="d-flex flex-column flex-md-row align-items-md-end gap-3" style="margin-top:-60px">
            <div class="position-relative flex-shrink-0">
                <?php if (!empty($user['avatar_url'])): ?>
                    <img src="<?= e($user['avatar_url']) ?>" alt="<?= e($user['username']) ?>"
                         class="rounded-4 shadow-lg"
                         style="width:120px;height:120px;object-fit:cover;border:4px solid var(--ink-900)">
                <?php else: ?>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-lg fw-bold"
                         style="width:120px;height:120px;font-size:3rem;background:var(--gradient);border:4px solid var(--ink-900)">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex-grow-1 pb-2">
                <h1 class="fw-bold text-white mb-1"><?= e($user['username']) ?></h1>
                <p class="text-secondary mb-2"><?= e($user['email']) ?></p>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($user['role'] === 'admin'): ?>
                        <span class="chip chip-brand"><i class="bi bi-shield-lock"></i> Administrator</span>
                    <?php else: ?>
                        <span class="chip"><i class="bi bi-person"></i> Member</span>
                    <?php endif; ?>
                    <span class="chip"><i class="bi bi-heart"></i> <?= $fav_count ?> Favourites</span>
                    <span class="chip"><i class="bi bi-music-note-list"></i> <?= count($playlists) ?> Playlists</span>
                    <span class="chip"><i class="bi bi-calendar3"></i> Joined <?= format_date($user['created_at']) ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($user['bio'])): ?>
            <p class="text-secondary mt-3 mb-0" style="max-width:600px"><?= e($user['bio']) ?></p>
        <?php endif; ?>
    </div>

    <div class="card-media p-4 mt-4">
        <h2 class="fw-bold text-white mb-1"><i class="bi bi-pencil-square"></i> Edit Profile</h2>
        <p class="text-secondary small mb-4">Update your personal information, avatar, and password.</p>
        <form method="post" action="">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" value="<?= e($user['full_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="<?= e($user['phone']) ?>" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="<?= e($user['address']) ?>" class="form-control" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Avatar URL</label>
                    <input type="text" name="avatar_url" value="<?= e($user['avatar_url'] ?? '') ?>" class="form-control" placeholder="https://… (leave blank for letter avatar)">
                    <small class="text-secondary">Paste an image URL to use as your profile picture.</small>
                </div>
                <div class="col-12">
                    <label class="form-label">Bio</label>
                    <textarea name="bio" rows="3" class="form-control" placeholder="Tell us about yourself…"><?= e($user['bio'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current">
                    <small class="text-secondary">Minimum 6 characters.</small>
                </div>
            </div>
            <button class="btn btn-primary mt-4 px-4"><i class="bi bi-save"></i> Save Changes</button>
        </form>
    </div>
</div>

<style>
.profile-cover {
    height: 220px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%);
}
.profile-cover::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 20% 50%, rgba(108,99,255,0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 30%, rgba(255,77,109,0.12) 0%, transparent 50%);
}
.profile-cover-gradient {
    background: linear-gradient(to bottom, transparent 0%, rgba(7,7,13,0.4) 100%);
}
</style>

<?php render_footer(); ?>
