<?php
$pageTitle = 'Manage Categories';
$section = 'categories';
require_once __DIR__ . '/includes/header.php';
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('admin/categories.php'); }
    $action = $_POST['action'] ?? '';
    if ($action === 'add_genre') {
        db()->prepare("INSERT IGNORE INTO genres (name) VALUES (?)")->execute([trim($_POST['name'])]);
        set_flash('success', 'Genre added.');
    } elseif ($action === 'delete_genre') {
        db()->prepare("DELETE FROM genres WHERE id = ?")->execute([(int)$_POST['id']]);
        set_flash('success', 'Genre deleted.');
    } elseif ($action === 'add_language') {
        db()->prepare("INSERT IGNORE INTO languages (name) VALUES (?)")->execute([trim($_POST['name'])]);
        set_flash('success', 'Language added.');
    } elseif ($action === 'delete_language') {
        db()->prepare("DELETE FROM languages WHERE id = ?")->execute([(int)$_POST['id']]);
        set_flash('success', 'Language deleted.');
    }
    redirect('admin/categories.php');
}
$genres = get_genres();
$languages = get_languages();
?>
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-media p-4">
            <h3 class="fw-bold text-white mb-3">Genres</h3>
            <form method="post" action="" class="d-flex gap-2 mb-3">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="add_genre">
                <input name="name" class="form-control" placeholder="Genre name" required>
                <button class="btn btn-primary"><i class="bi bi-plus-lg"></i></button>
            </form>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($genres as $g): ?>
                    <span class="chip" style="background:var(--card);border:1px solid var(--border)">
                        <?= e($g['name']) ?> (<?= $g['song_count'] ?>)
                        <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this genre?')">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="delete_genre">
                            <input type="hidden" name="id" value="<?= $g['id'] ?>">
                            <button class="btn btn-sm text-danger p-0 lh-1"><i class="bi bi-x"></i></button>
                        </form>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-media p-4">
            <h3 class="fw-bold text-white mb-3">Languages</h3>
            <form method="post" action="" class="d-flex gap-2 mb-3">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="action" value="add_language">
                <input name="name" class="form-control" placeholder="Language name" required>
                <button class="btn btn-primary"><i class="bi bi-plus-lg"></i></button>
            </form>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($languages as $l): ?>
                    <span class="chip" style="background:var(--card);border:1px solid var(--border)">
                        <?= e($l['name']) ?>
                        <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this language?')">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="delete_language">
                            <input type="hidden" name="id" value="<?= $l['id'] ?>">
                            <button class="btn btn-sm text-danger p-0 lh-1"><i class="bi bi-x"></i></button>
                        </form>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
