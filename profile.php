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

    <div class="card-media p-0 rounded-4 overflow-hidden mb-0 profile-hero-card">

        <div class="profile-cover position-relative overflow-hidden">
            <div class="profile-cover-gradient position-absolute" style="inset:0"></div>
        </div>

        <div class="px-4 pb-4 pt-0">

        <div class="d-flex flex-column flex-md-row align-items-md-end gap-3" style="margin-top:-60px">
            <div class="position-relative flex-shrink-0">
                <?php if (!empty($user['avatar_url'])): ?>
                    <img src="<?= e(media_url($user['avatar_url'] ?? null)) ?>" alt="<?= e($user['username']) ?>"
                         class="rounded-4 shadow-lg"
                         style="width:120px;height:120px;object-fit:cover;border:4px solid var(--dark)">
                <?php else: ?>
                    <div class="rounded-4 d-flex align-items-center justify-content-center shadow-lg fw-bold"
                         style="width:120px;height:120px;font-size:3rem;background:linear-gradient(135deg,var(--primary),var(--pink));border:4px solid var(--dark)">
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

            <div class="pb-2">
                <button class="btn btn-primary" id="editProfileBtn" onclick="document.getElementById('editProfileForm').style.display='block';this.style.display='none'">
                    <i class="bi bi-pencil-square"></i> Edit Profile
                </button>
            </div>
        </div>

        <?php if (!empty($user['bio'])): ?>
            <p class="text-secondary mt-3 mb-0" style="max-width:600px"><?= e($user['bio']) ?></p>
        <?php endif; ?>
        </div>
    </div>
    </div>

    <!-- Profile Info Display -->
    <div class="card-media p-4 mt-4">
        <h2 class="fw-bold text-white mb-4"><i class="bi bi-info-circle"></i> My Information</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="profile-info-item">
                    <div class="profile-info-icon"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <p class="profile-info-label">Full Name</p>
                        <p class="profile-info-value"><?= e($user['full_name']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-info-item">
                    <div class="profile-info-icon"><i class="bi bi-envelope"></i></div>
                    <div>
                        <p class="profile-info-label">Email Address</p>
                        <p class="profile-info-value"><?= e($user['email']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-info-item">
                    <div class="profile-info-icon"><i class="bi bi-telephone"></i></div>
                    <div>
                        <p class="profile-info-label">Phone Number</p>
                        <p class="profile-info-value"><?= e($user['phone']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-info-item">
                    <div class="profile-info-icon"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <p class="profile-info-label">Address</p>
                        <p class="profile-info-value"><?= e($user['address']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-info-item">
                    <div class="profile-info-icon"><i class="bi bi-person-check"></i></div>
                    <div>
                        <p class="profile-info-label">Username</p>
                        <p class="profile-info-value"><?= e($user['username']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-info-item">
                    <div class="profile-info-icon"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <p class="profile-info-label">Member Since</p>
                        <p class="profile-info-value"><?= format_date($user['created_at']) ?></p>
                    </div>
                </div>
            </div>
            <?php if (!empty($user['bio'])): ?>
            <div class="col-12">
                <div class="profile-info-item">
                    <div class="profile-info-icon"><i class="bi bi-chat-left-text"></i></div>
                    <div>
                        <p class="profile-info-label">Bio</p>
                        <p class="profile-info-value"><?= e($user['bio']) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Profile Form (hidden by default) -->
    <div class="card-media p-4 mt-4" id="editProfileForm" style="display:none">
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
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary px-4"><i class="bi bi-save"></i> Save Changes</button>
                <button type="button" class="btn btn-ghost px-4" onclick="document.getElementById('editProfileForm').style.display='none';document.getElementById('editProfileBtn').style.display='inline-block'"><i class="bi bi-x-circle"></i> Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
.profile-cover {
    height: 220px;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%);
    overflow: hidden;
}
.profile-cover::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 20% 50%, rgba(108,99,255,0.25) 0%, transparent 50%),
        radial-gradient(circle at 80% 30%, rgba(255,77,109,0.20) 0%, transparent 50%);
}
.profile-cover::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill='none' stroke='rgba(255,255,255,0.04)' stroke-width='1'%3E%3Cpath d='M0 30 Q15 15 30 30 T60 30'/%3E%3Cpath d='M0 40 Q15 25 30 40 T60 40'/%3E%3Cpath d='M0 20 Q15 5 30 20 T60 20'/%3E%3C/g%3E%3C/svg%3E");
    background-size: 120px 120px;
}
.profile-cover-gradient {
    background: linear-gradient(to bottom, transparent 0%, rgba(7,7,13,0.6) 100%);
}
.profile-info-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px;
    border-radius: 12px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    transition: all 0.2s ease;
}
.profile-info-item:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.1);
}
.profile-info-icon {
    width: 42px;
    height: 42px;
    min-width: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    background: linear-gradient(135deg, rgba(108,99,255,0.2), rgba(255,77,109,0.15));
    color: #a5b4fc;
}
.profile-info-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #8b93a7;
    margin: 0 0 2px 0;
}
.profile-info-value {
    font-size: 0.95rem;
    color: #fff;
    font-weight: 500;
    margin: 0;
    word-break: break-word;
}
</style>

<?php render_footer(); ?>
