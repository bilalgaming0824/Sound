# 🎵 SOUND — Music & Video Entertainment Platform

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)
![Status](https://img.shields.io/badge/Status-Active-success)

A full-featured music and video streaming platform built with PHP, MySQL, Bootstrap 5, and vanilla JavaScript. Features a modern dark-themed UI with smooth animations, comprehensive admin dashboard, and industry-standard security.

---

## ✨ Features

### User Features
- 🎧 **Music Streaming** — Stream songs across genres and languages
- 🎬 **Video Streaming** — Watch music videos in high quality
- ❤️ **Favourites** — Save your favourite songs and videos
- 📋 **Playlists** — Create and manage custom playlists (Workout, Gaming, Study, etc.)
- 🕐 **Recently Played** — Auto-tracks your listening history
- ⭐ **Ratings & Reviews** — Rate songs 1-5 stars and post reviews
- 👤 **Profile** — Upload profile picture, edit bio and details
- 📰 **Newsletter** — Subscribe for updates
- 🔐 **Email Verification** — Account activation via email
- 🔑 **Forgot Password** — Secure password reset flow

### Admin Features
- 📊 **Dashboard Charts** — Songs/Videos upload trends, Users growth, Top genres (Chart.js)
- 📈 **Analytics** — Most played songs, Top rated, Most viewed videos, Most active users
- 🔔 **Notifications** — Bell icon with new user, review, and video alerts
- 🎵 **Content Management** — Full CRUD for songs, videos, albums, artists, categories
- 📝 **Activity Logs** — Every admin action logged with timestamp
- 👥 **User Management** — View, edit, and manage all users

### Security Features
- ✅ **Prepared Statements** (PDO) — SQL injection protection
- ✅ **XSS Protection** — `htmlspecialchars()` on all output
- ✅ **CSRF Tokens** — On every form submission
- ✅ **Session Regeneration** — Prevents session fixation
- ✅ **Password Hashing** — bcrypt via `password_hash()`
- ✅ **Login Rate Limiting** — Max 5 attempts per 15 minutes
- ✅ **File Upload Security** — MIME type check, size limit, random filenames, extension blocking

### UI/UX Features
- 🎨 **Modern Dark Theme** — Glassmorphism, gradients, smooth transitions
- 🔔 **Toast Notifications** — Slide-in toasts replacing `alert()`
- 📑 **Breadcrumbs** — Navigation trail on every page
- ⬆️ **Back to Top** — Smooth scroll button
- ⏳ **Page Loader** — Animated loading screen
- 💀 **Skeleton Loading** — Facebook-style shimmer placeholders
- 📱 **Fully Responsive** — Mobile, tablet, desktop breakpoints
- 🔍 **Advanced Search** — Filter by type, genre, language, year in one box
- 📄 **Pagination** — 12 items per page with numbered navigation

### SEO Features
- ✅ Meta tags (description, keywords, author)
- ✅ OpenGraph tags
- ✅ Twitter Card tags
- ✅ `robots.txt`
- ✅ Dynamic `sitemap.xml`
- ✅ Favicon

---

## 📁 Folder Structure

```
Sound-main/
├── admin/                  # Admin panel
│   ├── includes/           # Admin header & footer
│   ├── albums.php          # Manage albums
│   ├── analytics.php       # Analytics dashboard
│   ├── artists.php         # Manage artists
│   ├── categories.php      # Manage genres/languages
│   ├── comments.php        # Manage reviews
│   ├── index.php           # Admin dashboard with charts
│   ├── logs.php            # Activity logs
│   ├── newsletter.php      # Newsletter subscribers
│   ├── songs.php           # Manage songs
│   ├── users.php           # Manage users
│   └── videos.php          # Manage videos
├── api/                    # AJAX endpoints
│   ├── favourite.php       # Toggle favourite
│   ├── newsletter.php      # Newsletter subscribe
│   ├── playlist_add.php    # Add song/video to playlist
│   ├── rate.php            # Star rating
│   └── search_suggest.php  # Live search suggestions
├── assets/
│   ├── css/
│   │   ├── style.css       # Main stylesheet
│   │   └── admin.css       # Admin panel styles
│   ├── js/
│   │   ├── app.js          # Frontend JavaScript
│   │   └── admin.js        # Admin JavaScript
│   └── img/
│       └── favicon.svg
├── config/
│   └── config.php          # Database & site configuration
├── database/
│   └── sound_entertainment.sql  # Complete schema + indexes + views + triggers + seed data
├── docs/                   # Documentation
│   ├── ER_DIAGRAM.md
│   ├── PROJECT_REPORT.md
│   └── USER_MANUAL.md
├── includes/
│   ├── functions.php       # Core functions (auth, security, helpers)
│   ├── header.php          # Site header & navigation
│   ├── footer.php          # Site footer
│   ├── media_card.php      # Reusable media card component
│   └── models.php          # Database models & queries
├── uploads/                # User-uploaded files (gitignored, created at runtime)
│   ├── covers/             # Uploaded cover images
│   ├── songs/              # Uploaded song files
│   └── videos/             # Uploaded video files
├── public/                 # Static assets (images only, no large media)
│   └── images/             # WebP cover art, artist photos, etc.
├── about.php               # About page
├── album_detail.php        # Album detail page
├── albums.php              # Browse albums
├── artist_detail.php       # Artist detail page
├── artists.php             # Browse artists
├── categories.php          # Browse categories
├── category.php           # Category detail page
├── contact.php             # Contact form
├── dashboard.php           # User dashboard
├── faq.php                 # FAQ page
├── forgot_password.php     # Password reset request
├── index.php               # Homepage
├── login.php               # Sign in
├── logout.php              # Sign out
├── music.php               # Browse music
├── playlist_detail.php     # Playlist detail page
├── playlists.php           # Browse playlists
├── profile.php             # User profile
├── register.php            # Create account
├── robots.txt              # Search engine rules
├── search.php              # Advanced search
├── sitemap.php             # Dynamic XML sitemap
├── song_detail.php         # Song detail page
├── terms.php               # Terms & Privacy
├── verify_email.php        # Email verification page
├── video_detail.php        # Video detail page
├── videos.php              # Browse videos
├── watch_video.php         # Video player page
├── 404.php                 # 404 error page
└── 500.php                 # 500 error page
```

---

## 🚀 Installation Guide

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx (XAMPP/WAMP recommended)

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/bilalgaming0824/Sound.git
   ```

2. **Move to your web server directory**
   ```bash
   # For XAMPP:
   mv Sound /opt/lampp/htdocs/
   # For WAMP:
   mv Sound C:/wamp/www/
   ```

3. **Import the database**
   - Open phpMyAdmin
   - Import `database/sound_entertainment.sql` — this single file creates the database, all tables, indexes, views, triggers, and seed data automatically

4. **Configure database connection**
   - Open `config/config.php`
   - Update `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` if different from defaults
   - Update `BASE_URL` to match your folder name

5. **Set up upload directories**
   ```bash
   mkdir -p uploads/covers uploads/songs uploads/videos
   chmod 755 uploads/
   ```

6. **Access the site**
   - Visit `http://localhost/Sound/` in your browser

7. **Admin login**
   - Username: `admin`
   - Password: `admin123`

---

## 📖 Documentation

- **ER Diagram**: `docs/ER_DIAGRAM.md`
- **Project Report**: `docs/PROJECT_REPORT.md`
- **User Manual**: `docs/USER_MANUAL.md`

### Database Design
- 15+ tables with foreign keys, indexes, and constraints
- Cascade delete on user-related tables
- Unique keys on ratings, favourites, and newsletter
- Database views for analytics (top rated, most played, most viewed, active users)
- Triggers for auto-incrementing view counts and logging registrations

---

## 🛠️ Tech Stack

| Technology | Purpose |
|------------|---------|
| PHP 8 | Backend logic |
| MySQL 8 | Database |
| PDO | Database abstraction (prepared statements) |
| Bootstrap 5.3 | UI framework |
| Bootstrap Icons | Icon set |
| Chart.js | Dashboard charts |
| Vanilla JS | Frontend interactivity |

---

## 🔒 Security

- All database queries use PDO prepared statements
- All user output passed through `htmlspecialchars()`
- CSRF tokens on every form
- bcrypt password hashing
- Session regeneration on login
- Login rate limiting (5 attempts / 15 minutes)
- File upload validation (MIME type, size, extension, random filename)

---

## 👥 Contributors

- **Bilal** — Developer & Designer

---

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## 📸 Screenshots

> Add screenshots of each page here after setting up the project:
> - Homepage
> - Music browse
> - Video browse
> - Song detail
> - Video player
> - Admin dashboard
> - Admin analytics
> - User profile
> - Login/Register
> - Search results

---

<p align="center">Built with ❤️ for music lovers</p>
