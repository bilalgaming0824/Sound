# SOUND Entertainment — Project Report

## 1. Introduction

SOUND is a web-based entertainment platform that hosts music and videos across English and regional languages. The platform allows users to browse, search, stream, review, and rate content organized by album, artist, year, genre, and language. An admin panel provides full content and user management.

This project fulfills the requirements of the Aptech eProject for the SOUND Group entertainment website.

## 2. Objectives

- Build a complete, real-world entertainment web application.
- Implement user registration, login, and profile management with validation.
- Allow users to search music and videos by name, artist, year, album, genre, and language.
- Enable users to add and modify reviews and ratings.
- Provide an admin panel for managing all content, categories, and users.
- Apply proper security measures (SQL injection protection, XSS protection, CSRF tokens, password hashing).
- Deliver a premium, responsive user interface.

## 3. Problem Statement

Music and videos are the most common sources of entertainment today. SOUND Group wants to host a website for entertainment, hosting new and old videos and songs in both regional and English languages.

The website should have a proper menu structure, and music and videos should be arranged by album, artist, year, genre, and language. Users should have the option of reviewing and rating the music and videos available.

The home page should have information about the site and a section for the latest music and videos with 5 listings in each. A "New" icon should flash alongside new additions.

### Customer Specifications
- The website hosts songs and videos with images, descriptions, and a "New" badge for recent additions.
- Three user roles: Administrator and User.
- Administrator can: add/delete music, add/delete videos, create categories (year, artist, album, etc.), manage users, and manage website content.
- Users can: register with a unique username, search by name/artist/year/album, add/modify reviews, and add/modify ratings.
- Name, address, phone, and email are mandatory. Validations are applied on all data fields.

## 4. Hardware & Software Requirements

### Hardware
- A computer with at least 2GB RAM
- Internet connection

### Operating System
- Windows / macOS / Linux

### Software
- **PHP** 8.0 or higher
- **MySQL** / MariaDB
- **Apache** web server (via XAMPP/WAMP/MAMP)
- **Bootstrap** 5 (CDN)
- A modern web browser (Chrome, Firefox, Edge, Safari)

## 5. System Design

### 5.1 Architecture
The application follows a classic **LAMP-style** architecture:
- **Presentation layer:** HTML + CSS + Bootstrap + JavaScript
- **Business logic layer:** PHP scripts handling routing, validation, and database access
- **Data layer:** MySQL database accessed via PDO (PHP Data Objects)

### 5.2 Database Schema
The database `sound_entertainment` contains 14 tables:
- `users` — accounts with role-based access
- `artists`, `genres`, `languages`, `albums` — categorization
- `songs`, `videos` — media content
- `comments`, `ratings` — user feedback
- `favourites`, `playlists`, `playlist_items` — user collections
- `play_history` — recently played tracking
- `newsletter` — email subscribers

See `ER_DIAGRAM.md` for the full schema.

### 5.3 User Roles

#### Administrator
- Manage songs (add, delete)
- Manage videos (add, delete)
- Manage albums (add, delete)
- Manage artists (add, delete)
- Manage categories (genres, languages)
- Manage users (promote/demote, delete)
- Manage comments (delete)
- View newsletter subscribers
- View dashboard analytics

#### User
- Register and create an account (unique username, mandatory fields)
- Login and logout
- Search music and videos
- Add and modify reviews
- Add and modify ratings
- Favourite songs and videos
- Create, edit, and delete playlists
- View recently played history
- Edit profile and change password

## 6. Security Implementation

### SQL Injection Protection
All database queries use **PDO prepared statements** with bound parameters. No user input is ever concatenated directly into SQL.

### XSS Protection
All dynamic output is escaped using `htmlspecialchars()` with `ENT_QUOTES` and `UTF-8` encoding via the `e()` helper function.

### CSRF Protection
Every form includes a hidden CSRF token generated with `bin2hex(random_bytes(32))` and verified on submission using `hash_equals()` to prevent timing attacks.

### Password Security
Passwords are hashed using PHP's `password_hash()` with the bcrypt algorithm. Plaintext passwords are never stored.

### Session Management
Sessions are started securely. Authentication state is stored in `$_SESSION`. Protected pages call `require_login()` or `require_admin()` to enforce access control.

### Input Validation
All form inputs are validated server-side:
- Username: 3+ characters, alphanumeric + underscore, unique
- Email: valid format, unique
- Phone: 7-16 digits with allowed symbols
- Password: minimum 6 characters, must match confirmation
- All mandatory fields (name, address, phone, email) are checked

## 7. Features Delivered (by Phase)

| Phase | Feature | Status |
|-------|---------|--------|
| 1 | Project setup, config, header/navbar/footer, common CSS/JS | Done |
| 2 | Home page (hero, featured, trending, albums, artists, categories, CTA, newsletter) | Done |
| 3 | Music module (search, filters, cards, hover effects) | Done |
| 4 | Song details (player, description, lyrics, related, comments, rating, share, download) | Done |
| 5 | Videos module (search, filters, cards, details, player, suggested, comments) | Done |
| 6 | Albums (listing, details, songs) | Done |
| 7 | Artists (listing, profile, bio, songs, videos, albums, social) | Done |
| 8 | Categories (genres browsing) | Done |
| 9 | Global search (songs, videos, albums, artists, live suggestions) | Done |
| 10 | Playlists (create, edit, delete, items) | Done |
| 11 | Authentication (register, login, logout, profile) | Done |
| 12 | User dashboard (overview, favourites, playlists, history) | Done |
| 13 | Admin panel (songs, videos, albums, artists, categories, users, comments, newsletter) | Done |
| 14 | Database (14 tables with seed data) | Done |
| 15 | Premium features (animations, back-to-top, skeleton, responsive) | Done |
| 16 | Security (CSRF, SQL injection, XSS, validation, sessions) | Done |
| 17 | Optimization (responsive, SEO meta, lazy loading, caching headers) | Done |
| 18 | Documentation (ER diagram, project report, user manual) | Done |

## 8. Testing

### Test Cases
| # | Test | Expected Result |
|---|------|----------------|
| 1 | Visit home page | Hero, latest 5 songs, 5 videos, trending, albums, artists, categories display |
| 2 | Search "Neon" | Returns matching songs and videos |
| 3 | Filter music by genre "Pop" | Only Pop songs appear |
| 4 | Register with empty fields | Validation errors shown |
| 5 | Register with valid data | Account created, logged in |
| 6 | Login with wrong password | Error: invalid credentials |
| 7 | Login as admin | Admin panel link appears |
| 8 | Add a song as admin | Song appears in library |
| 9 | Rate a song (5 stars) | Rating updates, average recalculates |
| 10 | Post a review | Review appears in list |
| 11 | Add to favourites | Heart fills, appears in dashboard |
| 12 | Create playlist | Playlist appears in playlists page |
| 13 | Delete a comment as admin | Comment removed |
| 14 | Toggle user role as admin | Role changes between user/admin |
| 15 | Visit invalid URL | 404 page displays |

## 9. Conclusion

The SOUND Entertainment website successfully delivers a premium, full-featured music and video streaming platform. It meets all the requirements specified in the problem statement, including user roles, content management, search, reviews, ratings, and proper security. The responsive design works across desktop, tablet, and mobile devices.
