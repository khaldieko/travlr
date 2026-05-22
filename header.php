<?php
/*
 * header.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * Every page includes this file with a single require_once. It starts
 * the session, loads the shared functions, prints the page <head>, and
 * shows a navigation bar that changes depending on whether the visitor
 * is logged in.
 */

/* Start the session only if one is not already running. A page such as
   login.php starts its own session before including this file, and
   starting it twice would trigger a PHP notice. */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

/* Work out whether someone is logged in for this session. */
if (isset($_SESSION['user'])) {
    $loggedin = true;
    $user     = $_SESSION['user'];
} else {
    $loggedin = false;
    $user     = '';
}

/* Allow each page to set its own browser title before including this
   file. If it has not, fall back to a default title. */
if (!isset($page_title)) {
    $page_title = 'Travlr';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h($page_title); ?></title>
    <link rel="stylesheet" href="styles.css">
    <script src="javascript.js" defer></script>
</head>
<body>

<header class="site-header">
    <a class="logo" href="index.php">
        <span class="logo-pin">&#9992;</span> Travlr
    </a>
    <div class="user-badge">
        <?php echo $loggedin
            ? 'Logged in as <strong>' . h($user) . '</strong>'
            : 'Welcome, Guest'; ?>
    </div>
</header>

<nav class="main-nav">
<?php if ($loggedin): ?>
    <a href="index.php">Home</a>
    <a href="members.php">Discover</a>
    <a href="friends.php">Friends</a>
    <a href="messages.php">Messages</a>
    <a href="profile.php">Edit Profile</a>
    <a href="logout.php">Log Out</a>
<?php else: ?>
    <a href="index.php">Home</a>
    <a href="signup.php">Sign Up</a>
    <a href="login.php">Log In</a>
<?php endif; ?>
</nav>

<main class="content">
