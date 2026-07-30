-- ============================================================
-- SOUND Entertainment — Complete MySQL Database
-- Import this ONE file into phpMyAdmin / MySQL.
-- It creates the database, all tables, indexes, views,
-- triggers, and seeds sample data — everything in one go.
-- ============================================================

CREATE DATABASE IF NOT EXISTS sound_entertainment
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sound_entertainment;

-- ---------- USERS ----------
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  address VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  email_verified TINYINT(1) NOT NULL DEFAULT 0,
  avatar_url VARCHAR(255) DEFAULT NULL,
  bio TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- ARTISTS ----------
CREATE TABLE IF NOT EXISTS artists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  bio TEXT DEFAULT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  social_facebook VARCHAR(255) DEFAULT NULL,
  social_twitter VARCHAR(255) DEFAULT NULL,
  social_instagram VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- GENRES / CATEGORIES ----------
CREATE TABLE IF NOT EXISTS genres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- LANGUAGES ----------
CREATE TABLE IF NOT EXISTS languages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- ALBUMS ----------
CREATE TABLE IF NOT EXISTS albums (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  artist_id INT DEFAULT NULL,
  year INT DEFAULT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE SET NULL
);

-- ---------- SONGS ----------
CREATE TABLE IF NOT EXISTS songs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT DEFAULT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  audio_url VARCHAR(255) DEFAULT NULL,
  artist_id INT DEFAULT NULL,
  album_id INT DEFAULT NULL,
  genre_id INT DEFAULT NULL,
  language_id INT DEFAULT NULL,
  year INT DEFAULT NULL,
  duration INT DEFAULT NULL,
  is_new TINYINT(1) NOT NULL DEFAULT 0,
  views INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE SET NULL,
  FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL,
  FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL,
  FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE SET NULL
);

-- ---------- VIDEOS ----------
CREATE TABLE IF NOT EXISTS videos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT DEFAULT NULL,
  image_url VARCHAR(255) DEFAULT NULL,
  video_url VARCHAR(255) DEFAULT NULL,
  artist_id INT DEFAULT NULL,
  album_id INT DEFAULT NULL,
  genre_id INT DEFAULT NULL,
  language_id INT DEFAULT NULL,
  year INT DEFAULT NULL,
  duration INT DEFAULT NULL,
  is_new TINYINT(1) NOT NULL DEFAULT 0,
  views INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE SET NULL,
  FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL,
  FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE SET NULL,
  FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE SET NULL
);

-- ---------- COMMENTS / REVIEWS ----------
CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  media_type ENUM('song','video') NOT NULL,
  media_id INT NOT NULL,
  rating INT NOT NULL DEFAULT 0 CHECK (rating BETWEEN 0 AND 5),
  comment TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------- RATINGS (separate quick ratings) ----------
CREATE TABLE IF NOT EXISTS ratings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  media_type ENUM('song','video') NOT NULL,
  media_id INT NOT NULL,
  score INT NOT NULL CHECK (score BETWEEN 1 AND 5),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_rating (user_id, media_type, media_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------- FAVOURITES ----------
CREATE TABLE IF NOT EXISTS favourites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  media_type ENUM('song','video') NOT NULL,
  media_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_fav (user_id, media_type, media_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------- PLAYLISTS ----------
CREATE TABLE IF NOT EXISTS playlists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS playlist_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  playlist_id INT NOT NULL,
  song_id INT DEFAULT NULL,
  video_id INT DEFAULT NULL,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
  FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
  FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE
);

-- ---------- HISTORY ----------
CREATE TABLE IF NOT EXISTS play_history (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  media_type ENUM('song','video') NOT NULL,
  media_id INT NOT NULL,
  played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------- NEWSLETTER ----------
CREATE TABLE IF NOT EXISTS newsletter (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- CONTACT MESSAGES ----------
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- ACTIVITY LOGS ----------
CREATE TABLE IF NOT EXISTS activity_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  action VARCHAR(100) NOT NULL,
  details VARCHAR(255) DEFAULT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ---------- PASSWORD RESETS ----------
CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------- EMAIL VERIFICATION ----------
CREATE TABLE IF NOT EXISTS email_verifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  used TINYINT(1) NOT NULL DEFAULT 0,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- INDEXES
-- ============================================================
CREATE INDEX idx_songs_artist ON songs(artist_id);
CREATE INDEX idx_songs_album ON songs(album_id);
CREATE INDEX idx_songs_genre ON songs(genre_id);
CREATE INDEX idx_songs_language ON songs(language_id);
CREATE INDEX idx_songs_year ON songs(year);
CREATE INDEX idx_songs_created ON songs(created_at DESC);
CREATE INDEX idx_songs_views ON songs(views DESC);
CREATE INDEX idx_videos_artist ON videos(artist_id);
CREATE INDEX idx_videos_genre ON videos(genre_id);
CREATE INDEX idx_videos_language ON videos(language_id);
CREATE INDEX idx_videos_created ON videos(created_at DESC);
CREATE INDEX idx_videos_year ON videos(year);
CREATE INDEX idx_videos_album ON videos(album_id);
CREATE INDEX idx_videos_views ON videos(views DESC);
CREATE INDEX idx_comments_media ON comments(media_type, media_id);
CREATE INDEX idx_comments_user ON comments(user_id);
CREATE INDEX idx_comments_created ON comments(created_at DESC);
CREATE INDEX idx_ratings_user ON ratings(user_id);
CREATE INDEX idx_favourites_user ON favourites(user_id);
CREATE INDEX idx_play_history_user ON play_history(user_id);
CREATE INDEX idx_play_history_media ON play_history(media_type, media_id);
CREATE INDEX idx_activity_logs_created ON activity_logs(created_at DESC);
CREATE INDEX idx_users_created ON users(created_at DESC);
CREATE INDEX idx_users_email_verified ON users(email_verified);

-- ============================================================
-- VIEWS
-- ============================================================

-- Top rated songs
CREATE OR REPLACE VIEW v_top_rated_songs AS
SELECT s.id, s.title, s.image_url, a.name AS artist_name,
       ROUND(AVG(r.score), 1) AS avg_rating, COUNT(r.id) AS rating_count
FROM songs s
LEFT JOIN ratings r ON r.media_id = s.id AND r.media_type = 'song'
LEFT JOIN artists a ON s.artist_id = a.id
GROUP BY s.id
HAVING avg_rating IS NOT NULL
ORDER BY avg_rating DESC, rating_count DESC;

-- Most played songs
CREATE OR REPLACE VIEW v_most_played_songs AS
SELECT s.id, s.title, s.image_url, s.views, a.name AS artist_name
FROM songs s
LEFT JOIN artists a ON s.artist_id = a.id
ORDER BY s.views DESC;

-- Most viewed videos
CREATE OR REPLACE VIEW v_most_viewed_videos AS
SELECT v.id, v.title, v.image_url, v.views, a.name AS artist_name
FROM videos v
LEFT JOIN artists a ON v.artist_id = a.id
ORDER BY v.views DESC;

-- Most active users
CREATE OR REPLACE VIEW v_most_active_users AS
SELECT u.id, u.username, u.avatar_url,
       (SELECT COUNT(*) FROM comments c WHERE c.user_id = u.id) AS comment_count,
       (SELECT COUNT(*) FROM ratings r WHERE r.user_id = u.id) AS rating_count,
       ((SELECT COUNT(*) FROM comments c WHERE c.user_id = u.id) +
        (SELECT COUNT(*) FROM ratings r WHERE r.user_id = u.id)) AS activity
FROM users u
ORDER BY activity DESC;

-- Dashboard stats
CREATE OR REPLACE VIEW v_dashboard_stats AS
SELECT
  (SELECT COUNT(*) FROM songs) AS total_songs,
  (SELECT COUNT(*) FROM videos) AS total_videos,
  (SELECT COUNT(*) FROM albums) AS total_albums,
  (SELECT COUNT(*) FROM artists) AS total_artists,
  (SELECT COUNT(*) FROM users) AS total_users,
  (SELECT COUNT(*) FROM comments) AS total_comments,
  (SELECT COUNT(*) FROM ratings) AS total_ratings;

-- ============================================================
-- TRIGGERS
-- ============================================================
DELIMITER //

-- Auto-increment views when a play is logged
CREATE TRIGGER IF NOT EXISTS trg_play_history_song
AFTER INSERT ON play_history
FOR EACH ROW
BEGIN
  IF NEW.media_type = 'song' THEN
    UPDATE songs SET views = views + 1 WHERE id = NEW.media_id;
  ELSEIF NEW.media_type = 'video' THEN
    UPDATE videos SET views = views + 1 WHERE id = NEW.media_id;
  END IF;
END//

-- Log user registration
CREATE TRIGGER IF NOT EXISTS trg_user_register
AFTER INSERT ON users
FOR EACH ROW
BEGIN
  INSERT INTO activity_logs (user_id, action, details) VALUES (NEW.id, 'register', CONCAT('New user: ', NEW.username));
END//

DELIMITER ;

-- ============================================================
-- SEED DATA — 75% Hindi, 25% English
-- ============================================================

-- Default admin: username "admin", password "admin123"
INSERT INTO users (username, full_name, address, phone, email, password, role, email_verified) VALUES
('admin', 'Site Administrator', 'SOUND HQ, Mumbai', '9000000000', 'admin@sound.test', '$2y$12$KYdpYZYZgl4u0gUX8ns7B.L/A0A2ioZ2UmtOgbUJvWgefrBriE2QC', 'admin', 1),
('john_doe', 'John Doe', '12 Park Street, Mumbai', '9876543210', 'john@example.test', '$2y$12$KYdpYZYZgl4u0gUX8ns7B.L/A0A2ioZ2UmtOgbUJvWgefrBriE2QC', 'user', 1);

INSERT INTO genres (name) VALUES
('Hindi'),('Bollywood'),('Pop'),('Rock'),('Hip Hop'),('R&B'),('Classical'),('Sufi'),('English'),('Lo-Fi'),('Electronic'),('Punjabi');

INSERT INTO languages (name) VALUES
('Hindi'),('English'),('Punjabi'),('Urdu'),('Tamil'),('Telugu'),('Marathi'),('Bengali');

-- 12 artists: 9 Hindi (75%), 3 English (25%)
INSERT INTO artists (name, bio, image_url) VALUES
('Arijit Singh','Bollywood playback king with a soulful, heart-touching voice.','https://images.pexels.com/photos/352505/pexels-photo-352505.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Shreya Ghoshal','Melodious Bollywood playback singer with golden vocals.','https://images.pexels.com/photos/6201175/pexels-photo-6201175.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Atif Aslam','Soulful Hindi rock and Bollywood singer with unmatched depth.','https://images.pexels.com/photos/15777800/pexels-photo-15777800.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Neha Kakkar','Energetic Bollywood party singer with chart-topping hits.','https://images.pexels.com/photos/7715782/pexels-photo-7715782.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Pritam','Legendary Bollywood music composer behind countless hits.','https://images.pexels.com/photos/8170126/pexels-photo-8170126.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Mohit Chauhan','Soulful indie and Bollywood singer with a unique timbre.','https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Vishal-Shekhar','Dynamic Bollywood music duo creating timeless melodies.','https://images.pexels.com/photos/248963/pexels-photo-248963.jpeg?auto=compress&cs=tinysrgb&w=600'),
('A.R. Rahman','Oscar-winning Indian composer who redefined film music.','https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Jubin Nautiyal','Romantic Hindi ballad singer with a velvety voice.','https://images.pexels.com/photos/38170212/pexels-photo-38170212.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Taylor Swift','Global pop superstar with record-breaking albums.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Ed Sheeran','Singer-songwriter sensation with heartfelt melodies.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600'),
('The Weeknd','R&B and pop icon with a dark, cinematic sound.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600');

-- 12 albums: 9 Hindi (75%), 3 English (25%)
INSERT INTO albums (title, artist_id, year, image_url) VALUES
('Tum Hi Ho', 1, 2024, 'https://images.pexels.com/photos/352505/pexels-photo-352505.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Tera Ban Jaunga', 2, 2024, 'https://images.pexels.com/photos/6201175/pexels-photo-6201175.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Pehli Nazar', 3, 2023, 'https://images.pexels.com/photos/15777800/pexels-photo-15777800.jpeg?auto=compress&cs=tinysrgb&w=600'),
('London Thumakda', 4, 2024, 'https://images.pexels.com/photos/7715782/pexels-photo-7715782.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Barfi', 5, 2023, 'https://images.pexels.com/photos/8170126/pexels-photo-8170126.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Rockstar', 6, 2022, 'https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Chennai Express', 7, 2023, 'https://images.pexels.com/photos/248963/pexels-photo-248963.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Dil Se', 8, 2022, 'https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Lut Gaye', 9, 2024, 'https://images.pexels.com/photos/38170212/pexels-photo-38170212.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Midnights', 10, 2024, 'https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600'),
('Shape', 11, 2023, 'https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600'),
('After Hours', 12, 2023, 'https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600');

-- 24 songs: 18 Hindi (75%), 6 English (25%)
-- Working audio: SoundHelix royalty-free MP3 samples
INSERT INTO songs (title, description, image_url, audio_url, artist_id, album_id, genre_id, language_id, year, duration, is_new, views) VALUES
('Tum Hi Ho','A soulful romantic Hindi ballad that touches the heart.','https://images.pexels.com/photos/352505/pexels-photo-352505.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',1,1,2,1,2024,292,1,8500),
('Chahun Main Ya Na','A beautiful Hindi love song with gentle melodies.','https://images.pexels.com/photos/352505/pexels-photo-352505.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',1,1,2,1,2024,275,0,5200),
('Tera Ban Jaunga','A devotional romantic Hindi song with serene vocals.','https://images.pexels.com/photos/6201175/pexels-photo-6201175.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',2,2,2,1,2024,310,1,7800),
('Raabta','A melodious Hindi track about unspoken connections.','https://images.pexels.com/photos/6201175/pexels-photo-6201175.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3',2,2,2,1,2023,258,0,4100),
('Pehli Nazar Mein','A heart-melting Hindi song about love at first sight.','https://images.pexels.com/photos/15777800/pexels-photo-15777800.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3',3,3,2,1,2023,287,1,6300),
('Tu Jaane Na','A soulful Hindi ballad about unspoken love.','https://images.pexels.com/photos/15777800/pexels-photo-15777800.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3',3,3,2,1,2023,312,0,3900),
('London Thumakda','A fun, upbeat Hindi party song full of energy.','https://images.pexels.com/photos/7715782/pexels-photo-7715782.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3',4,4,2,1,2024,195,1,9200),
('Kala Chashma','A high-energy Hindi dance track that gets everyone moving.','https://images.pexels.com/photos/7715782/pexels-photo-7715782.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3',4,4,2,1,2024,208,0,6700),
('Phir Le Aya Dil','A soulful Hindi ghazal-inspired romantic track.','https://images.pexels.com/photos/8170126/pexels-photo-8170126.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3',5,5,2,1,2023,265,0,3400),
('Barfi','A sweet Hindi melody inspired by the mountains.','https://images.pexels.com/photos/8170126/pexels-photo-8170126.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3',5,5,2,1,2023,232,1,2800),
('Sadda Haq','A powerful Hindi rock anthem about freedom.','https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3',6,6,3,1,2022,245,0,5600),
('Jo Bheji Thi Dua','A hauntingly beautiful Hindi song about longing.','https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3',6,6,2,1,2022,298,1,4800),
('Lungi Dance','A fun Hindi tribute song with infectious energy.','https://images.pexels.com/photos/248963/pexels-photo-248963.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3',7,7,2,1,2023,218,0,7200),
('Titli','A gentle Hindi melody celebrating love and butterflies.','https://images.pexels.com/photos/248963/pexels-photo-248963.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3',7,7,2,1,2023,240,1,3600),
('Dil Se Re','An iconic Hindi track bursting with passion and energy.','https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-15.mp3',8,8,2,1,2022,282,1,8900),
('Chaiyya Chaiyya','A legendary Hindi song that became a global phenomenon.','https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-16.mp3',8,8,2,1,2022,318,0,10500),
('Lut Gaye','A heart-wrenching Hindi romantic ballad.','https://images.pexels.com/photos/38170212/pexels-photo-38170212.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',9,9,2,1,2024,256,1,6800),
('Tum Hi Aana','A soulful Hindi track about waiting for love.','https://images.pexels.com/photos/38170212/pexels-photo-38170212.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',9,9,2,1,2024,278,0,4500),
('Anti-Hero','A catchy English pop anthem about self-acceptance.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',10,10,9,2,2024,200,1,4200),
('Lavender Haze','A dreamy English pop track with atmospheric production.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3',10,10,9,2,2024,215,0,3100),
('Shape of You','A global English hit with an irresistible groove.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3',11,11,9,2,2023,233,1,9500),
('Perfect','A beautiful English love ballad for every romantic moment.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3',11,11,9,2,2023,263,0,5800),
('Blinding Lights','An English synth-pop masterpiece with infectious energy.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3',12,12,9,2,2023,200,1,8200),
('Save Your Tears','An English pop track with a retro 80s vibe.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3',12,12,9,2,2023,215,0,4900);

-- 12 videos: 9 Hindi (75%), 3 English (25%)
-- Working video: Google sample MP4 videos (BigBuckBunny, Sintel, Tears of Steel, etc.)

-- Additional songs for all categories (Rock, Pop, Classical, Hip Hop, Sufi, Punjabi, Electronic, Lo-Fi)
-- Varied per-artist song counts
INSERT INTO songs (title, description, image_url, audio_url, artist_id, album_id, genre_id, language_id, year, duration, is_new, views) VALUES
('Bohemian Rhapsody','An epic rock masterpiece with operatic sections.','https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3',6,6,4,1,2022,354,1,15000),
('Smells Like Teen Spirit','A grunge rock anthem that defined a generation.','https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3',6,6,4,1,2022,301,0,8200),
('Stairway to Heaven','A legendary rock ballad building from soft to powerful.','https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3',6,6,4,1,2022,482,0,9100),
('Shape of My Heart','A contemplative pop rock track about love and loss.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3',11,11,3,2,2023,265,0,4500),
('Sugar','A sweet English pop track with catchy hooks.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3',11,11,3,2,2023,235,1,7800),
('Levitating','A disco-infused English pop dance track.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3',10,10,3,2,2024,203,1,9200),
('Raag Yaman','A serene classical instrumental piece in Raga Yaman.','https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-15.mp3',8,8,7,1,2022,420,0,2100),
('Kun Faya Kun','A devotional Sufi track that stirs the soul.','https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-16.mp3',8,8,8,1,2022,255,1,12000),
('Tum Tak','A beautiful Sufi-inspired romantic Hindi song.','https://images.pexels.com/photos/6201175/pexels-photo-6201175.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',2,2,8,1,2023,298,0,3400),
('Lover','A dreamy English pop love song.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3',10,10,3,2,2024,221,0,5600),
('Dhivehi Remix','An electronic dance track with pulsating beats.','https://images.pexels.com/photos/8170126/pexels-photo-8170126.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3',12,12,11,2,2023,198,1,4300),
('Starlight Lo-Fi','A chill lo-fi beat for late night studying.','https://images.pexels.com/photos/38170212/pexels-photo-38170212.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3',12,12,10,2,2024,185,0,2800),
('Punjabi Wedding','A high-energy Punjabi dance track for celebrations.','https://images.pexels.com/photos/9626651/pexels-photo-9626651.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3',4,4,12,3,2024,192,1,11000),
('Dhol Beats','A rhythmic Punjabi track with infectious dhol beats.','https://images.pexels.com/photos/9626651/pexels-photo-9626651.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3',4,4,12,3,2023,205,0,6700),
('Lose Yourself','A motivational hip hop anthem about seizing the moment.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3',11,11,5,2,2022,326,1,18000),
('Gods Plan','A chart-topping English hip hop track.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3',11,11,5,2,2023,199,0,7500),
('R&B Groove','A smooth R&B track with silky vocals.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3',12,12,6,2,2024,247,0,3900),
('Earned It','A dark sensual English R&B track.','https://images.pexels.com/photos/38170212/pexels-photo-38170212.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3',12,12,6,2,2023,254,1,6200),
('Pop Star Life','An upbeat English pop anthem about fame.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3',10,10,3,2,2024,188,1,5400),
('Highway Tune','A classic rock riff-driven track.','https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3',6,6,4,1,2023,278,0,4800),
('Sufi Soul','A meditative Sufi track with haunting vocals.','https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3',8,8,8,1,2023,310,0,4200),
('Electronic Dreams','An atmospheric electronic track for late nights.','https://images.pexels.com/photos/8170126/pexels-photo-8170126.jpeg?auto=compress&cs=tinysrgb&w=600','https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3',12,12,11,2,2024,215,1,3600);

INSERT INTO videos (title, description, image_url, video_url, album_id, genre_id, language_id, year, duration, is_new, views) VALUES
('Tum Hi Ho — Music Video','The official emotional music video for the Hindi romantic ballad.','https://images.pexels.com/photos/352505/pexels-photo-352505.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',1,2,1,2024,292,1,12000),
('Tera Ban Jaunga — Live','A breathtaking live performance of the Hindi devotional romance.','https://images.pexels.com/photos/6201175/pexels-photo-6201175.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',2,2,1,2024,310,1,8900),
('Pehli Nazar — Lyric Video','Sing along to the soulful Hindi love at first sight track.','https://images.pexels.com/photos/15777800/pexels-photo-15777800.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',3,2,1,2023,287,0,6700),
('London Thumakda — Party Video','A high-energy Hindi party video that gets everyone dancing.','https://images.pexels.com/photos/7715782/pexels-photo-7715782.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',4,2,1,2024,195,1,14000),
('Barfi — Music Video','A sweet Hindi melody video shot in beautiful mountain locations.','https://images.pexels.com/photos/8170126/pexels-photo-8170126.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',5,2,1,2023,232,0,5200),
('Sadda Haq — Rock Video','A powerful Hindi rock anthem video about freedom and rebellion.','https://images.pexels.com/photos/5014756/pexels-photo-5014756.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyrides.mp4',6,3,1,2022,245,1,7800),
('Lungi Dance — Dance Video','The viral Hindi tribute dance video with infectious choreography.','https://images.pexels.com/photos/248963/pexels-photo-248963.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',7,2,1,2023,218,0,9500),
('Dil Se Re — Classic Video','The iconic Hindi music video that defined a generation.','https://images.pexels.com/photos/4218027/pexels-photo-4218027.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',8,2,1,2022,282,1,11000),
('Lut Gaye — Romantic Video','A heart-wrenching Hindi romantic video that went viral.','https://images.pexels.com/photos/38170212/pexels-photo-38170212.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4',9,2,1,2024,256,1,8500),
('Anti-Hero — Official Video','The colorful English pop music video for the chart-topping hit.','https://images.pexels.com/photos/3563172/pexels-photo-3563172.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',10,9,2,2024,200,1,6500),
('Shape of You — Music Video','The global English hit video that broke all streaming records.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/VolkswagenGTIReview.mp4',11,9,2,2023,233,0,9800),
('Blinding Lights — Visualizer','A neon-soaked English visualizer for the synth-pop masterpiece.','https://images.pexels.com/photos/7715754/pexels-photo-7715754.jpeg?auto=compress&cs=tinysrgb&w=600','https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4',12,9,2,2023,200,1,7200);

INSERT INTO comments (user_id, media_type, media_id, rating) VALUES
(2,'song',1,5),
(2,'song',3,5),
(2,'song',5,4),
(2,'song',7,5),
(2,'song',15,5),
(2,'song',17,4),
(2,'video',1,5),
(2,'video',4,5),
(2,'video',8,4),
(2,'song',21,5),
(2,'song',23,4),
(2,'video',11,5);

INSERT INTO ratings (user_id, media_type, media_id, score) VALUES
(2,'song',1,5),(2,'song',3,5),(2,'song',5,4),(2,'song',7,5),
(2,'song',15,5),(2,'song',17,4),(2,'song',9,4),(2,'song',11,5),
(2,'video',1,5),(2,'video',4,5),(2,'video',8,4),
(2,'song',21,5),(2,'song',23,4),(2,'song',19,4),
(2,'video',11,5),(2,'video',12,4);

INSERT INTO favourites (user_id, media_type, media_id) VALUES
(2,'song',1),(2,'song',7),(2,'song',15),(2,'song',21),
(2,'video',1),(2,'video',4),(2,'video',8),(2,'song',17);

-- ============================================================
-- DONE — Database is ready. Admin login: admin / admin123
-- ============================================================
