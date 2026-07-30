# SOUND Entertainment — ER Diagram

## Entity Relationship Diagram

```
+-------------------+
|   play_history    |
+-------------------+
| id (PK)           |
| user_id (FK)-----+|
| media_type        |
| media_id          |
| played_at         |              v
+-------------------+      +-------------------+
                           |     songs        |
+-------------------+      +-------------------+
|   playlists      |      | id (PK)           |
+-------------------+      | title             |
| id (PK)           |      | description       |
| user_id (FK)-----+|      | image_url         |
| name              |      | audio_url         |
| description       |      | artist_id (FK)----+
| created_at        |      | album_id (FK)-----+
+-------------------+      | genre_id (FK)-----+
        | 1                | language_id (FK)--+
        v                  | year              |
+-------------------+      | duration          |
| playlist_items    |      | is_new            |
+-------------------+      | views             |
| id (PK)           |      | created_at        |
| playlist_id (FK)-+|      +-------------------+
| song_id (FK)-----+|              |
| video_id (FK)----+|              |
| added_at          |              |
+-------------------+              |
                                   |
+-------------------+              |
|   favourites      |              |
+-------------------+              |
| id (PK)           |              |
| user_id (FK)-----+|              |
| media_type        |              |
| media_id          |              |
| created_at        |              |
+-------------------+              |
                                   |
+-------------------+              |
|   comments        |              |
+-------------------+              |
| id (PK)           |              |
| user_id (FK)-----+|              |
| media_type        |              |
| media_id          |              |
| rating (0-5)      |              |
| comment           |              |
| created_at        |              |
+-------------------+              |
                                   |
+-------------------+              |
|   ratings        |              |
+-------------------+              |
| id (PK)           |              |
| user_id (FK)-----+|              |
| media_type        |              |
| media_id          |              |
| score (1-5)       |              |
| created_at        |              |
+-------------------+              |
                                   |
+-------------------+      +-------+----------+
|   newsletter     |      | genres | languages|
+-------------------+      +-------------------+
| id (PK)           |      | id (PK)           |
| email (UQ)        |      | name (UQ)         |
| subscribed_at     |      +-------------------+
+-------------------+
```

## Relationships

| From | To | Type | Description |
|------|----|----|-------------|
| users | comments | 1:N | A user writes many comments |
| users | ratings | 1:N | A user rates many items |
| users | favourites | 1:N | A user favourites many items |
| users | playlists | 1:N | A user creates many playlists |
| users | play_history | 1:N | A user has many history entries |
| artists | albums | 1:N | An artist has many albums |
| artists | songs | 1:N | An artist has many songs |
| artists | videos | 1:N | An artist has many videos |
| albums | songs | 1:N | An album contains many songs |
| albums | videos | 1:N | An album contains many videos |
| genres | songs | 1:N | A genre has many songs |
| genres | videos | 1:N | A genre has many videos |
| languages | songs | 1:N | A language has many songs |
| languages | videos | 1:N | A language has many videos |
| playlists | playlist_items | 1:N | A playlist contains many items |
| songs | playlist_items | 1:N | A song can be in many playlists |
| videos | playlist_items | 1:N | A video can be in many playlists |

## Tables Summary

| Table | Purpose |
|-------|---------|
| `users` | Registered users (user/admin roles) |
| `artists` | Music artists/bands |
| `genres` | Music categories (Pop, Rock, etc.) |
| `languages` | Song/video languages |
| `albums` | Music albums |
| `songs` | Individual song tracks |
| `videos` | Video tracks |
| `comments` | User reviews on songs/videos |
| `ratings` | User star ratings (1-5) |
| `favourites` | User's favourite songs/videos |
| `playlists` | User-created playlists |
| `playlist_items` | Songs/videos in a playlist |
| `play_history` | Recently played items |
| `newsletter` | Email subscribers |
