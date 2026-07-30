# SOUND Entertainment — Documentation

This folder contains the project documentation for the SOUND Entertainment website.

## Contents
- `ER_DIAGRAM.md` — Entity-Relationship diagram of the database
- `PROJECT_REPORT.md` — Full project report (SRS-style)
- `USER_MANUAL.md` — How to use the website

## Diagrams Overview

### Entity-Relationship (ER) Diagram
See `ER_DIAGRAM.md` for the full ER diagram showing all tables and relationships.

### Data Flow Diagram (DFD)
```
[User] --> (Register/Login) --> [Session] --> (Browse/Search) --> [Songs/Videos]
[User] --> (Add Review) --> [Comments]
[User] --> (Rate) --> [Ratings]
[User] --> (Favourite) --> [Favourites]
[User] --> (Create Playlist) --> [Playlists] --> [Playlist Items]
[Admin] --> (Manage Content) --> [Songs/Videos/Albums/Artists/Genres]
[Admin] --> (Manage Users) --> [Users]
[Visitor] --> (Subscribe) --> [Newsletter]
```

### Use Case Diagram
```
                    +-------------------+
                    |    SOUND System   |
                    +-------------------+
                           |
        +------+----------+----------+------+
        |      |          |          |      |
    [Browse] [Search] [Register] [Login] [View Detail]
        |      |          |          |
    [Filter] [Rate]   [Profile]  [Dashboard]
        |      |          |
    [Play] [Review]  [Playlists]
                          |
                    [Admin Panel]
                          |
              +-----------+-----------+
              |           |           |
        [Manage Songs] [Manage Users] [Manage Categories]
```
