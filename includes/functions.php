<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';

/**
 * Database connection using PDO with prepared statements (SQL injection protection).
 */
function db() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            include __DIR__ . '/../500.php';
            exit;
        }
    }
    return $pdo;
}

/**
 * XSS-safe output.
 */
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect helper.
 */
function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

/**
 * Build a site URL.
 */
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

/**
 * Media URL: prefixes local paths with BASE_URL, leaves external URLs as-is.
 * Used for image_url, audio_url, video_url fields from the database.
 */
function media_url(?string $path): string {
    if (!$path) return url('public/images/default-cover.webp');
    if (str_starts_with($path, 'http')) return $path;
    return url($path);
}

/**
 * Asset URL.
 */
function asset(string $path): string {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Check if user is logged in.
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin.
 */
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * Current logged-in user array.
 */
function current_user(): ?array {
    if (!is_logged_in()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'full_name' => $_SESSION['full_name'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['user_role'],
    ];
}

/**
 * Require login, else redirect.
 */
function require_login(): void {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

/**
 * Require admin, else redirect.
 */
function require_admin(): void {
    if (!is_admin()) {
        redirect('index.php');
    }
}

/**
 * Check login rate limit (max 5 attempts per 15 minutes per IP).
 * Returns true if allowed, false if rate-limited.
 */
function check_login_rate_limit(string $identifier): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = 'login_attempts_' . md5($identifier . $ip);
    $attempts = $_SESSION[$key] ?? [];
    $now = time();
    $attempts = array_filter($attempts, fn($t) => $t > $now - 900);
    if (count($attempts) >= 5) {
        return false;
    }
    return true;
}

/**
 * Record a failed login attempt.
 */
function record_login_attempt(string $identifier): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = 'login_attempts_' . md5($identifier . $ip);
    if (!isset($_SESSION[$key])) $_SESSION[$key] = [];
    $_SESSION[$key][] = time();
}

/**
 * Clear login attempts after successful login.
 */
function clear_login_attempts(string $identifier): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = 'login_attempts_' . md5($identifier . $ip);
    unset($_SESSION[$key]);
}

/**
 * Regenerate session ID to prevent session fixation.
 */
function regenerate_session(): void {
    $old = $_SESSION;
    session_destroy();
    session_start();
    $_SESSION = $old;
    session_regenerate_id(true);
}

/**
 * Flash message helper.
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format duration in MM:SS.
 */
function format_duration(?int $seconds): string {
    if (!$seconds || $seconds <= 0) return '--:--';
    $m = floor($seconds / 60);
    $s = $seconds % 60;
    return $m . ':' . str_pad((string)$s, 2, '0', STR_PAD_LEFT);
}

/**
 * Format date.
 */
function format_date(?string $date): string {
    if (!$date) return '';
    return date('M j, Y', strtotime($date));
}

/**
 * Generate CSRF token.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token.
 */
function verify_csrf(string $token): bool {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Average rating helper.
 */
function avg_rating(array $ratings): float {
    if (empty($ratings)) return 0;
    return array_sum(array_column($ratings, 'score')) / count($ratings);
}

/**
 * Handle a validated file upload with a random filename.
 * Returns the saved relative path (BASE_URL/uploads/...) or null on failure.
 * @param string $field  Form field name
 * @param string $kind   One of: image, audio, video
 * @return string|null   Relative path from project root, or null if no file / invalid
 */
function handle_upload(string $field, string $kind): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        set_flash('danger', 'Upload failed (error code ' . $file['error'] . ').');
        return null;
    }
    if ($file['size'] > 50 * 1024 * 1024) {
        set_flash('danger', 'File too large. Maximum 50 MB.');
        return null;
    }

    $allowed = [
        'image' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/jpg' => 'jpg', 'image/webp' => 'webp'],
        'audio' => ['audio/mpeg' => 'mp3', 'audio/mp3' => 'mp3'],
        'video' => ['video/mp4' => 'mp4', 'video/webm' => 'webm'],
    ];
    if (!isset($allowed[$kind])) {
        set_flash('danger', 'Invalid upload type.');
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowed[$kind][$mime])) {
        set_flash('danger', 'Invalid file type. Allowed: ' . implode(', ', array_unique(array_values($allowed[$kind]))));
        return null;
    }

    $folderMap = ['image' => 'covers', 'audio' => 'songs', 'video' => 'videos'];
    $folder = $folderMap[$kind];
    $ext = $allowed[$kind][$mime];
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/' . $folder;
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    if (!is_writable($uploadDir)) {
        set_flash('danger', 'Upload directory is not writable. Please create uploads/' . $folder . '/ with write permission.');
        return null;
    }
    if (!move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $name)) {
        set_flash('danger', 'Failed to save uploaded file.');
        return null;
    }
    return 'uploads/' . $folder . '/' . $name;
}

/**
 * Log an activity to the database.
 */
function log_activity(?int $userId, string $action, ?string $details = null): void {
    try {
        $stmt = db()->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?,?,?,?)");
        $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? null]);
    } catch (Throwable $e) {
        // Silently fail — logging is best-effort
    }
}

/**
 * Get recent activity logs (admin view).
 */
function get_activity_logs(int $limit = 50): array {
    $stmt = db()->prepare("SELECT a.*, u.username FROM activity_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT $limit");
    $stmt->execute([]);
    return $stmt->fetchAll();
}

/**
 * Create a password reset token for a user. Returns the token or null.
 */
function create_password_reset(int $userId): ?string {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
    db()->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?,?,?)")->execute([$userId, $token, $expires]);
    return $token;
}

/**
 * Verify a password reset token. Returns user_id or null.
 */
function verify_password_reset(string $token): ?int {
    $stmt = db()->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    return $row ? (int)$row['user_id'] : null;
}

/**
 * Consume (mark used) a password reset token.
 */
function consume_password_reset(string $token): void {
    db()->prepare("UPDATE password_resets SET used = 1 WHERE token = ?")->execute([$token]);
}

/**
 * Human-readable "time ago" formatter.
 */
function time_ago(string $datetime): string {
    $ts = strtotime($datetime);
    $diff = time() - $ts;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    if ($diff < 2592000) return floor($diff / 604800) . 'w ago';
    return date('M j, Y', $ts);
}

/**
 * Paginate an array of items. Returns [items, pagination_html].
 */
function paginate(array $items, int $perPage = 12, string $paramName = 'p'): array {
    $total = count($items);
    $pages = max(1, ceil($total / $perPage));
    $current = max(1, min((int)($_GET[$paramName] ?? 1), $pages));
    $offset = ($current - 1) * $perPage;
    $pageItems = array_slice($items, $offset, $perPage);
    $html = '';
    if ($pages > 1) {
        $queryParams = $_GET;
        unset($queryParams[$paramName]);
        $base = http_build_query($queryParams);
        $sep = $base ? '&' : '';
        $url = basename($_SERVER['PHP_SELF']) . ($base ? "?$base&" : '?');
        $html = '<nav class="d-flex justify-content-center mt-4"><ul class="pagination">';
        if ($current > 1) $html .= '<li class="page-item"><a class="page-link" href="' . $url . $paramName . '=' . ($current - 1) . '"><i class="bi bi-chevron-left"></i></a></li>';
        for ($i = 1; $i <= $pages; $i++) {
            if ($i == 1 || $i == $pages || abs($i - $current) <= 2) {
                $html .= '<li class="page-item ' . ($i == $current ? 'active' : '') . '"><a class="page-link" href="' . $url . $paramName . '=' . $i . '">' . $i . '</a></li>';
            } elseif ($i == 2 || $i == $pages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }
        }
        if ($current < $pages) $html .= '<li class="page-item"><a class="page-link" href="' . $url . $paramName . '=' . ($current + 1) . '"><i class="bi bi-chevron-right"></i></a></li>';
        $html .= '</ul></nav>';
    }
    return [$pageItems, $html];
}

/**
 * Get all flash messages (for toast JSON).
 */
function get_all_flashes(): array {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/**
 * Render a breadcrumb trail.
 */
function render_breadcrumb(array $crumbs): void {
    echo '<ul class="breadcrumb-custom">';
    echo '<li><a href="' . url('index.php') . '"><i class="bi bi-house"></i> Home</a></li>';
    foreach ($crumbs as $i => $crumb) {
        echo '<li class="sep"><i class="bi bi-chevron-right"></i></li>';
        if (isset($crumb['url']) && $i < count($crumbs) - 1) {
            echo '<li><a href="' . url($crumb['url']) . '">' . e($crumb['label']) . '</a></li>';
        } else {
            echo '<li class="active">' . e($crumb['label']) . '</li>';
        }
    }
    echo '</ul>';
}
