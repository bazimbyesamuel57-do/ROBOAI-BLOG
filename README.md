## Course
- Course: Project: Getting Started in Web Programming (DLBITPEWP01_E)
- Student: Bazimbye Samuel | ID: 10746591
- Tutor: Andrew Adjah Sai

## Project Phases
- Phase 1: Concept document
- phase 2:Development
- phase 3: Finalization

# PHASE 1:CONCEPTION PHASE
# ROBOAI-BLOG
A blog for robotics and AI trends, articles and research, built with PHP and MSQL.
## Purpose
RoboAI is a tech blog focused on Robotics and Artificial Intelligence trends. 
It provides articles and insights on the fast-growing influence of AI and Robotics 
in everyday life — exploring both their positive and negative implications. 
The blog targets developers, computer scientists, students and researchers.

## Features

### Visitor
- View a list of posts on the homepage
- Search for posts using the search bar
- Filter and browse posts by category
- View full post content on the Post Detail page
- Like a post
- Leave a comment on a post
- View blog information on the About Us page
- View contact information on the Contact page

### Admin
- Log in with secure credentials
- Create, edit and delete posts
- Manage categories and tags
- View, flag and delete comments
- View blog stats from the dashboard
- Log out to end the admin session

## Tech Stack
| Backend | PHP |
| Database | MySQL |
| Frontend | Server-Side Rendering (PHP renders HTML) |
| Styling | Bootstrap + custom CSS |
| File Storage | Server File System (uploaded images) |

## Security
- Single hard-coded admin account (no user registration)
- Admin password stored hashed in server-side config
- Admin panel accessible only after successful login
- All forms protected against SQL injection

## Data Model

### Admin
| admin_id | INT (PK) |
| username | VARCHAR |
| password | VARCHAR (hashed) |

### Post
| post_id | INT (PK) |
| admin_id | INT (FK → Admin) |
| category_id | INT (FK → Category) |
| title | VARCHAR |
| excerpt | TEXT |
| body | TEXT |
| author | VARCHAR |
| date | DATETIME |
| likes_count | INT |
| image_path | VARCHAR |
| status | ENUM('draft','published') |

### Comment
| comment_id | INT (PK) |
| post_id | INT (FK → Post) |
| body | TEXT |
| author_name | VARCHAR |
| date | DATETIME |
| is_flagged | TINYINT(1) |
| likes_count | INT |

### Category
| category_id | INT (PK) |
| name | VARCHAR |

### Tag
| tag_id | INT (PK) |
| tag_name | VARCHAR |

### POST_TAGS (Junction Table)

| post_id | INT (FK → Post) |
| tag_id | INT (FK → Tag) |

## Relationships
| Admin → Post | 1-to-many |
| Category → Post | 1-to-many |
| Post → Comment | 1-to-many |
| Post ↔ Tag | Many-to-many (via POST_TAGS) |
