# Travlr

**A travel-focused social networking web application.**

Travlr is a social network built for travellers. Members create a profile,
follow other explorers, and share their journeys through public and private
messages. This project was built for a Web Programming course.

---

## Features

- **Sign up** with live username-availability checking (asynchronous request)
- **Log in / log out** with secure, hashed passwords and session control
- **User profiles** with a travel bio and an uploaded photo (auto-resized to a thumbnail)
- **Discover** page – a directory of every member
- **Follow / unfollow** other travellers, with mutual-friend detection
- **Friends** page – mutual friends, followers, and following
- **Public and private messaging** on member message walls
- **Responsive design** that works on phones, tablets, and desktops

---

## Technology

- **PHP** (server-side logic, PDO with prepared statements)
- **MySQL** (relational database)
- **HTML5 and CSS3** (structure and a custom responsive stylesheet)
- **JavaScript** (asynchronous username check via the Fetch API)
- **PHP GD library** (resizing uploaded profile photos)

---

## Project structure

| File | Purpose |
|------|---------|
| `functions.php` | Database connection and shared helper functions |
| `header.php` / `footer.php` | Shared page layout and navigation |
| `setup.php` | Creates the four database tables (run once) |
| `index.php` | Home page / member dashboard |
| `signup.php` / `checkuser.php` | Registration and live username check |
| `login.php` / `logout.php` | Authentication |
| `profile.php` | Edit travel bio and upload a profile photo |
| `members.php` | Member directory + follow / unfollow |
| `friends.php` | Mutual friends, followers, and following |
| `messages.php` | Public and private message walls |
| `styles.css` | Stylesheet |
| `javascript.js` | Client-side helpers |
| `uploads/` | Stores uploaded profile photos |

---

## Database tables

| Table | Columns |
|-------|---------|
| `members` | user, pass (hashed), joined |
| `profiles` | user, text |
| `friends` | follower, followee |
| `messages` | id, auth, recip, pm, time, message |

---

## How to run it locally

1. **Install a local server** such as [XAMPP](https://www.apachefriends.org/)
   (provides Apache, MySQL, and PHP).
2. **Copy the project** into your web root, in a folder named `travlr`
   (for XAMPP this is `xampp/htdocs/travlr`).
3. **Create the database.** Open phpMyAdmin (`http://localhost/phpmyadmin`)
   and create a new database called `travlr`.
4. **Check the settings** at the top of `functions.php` and make sure the
   database name, user, and password match your environment. The defaults
   (`root` / no password) work with a standard XAMPP install.
5. **Run the setup script** once by visiting
   `http://localhost/travlr/setup.php`. This creates the four tables.
6. **Open the app** at `http://localhost/travlr/`.

---

## Notes

- Passwords are stored as secure hashes using PHP's `password_hash()`.
- All database access uses PDO prepared statements to prevent SQL injection.
- All user-supplied content is escaped on output to prevent XSS.
