# Online Event Management System

A fully functional web-based Online Event Management System built from scratch with **PHP and MySQL** for the backend and **HTML5, CSS3, and JavaScript** for the frontend. No CSS frameworks (Bootstrap, Tailwind, etc.) and no CMS (WordPress) are used — all components are hand-coded.

## Features

### 1. User Registration & Authentication
- User registration with Full Name, Email, Password, and Profile Picture
- Unique email validation
- Password hashing using `password_hash()` / `password_verify()`
- Secure login and logout with `$_SESSION` handling
- Forgot password with time-limited reset tokens
- Session-based access control on restricted pages

### 2. Dashboard & Event Management
- View upcoming events ordered by date/time
- Create, edit, and delete events (owners only)
- Event timestamps (created_at, updated_at)
- Correct event ordering (ascending by event date)

### 3. User Profile & Search
- Profile display with avatar, name, email, and join date
- Update profile name and profile picture
- Search users and events
- View-only profile access for other users' profiles

### 4. Private Messaging System
- Send private messages stored in a separate `messages` table
- Conversation view layout
- Access control: users can only message/view conversations they participate in

### 5. Security
- PDO prepared statements throughout (SQL injection prevention)
- XSS protection via `htmlspecialchars()` output escaping
- Input validation and sanitization
- Secure file uploads (extension, MIME type, and size checks; unique filenames)
- Restricted page protection (redirects unauthenticated users)

### 6. Front-End Interactivity
- JavaScript form validation (required fields, password match, email format)
- Image preview before upload
- Live search for users and events (AJAX/fetch)
- Clean custom UI (no CSS frameworks)

## Project Structure

```
online-event-management-system/
├── index.php                 # Entry point (redirects to dashboard/login)
├── register.php              # User registration
├── login.php                 # User login
├── logout.php                # User logout
├── forgot_password.php       # Password reset request
├── reset_password.php        # Password reset with token
├── dashboard.php             # Dashboard + upcoming events
├── events.php                # All events list
├── create_event.php          # Create new event
├── edit_event.php?id=        # Edit event (owner only)
├── delete_event.php?id=      # Delete event (owner only)
├── profile.php?id=           # View profile
├── edit_profile.php          # Update profile
├── search.php                # Search users & events
├── messages.php              # Conversation list
├── conversation.php?id=      # Conversation view
├── send_message.php          # Send message handler
├── database.sql              # MySQL schema
├── README.md                 # This file
├── includes/
│   ├── db.php                # PDO connection
│   ├── auth.php              # Session & auth helpers
│   ├── functions.php         # Helpers (escape, sanitize, upload, flash)
│   ├── header.php            # Shared HTML header/nav
│   └── footer.php            # Shared HTML footer
├── css/
│   └── style.css             # Custom styles (no frameworks)
├── js/
│   └── main.js               # Validation, image preview, live search
└── uploads/
    └── profile_pics/         # Uploaded profile pictures
```

## Setup Instructions

### Prerequisites
- PHP 7.4 or newer (PDO, password_hash supported)
- MySQL / MariaDB
- Apache / Nginx (or PHP's built-in server)

### 1. Import the database
Create the database and tables by importing `database.sql`:

```bash
mysql -u root -p < database.sql
```

Or import via phpMyAdmin: create a database named `event_management`, then run the SQL.

### 2. Configure database connection
Open `includes/db.php` and update the credentials if needed:

```php
$username = 'root';
$password = 'your_mysql_password';
```

### 3. Ensure the uploads folder is writable
The web server must be able to write to `uploads/profile_pics/`.

### 4. Run the application
Point your web server at the project folder, or use PHP's built-in server:

```bash
php -S localhost:8000 -t .
```

Then visit `http://localhost:8000` in your browser.

## Default Behavior
- There is no pre-created admin account. Register a new account at `/register.php` to get started.
- Password reset links are displayed on screen (no mail server configured), so you can copy the `reset_password.php?token=...` URL directly to test the reset flow.

## Security Notes
- All SQL queries use PDO prepared statements.
- User-supplied output is escaped with `htmlspecialchars()` to prevent XSS.
- Profile pictures are validated by extension, MIME type, and size (max 2 MB), then saved with a unique filename.
- Session IDs are regenerated on login to mitigate session fixation.

## License
Educational project — INTERNET PROGRAMMING 622 Assignment.