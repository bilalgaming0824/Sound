<?php
$pageTitle = 'Manage Reviews';
$section = 'comments';
require_once __DIR__ . '/includes/header.php';
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf'] ?? '')) { set_flash('danger', 'Invalid request.'); redirect('admin/comments.php'); }
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        db()->prepare("DELETE FROM comments WHERE id = ?")->execute([(int)$_POST['id']]);
        set_flash('success', 'Review deleted.');
    } elseif ($action === 'edit') {
        $cid = (int)$_POST['id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        if ($rating < 1 || $rating > 5) { set_flash('danger', 'Rating must be 1-5.'); redirect('admin/comments.php'); }
        if ($comment === '') { set_flash('danger', 'Comment cannot be empty.'); redirect('admin/comments.php'); }
        db()->prepare("UPDATE comments SET rating = ?, comment = ? WHERE id = ?")->execute([$rating, $comment, $cid]);
        set_flash('success', 'Review updated.');
    }
    redirect('admin/comments.php');
}

$comments = db()->query("SELECT c.*, u.username, u.avatar_url,
    CASE WHEN c.media_type='song' THEN s.title ELSE v.title END AS media_title
    FROM comments c JOIN users u ON c.user_id = u.id
    LEFT JOIN songs s ON c.media_type='song' AND c.media_id = s.id
    LEFT JOIN videos v ON c.media_type='video' AND c.media_id = v.id
    ORDER BY c.created_at DESC")->fetchAll();
?>
<?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
<p class="text-secondary mb-3"><?= count($comments) ?> reviews</p>
<div class="admin-table table-responsive">
    <table class="table table-hover">
        <thead><tr><th>User</th><th>Media</th><th>Rating</th><th>Comment</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($comments as $c): ?>
            <tr>
                <td><span class="avatar me-2"><?= strtoupper(substr($c['username'],0,1)) ?></span><?= e($c['username']) ?></td>
                <td class="small"><?= e($c['media_type']) ?>: <?= e($c['media_title'] ?? '—') ?></td>
                <td><?= render_star_rating($c['rating']) ?></td>
                <td class="small" style="max-width:300px"><?= e($c['comment']) ?></td>
                <td class="small text-secondary"><?= format_date($c['created_at']) ?></td>
                <td>
                    <button class="btn btn-sm btn-ghost" data-bs-toggle="modal" data-bs-target="#editModal"
                        data-id="<?= $c['id'] ?>" data-rating="<?= $c['rating'] ?>" data-comment="<?= e($c['comment']) ?>"><i class="bi bi-pencil"></i></button>
                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Delete this review?')">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <button class="btn btn-sm btn-ghost text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="" class="modal-content card-media">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <div class="modal-header border-0"><h5 class="modal-title text-white">Edit Review</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-select mb-3">
                    <?php for ($i=1;$i<=5;$i++): ?><option value="<?= $i ?>" <?= $i==5?'selected':'' ?>><?= $i ?> star<?= $i!=1?'s':'' ?></option><?php endfor; ?>
                </select>
                <label class="form-label">Comment</label>
                <textarea name="comment" rows="4" class="form-control" required></textarea>
            </div>
            <div class="modal-footer border-0"><button class="btn btn-primary">Save Changes</button></div>
        </form>
    </div>
</div>

<script>
var editModal=document.getElementById('editModal');
if(editModal){editModal.addEventListener('show.bs.modal',function(e){
    var b=e.relatedTarget;
    document.getElementById('editId').value=b.dataset.id;
    editModal.querySelector('select[name=rating]').value=b.dataset.rating;
    editModal.querySelector('textarea[name=comment]').value=b.dataset.comment;
});}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
