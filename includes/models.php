<?php
require_once __DIR__ . '/functions.php';

/* ============ SONGS ============ */
function get_songs(int $limit = 0, string $order = 'created_at DESC'): array {
    $sql = "SELECT s.*, a.name AS artist_name, al.title AS album_title, g.name AS genre_name, l.name AS language_name
            FROM songs s
            LEFT JOIN artists a ON s.artist_id = a.id
            LEFT JOIN albums al ON s.album_id = al.id
            LEFT JOIN genres g ON s.genre_id = g.id
            LEFT JOIN languages l ON s.language_id = l.id
            ORDER BY $order" . ($limit ? " LIMIT $limit" : "");
    return db()->query($sql)->fetchAll();
}

function get_latest_songs(int $limit = 5): array {
    return get_songs($limit, 'created_at DESC');
}

function get_trending_songs(int $limit = 5): array {
    return get_songs($limit, 'views DESC');
}

function get_song(int $id): ?array {
    $stmt = db()->prepare("SELECT s.*, a.name AS artist_name, a.id AS artist_id, al.title AS album_title, g.name AS genre_name, l.name AS language_name
            FROM songs s
            LEFT JOIN artists a ON s.artist_id = a.id
            LEFT JOIN albums al ON s.album_id = al.id
            LEFT JOIN genres g ON s.genre_id = g.id
            LEFT JOIN languages l ON s.language_id = l.id
            WHERE s.id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function increment_song_views(int $id): void {
    db()->prepare("UPDATE songs SET views = views + 1 WHERE id = ?")->execute([$id]);
}

function get_related_songs(int $songId, int $limit = 5): array {
    $song = get_song($songId);
    if (!$song) return [];
    $stmt = db()->prepare("SELECT s.*, a.name AS artist_name
            FROM songs s LEFT JOIN artists a ON s.artist_id = a.id
            WHERE s.id != ? AND (s.artist_id = ? OR s.genre_id = ? OR s.album_id = ?)
            ORDER BY s.created_at DESC LIMIT $limit");
    $stmt->execute([$songId, $song['artist_id'], $song['genre_id'], $song['album_id']]);
    return $stmt->fetchAll();
}

function search_songs(string $q): array {
    $like = "%$q%";
    $stmt = db()->prepare("SELECT s.*, a.name AS artist_name
            FROM songs s LEFT JOIN artists a ON s.artist_id = a.id
            WHERE s.title LIKE ? OR a.name LIKE ? OR (s.year IS NOT NULL AND CAST(s.year AS CHAR) LIKE ?)
            ORDER BY s.created_at DESC");
    $stmt->execute([$like, $like, $like]);
    return $stmt->fetchAll();
}

function filter_songs(array $f): array {
    $where = []; $params = [];
    if (!empty($f['q'])) { $where[] = "(s.title LIKE ? OR a.name LIKE ?)"; $params[] = "%{$f['q']}%"; $params[] = "%{$f['q']}%"; }
    if (!empty($f['artist'])) { $where[] = "s.artist_id = ?"; $params[] = $f['artist']; }
    if (!empty($f['genre'])) { $where[] = "s.genre_id = ?"; $params[] = $f['genre']; }
    if (!empty($f['language'])) { $where[] = "s.language_id = ?"; $params[] = $f['language']; }
    if (!empty($f['album'])) { $where[] = "s.album_id = ?"; $params[] = $f['album']; }
    if (!empty($f['year'])) { $where[] = "s.year = ?"; $params[] = $f['year']; }
    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $sort = $f['sort'] ?? 'newest';
    $order = match($sort) {
        'title' => 's.title ASC',
        'year' => 's.year DESC',
        'views' => 's.views DESC',
        default => 's.created_at DESC',
    };
    $stmt = db()->prepare("SELECT s.*, a.name AS artist_name, al.title AS album_title, g.name AS genre_name, l.name AS language_name
            FROM songs s
            LEFT JOIN artists a ON s.artist_id = a.id
            LEFT JOIN albums al ON s.album_id = al.id
            LEFT JOIN genres g ON s.genre_id = g.id
            LEFT JOIN languages l ON s.language_id = l.id
            $whereSql ORDER BY $order");
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/* ============ VIDEOS ============ */
function get_videos(int $limit = 0, string $order = 'created_at DESC'): array {
    $sql = "SELECT v.*, a.name AS artist_name, al.title AS album_title, g.name AS genre_name, l.name AS language_name
            FROM videos v
            LEFT JOIN artists a ON v.artist_id = a.id
            LEFT JOIN albums al ON v.album_id = al.id
            LEFT JOIN genres g ON v.genre_id = g.id
            LEFT JOIN languages l ON v.language_id = l.id
            ORDER BY $order" . ($limit ? " LIMIT $limit" : "");
    return db()->query($sql)->fetchAll();
}

function get_latest_videos(int $limit = 5): array {
    return get_videos($limit, 'created_at DESC');
}

function get_video(int $id): ?array {
    $stmt = db()->prepare("SELECT v.*, a.name AS artist_name, a.id AS artist_id, al.title AS album_title, g.name AS genre_name, l.name AS language_name
            FROM videos v
            LEFT JOIN artists a ON v.artist_id = a.id
            LEFT JOIN albums al ON v.album_id = al.id
            LEFT JOIN genres g ON v.genre_id = g.id
            LEFT JOIN languages l ON v.language_id = l.id
            WHERE v.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function increment_video_views(int $id): void {
    db()->prepare("UPDATE videos SET views = views + 1 WHERE id = ?")->execute([$id]);
}

function search_videos(string $q): array {
    $like = "%$q%";
    $stmt = db()->prepare("SELECT v.*, a.name AS artist_name
            FROM videos v LEFT JOIN artists a ON v.artist_id = a.id
            WHERE v.title LIKE ? OR a.name LIKE ? OR (v.year IS NOT NULL AND CAST(v.year AS CHAR) LIKE ?)
            ORDER BY v.created_at DESC");
    $stmt->execute([$like, $like, $like]);
    return $stmt->fetchAll();
}

function filter_videos(array $f): array {
    $where = []; $params = [];
    if (!empty($f['q'])) { $where[] = "(v.title LIKE ? OR a.name LIKE ?)"; $params[] = "%{$f['q']}%"; $params[] = "%{$f['q']}%"; }
    if (!empty($f['artist'])) { $where[] = "v.artist_id = ?"; $params[] = $f['artist']; }
    if (!empty($f['genre'])) { $where[] = "v.genre_id = ?"; $params[] = $f['genre']; }
    if (!empty($f['language'])) { $where[] = "v.language_id = ?"; $params[] = $f['language']; }
    if (!empty($f['album'])) { $where[] = "v.album_id = ?"; $params[] = $f['album']; }
    if (!empty($f['year'])) { $where[] = "v.year = ?"; $params[] = $f['year']; }
    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $sort = $f['sort'] ?? 'newest';
    $order = match($sort) {
        'title' => 'v.title ASC',
        'year' => 'v.year DESC',
        'views' => 'v.views DESC',
        default => 'v.created_at DESC',
    };
    $stmt = db()->prepare("SELECT v.*, a.name AS artist_name, al.title AS album_title, g.name AS genre_name, l.name AS language_name
            FROM videos v
            LEFT JOIN artists a ON v.artist_id = a.id
            LEFT JOIN albums al ON v.album_id = al.id
            LEFT JOIN genres g ON v.genre_id = g.id
            LEFT JOIN languages l ON v.language_id = l.id
            $whereSql ORDER BY $order");
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/* ============ ARTISTS ============ */
function get_artists(int $limit = 0): array {
    $sql = "SELECT a.*, (SELECT COUNT(*) FROM songs s WHERE s.artist_id = a.id) AS song_count
            FROM artists a ORDER BY a.name ASC" . ($limit ? " LIMIT $limit" : "");
    return db()->query($sql)->fetchAll();
}

function get_artist(int $id): ?array {
    $stmt = db()->prepare("SELECT * FROM artists WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function get_artist_songs(int $artistId, int $limit = 0): array {
    $sql = "SELECT s.*, a.name AS artist_name FROM songs s LEFT JOIN artists a ON s.artist_id = a.id
            WHERE s.artist_id = ? ORDER BY s.created_at DESC" . ($limit ? " LIMIT $limit" : "");
    $stmt = db()->prepare($sql);
    $stmt->execute([$artistId]);
    return $stmt->fetchAll();
}

function get_artist_videos(int $artistId, int $limit = 0): array {
    $sql = "SELECT v.*, a.name AS artist_name FROM videos v LEFT JOIN artists a ON v.artist_id = a.id
            WHERE v.artist_id = ? ORDER BY v.created_at DESC" . ($limit ? " LIMIT $limit" : "");
    $stmt = db()->prepare($sql);
    $stmt->execute([$artistId]);
    return $stmt->fetchAll();
}

function get_artist_albums(int $artistId): array {
    $stmt = db()->prepare("SELECT * FROM albums WHERE artist_id = ? ORDER BY year DESC");
    $stmt->execute([$artistId]);
    return $stmt->fetchAll();
}

/* ============ ALBUMS ============ */
function get_albums(int $limit = 0): array {
    $sql = "SELECT al.*, a.name AS artist_name, (SELECT COUNT(*) FROM songs s WHERE s.album_id = al.id) AS song_count
            FROM albums al LEFT JOIN artists a ON al.artist_id = a.id ORDER BY al.title ASC" . ($limit ? " LIMIT $limit" : "");
    return db()->query($sql)->fetchAll();
}

function get_album(int $id): ?array {
    $stmt = db()->prepare("SELECT al.*, a.name AS artist_name FROM albums al LEFT JOIN artists a ON al.artist_id = a.id WHERE al.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function get_album_songs(int $albumId): array {
    $stmt = db()->prepare("SELECT s.*, a.name AS artist_name FROM songs s LEFT JOIN artists a ON s.artist_id = a.id WHERE s.album_id = ? ORDER BY s.id ASC");
    $stmt->execute([$albumId]);
    return $stmt->fetchAll();
}

/* ============ GENRES / LANGUAGES ============ */
function get_genres(): array {
    return db()->query("SELECT g.*, (SELECT COUNT(*) FROM songs s WHERE s.genre_id = g.id) AS song_count FROM genres g ORDER BY g.name ASC")->fetchAll();
}
function get_genre(int $id): ?array {
    $stmt = db()->prepare("SELECT * FROM genres WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}
function get_languages(): array {
    return db()->query("SELECT * FROM languages ORDER BY name ASC")->fetchAll();
}

/* ============ COMMENTS / RATINGS ============ */
function get_comments(string $type, int $mediaId): array {
    $stmt = db()->prepare("SELECT c.*, u.username, u.avatar_url FROM comments c JOIN users u ON c.user_id = u.id
            WHERE c.media_type = ? AND c.media_id = ? ORDER BY c.created_at DESC");
    $stmt->execute([$type, $mediaId]);
    return $stmt->fetchAll();
}

function get_avg_rating(string $type, int $mediaId): float {
    $stmt = db()->prepare("SELECT AVG(score) AS avg FROM ratings WHERE media_type = ? AND media_id = ?");
    $stmt->execute([$type, $mediaId]);
    $row = $stmt->fetch();
    return $row['avg'] ? round((float)$row['avg'], 1) : 0;
}

function get_rating_count(string $type, int $mediaId): int {
    $stmt = db()->prepare("SELECT COUNT(*) AS c FROM ratings WHERE media_type = ? AND media_id = ?");
    $stmt->execute([$type, $mediaId]);
    return (int)($stmt->fetch()['c'] ?? 0);
}

function get_user_rating(int $userId, string $type, int $mediaId): int {
    $stmt = db()->prepare("SELECT score FROM ratings WHERE user_id = ? AND media_type = ? AND media_id = ?");
    $stmt->execute([$userId, $type, $mediaId]);
    $row = $stmt->fetch();
    return $row ? (int)$row['score'] : 0;
}

/* ============ FAVOURITES ============ */
function is_favourite(int $userId, string $type, int $mediaId): bool {
    $stmt = db()->prepare("SELECT id FROM favourites WHERE user_id = ? AND media_type = ? AND media_id = ?");
    $stmt->execute([$userId, $type, $mediaId]);
    return (bool)$stmt->fetch();
}

function toggle_favourite(int $userId, string $type, int $mediaId): bool {
    if (is_favourite($userId, $type, $mediaId)) {
        db()->prepare("DELETE FROM favourites WHERE user_id = ? AND media_type = ? AND media_id = ?")->execute([$userId, $type, $mediaId]);
        return false;
    }
    db()->prepare("INSERT INTO favourites (user_id, media_type, media_id) VALUES (?,?,?)")->execute([$userId, $type, $mediaId]);
    return true;
}

function get_favourite_songs(int $userId): array {
    $stmt = db()->prepare("SELECT s.*, a.name AS artist_name FROM favourites f JOIN songs s ON f.media_id = s.id AND f.media_type='song'
            LEFT JOIN artists a ON s.artist_id = a.id WHERE f.user_id = ? ORDER BY f.created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function get_favourite_videos(int $userId): array {
    $stmt = db()->prepare("SELECT v.*, a.name AS artist_name FROM favourites f JOIN videos v ON f.media_id = v.id AND f.media_type='video'
            LEFT JOIN artists a ON v.artist_id = a.id WHERE f.user_id = ? ORDER BY f.created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function count_user_favourites(int $userId): int {
    $stmt = db()->prepare("SELECT COUNT(*) FROM favourites WHERE user_id = ?");
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

/* ============ PLAYLISTS ============ */
function get_playlists(int $userId): array {
    $stmt = db()->prepare("SELECT p.*, (SELECT COUNT(*) FROM playlist_items pi WHERE pi.playlist_id = p.id) AS item_count
            FROM playlists p WHERE p.user_id = ? ORDER BY p.created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function get_playlist(int $id, int $userId): ?array {
    $stmt = db()->prepare("SELECT * FROM playlists WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
    return $stmt->fetch() ?: null;
}

function get_playlist_items(int $playlistId): array {
    $songs = db()->prepare("SELECT s.*, a.name AS artist_name FROM playlist_items pi JOIN songs s ON pi.song_id = s.id
            LEFT JOIN artists a ON s.artist_id = a.id WHERE pi.playlist_id = ? AND pi.song_id IS NOT NULL ORDER BY pi.added_at DESC");
    $songs->execute([$playlistId]);
    $videos = db()->prepare("SELECT v.*, a.name AS artist_name FROM playlist_items pi JOIN videos v ON pi.video_id = v.id
            LEFT JOIN artists a ON v.artist_id = a.id WHERE pi.playlist_id = ? AND pi.video_id IS NOT NULL ORDER BY pi.added_at DESC");
    $videos->execute([$playlistId]);
    return ['songs' => $songs->fetchAll(), 'videos' => $videos->fetchAll()];
}

/* ============ HISTORY ============ */
function add_history(int $userId, string $type, int $mediaId): void {
    try {
        db()->prepare("INSERT INTO play_history (user_id, media_type, media_id) VALUES (?,?,?)")->execute([$userId, $type, $mediaId]);
    } catch (\Throwable $e) {
        // Table may not exist or FK constraint — silently skip history tracking
    }
}

function get_history(int $userId, int $limit = 20): array {
    $stmt = db()->prepare("SELECT h.*, CASE WHEN h.media_type='song' THEN s.title ELSE v.title END AS title,
            CASE WHEN h.media_type='song' THEN s.image_url ELSE v.image_url END AS image_url
            FROM play_history h
            LEFT JOIN songs s ON h.media_type='song' AND h.media_id = s.id
            LEFT JOIN videos v ON h.media_type='video' AND h.media_id = v.id
            WHERE h.user_id = ? ORDER BY h.played_at DESC LIMIT $limit");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/* ============ USERS ============ */
function get_all_users(): array {
    return db()->query("SELECT id, username, full_name, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();
}

function get_user_by_id(int $id): ?array {
    $stmt = db()->prepare("SELECT id, username, full_name, address, phone, email, role, avatar_url, bio, created_at FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function get_user_by_username(string $username): ?array {
    $stmt = db()->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->execute([$username, $username]);
    return $stmt->fetch() ?: null;
}

/* ============ NEWSLETTER ============ */
function add_newsletter(string $email): bool {
    $stmt = db()->prepare("INSERT IGNORE INTO newsletter (email) VALUES (?)");
    $stmt->execute([$email]);
    return $stmt->rowCount() > 0;
}

/* ============ STATS (admin) ============ */
function admin_stats(): array {
    $pdo = db();
    return [
        'songs' => (int)$pdo->query("SELECT COUNT(*) FROM songs")->fetchColumn(),
        'videos' => (int)$pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn(),
        'albums' => (int)$pdo->query("SELECT COUNT(*) FROM albums")->fetchColumn(),
        'artists' => (int)$pdo->query("SELECT COUNT(*) FROM artists")->fetchColumn(),
        'users' => (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'comments' => (int)$pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn(),
        'ratings' => (int)$pdo->query("SELECT COUNT(*) FROM ratings")->fetchColumn(),
        'genres' => (int)$pdo->query("SELECT COUNT(*) FROM genres")->fetchColumn(),
        'languages' => (int)$pdo->query("SELECT COUNT(*) FROM languages")->fetchColumn(),
        'newsletter' => (int)$pdo->query("SELECT COUNT(*) FROM newsletter")->fetchColumn(),
    ];
}

/* ============ CHART DATA ============ */
function chart_songs_trend(int $months = 6): array {
    $pdo = db();
    $data = [];
    for ($i = $months - 1; $i >= 0; $i--) {
        $start = date('Y-m-01', strtotime("-$i months"));
        $end = date('Y-m-t', strtotime("-$i months"));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM songs WHERE created_at >= ? AND created_at <= ?");
        $stmt->execute([$start . ' 00:00:00', $end . ' 23:59:59']);
        $data[] = ['label' => date('M', strtotime($start)), 'value' => (int)$stmt->fetchColumn()];
    }
    return $data;
}

function chart_videos_trend(int $months = 6): array {
    $pdo = db();
    $data = [];
    for ($i = $months - 1; $i >= 0; $i--) {
        $start = date('Y-m-01', strtotime("-$i months"));
        $end = date('Y-m-t', strtotime("-$i months"));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM videos WHERE created_at >= ? AND created_at <= ?");
        $stmt->execute([$start . ' 00:00:00', $end . ' 23:59:59']);
        $data[] = ['label' => date('M', strtotime($start)), 'value' => (int)$stmt->fetchColumn()];
    }
    return $data;
}

function chart_users_growth(int $months = 6): array {
    $pdo = db();
    $data = [];
    $cumulative = 0;
    $baseCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE created_at < '" . date('Y-m-01', strtotime('-' . ($months - 1) . ' months')) . "'")->fetchColumn();
    $cumulative = $baseCount;
    for ($i = $months - 1; $i >= 0; $i--) {
        $start = date('Y-m-01', strtotime("-$i months"));
        $end = date('Y-m-t', strtotime("-$i months"));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE created_at >= ? AND created_at <= ?");
        $stmt->execute([$start . ' 00:00:00', $end . ' 23:59:59']);
        $cumulative += (int)$stmt->fetchColumn();
        $data[] = ['label' => date('M', strtotime($start)), 'value' => $cumulative];
    }
    return $data;
}

function chart_top_genres(): array {
    $pdo = db();
    $rows = $pdo->query("SELECT g.name, COUNT(s.id) AS count FROM genres g LEFT JOIN songs s ON s.genre_id = g.id GROUP BY g.id ORDER BY count DESC LIMIT 6")->fetchAll();
    return array_map(fn($r) => ['label' => $r['name'], 'value' => (int)$r['count']], $rows);
}

/* ============ ANALYTICS ============ */
function get_most_played_songs(int $limit = 10): array {
    return db()->query("SELECT s.title, s.views, a.name AS artist_name, s.image_url FROM songs s LEFT JOIN artists a ON s.artist_id = a.id ORDER BY s.views DESC LIMIT $limit")->fetchAll();
}

function get_top_rated_songs(int $limit = 10): array {
    return db()->query("SELECT s.title, ROUND(AVG(r.score),1) AS avg_rating, COUNT(r.id) AS rating_count, a.name AS artist_name, s.image_url FROM songs s LEFT JOIN ratings r ON r.media_id = s.id AND r.media_type = 'song' LEFT JOIN artists a ON s.artist_id = a.id GROUP BY s.id HAVING avg_rating IS NOT NULL ORDER BY avg_rating DESC, rating_count DESC LIMIT $limit")->fetchAll();
}

function get_most_viewed_videos(int $limit = 10): array {
    return db()->query("SELECT v.title, v.views, a.name AS artist_name, v.image_url FROM videos v LEFT JOIN artists a ON v.artist_id = a.id ORDER BY v.views DESC LIMIT $limit")->fetchAll();
}

function get_most_active_users(int $limit = 10): array {
    return db()->query("SELECT u.username, u.avatar_url, COUNT(c.id) AS comment_count, COUNT(r.id) AS rating_count, (COUNT(c.id) + COUNT(r.id)) AS activity FROM users u LEFT JOIN comments c ON c.user_id = u.id LEFT JOIN ratings r ON r.user_id = u.id GROUP BY u.id ORDER BY activity DESC LIMIT $limit")->fetchAll();
}

/* ============ NOTIFICATIONS ============ */
function get_admin_notifications(int $limit = 10): array {
    $pdo = db();
    $notifs = [];
    $newUsers = $pdo->query("SELECT id, username, created_at FROM users ORDER BY created_at DESC LIMIT $limit")->fetchAll();
    foreach ($newUsers as $u) {
        $notifs[] = ['type' => 'user', 'icon' => 'bi-person-plus', 'text' => $u['username'] . ' registered', 'time' => $u['created_at'], 'color' => '#3B82F6'];
    }
    $newComments = $pdo->query("SELECT c.id, u.username, c.created_at FROM comments c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC LIMIT $limit")->fetchAll();
    foreach ($newComments as $c) {
        $notifs[] = ['type' => 'review', 'icon' => 'bi-chat-square-text', 'text' => $c['username'] . ' posted a review', 'time' => $c['created_at'], 'color' => '#F59E0B'];
    }
    $newVideos = $pdo->query("SELECT id, title, created_at FROM videos ORDER BY created_at DESC LIMIT $limit")->fetchAll();
    foreach ($newVideos as $v) {
        $notifs[] = ['type' => 'video', 'icon' => 'bi-play-btn', 'text' => 'New video: ' . $v['title'], 'time' => $v['created_at'], 'color' => '#FF4D6D'];
    }
    usort($notifs, fn($a, $b) => strcmp($b['time'], $a['time']));
    return array_slice($notifs, 0, $limit);
}

function get_unread_notification_count(): int {
    $pdo = db();
    $key = 'admin_notif_seen_' . ($_SESSION['user_id'] ?? 0);
    $lastSeen = $_SESSION[$key] ?? '1970-01-01 00:00:00';
    $songs = (int)$pdo->prepare("SELECT COUNT(*) FROM videos WHERE created_at > ?");
    $stmt = $pdo->prepare("SELECT (SELECT COUNT(*) FROM users WHERE created_at > ?) + (SELECT COUNT(*) FROM comments WHERE created_at > ?) + (SELECT COUNT(*) FROM videos WHERE created_at > ?) AS total");
    $stmt->execute([$lastSeen, $lastSeen, $lastSeen]);
    return (int)$stmt->fetchColumn();
}
