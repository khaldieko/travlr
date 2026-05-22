<?php
/*
 * setup.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * Run this file ONCE in your browser (http://localhost/travlr/setup.php)
 * before using the rest of the application. It creates the four MySQL
 * tables the app needs. Because each table is created only if it does
 * not already exist, this file is safe to run more than once.
 */

require_once 'functions.php';

/* members - one row per registered account.
   The password is stored as a secure hash, never as plain text. */
create_table('members', "
    user   VARCHAR(32)  NOT NULL,
    pass   VARCHAR(255) NOT NULL,
    joined DATETIME     NOT NULL,
    PRIMARY KEY (user)
");

/* profiles - one row per member holding their travel bio. */
create_table('profiles', "
    user VARCHAR(32)   NOT NULL,
    text VARCHAR(2000) NOT NULL DEFAULT '',
    PRIMARY KEY (user)
");

/* friends - one row per follow relationship.
   A row means: 'follower' is following 'followee'. */
create_table('friends', "
    follower VARCHAR(32) NOT NULL,
    followee VARCHAR(32) NOT NULL,
    INDEX (follower),
    INDEX (followee)
");

/* messages - public and private messages posted to a member's wall.
   pm = 0 means a public message, pm = 1 means a private message. */
create_table('messages', "
    id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    auth    VARCHAR(32)  NOT NULL,
    recip   VARCHAR(32)  NOT NULL,
    pm      TINYINT(1)   NOT NULL DEFAULT 0,
    time    INT UNSIGNED NOT NULL,
    message VARCHAR(2000) NOT NULL,
    PRIMARY KEY (id),
    INDEX (auth),
    INDEX (recip)
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Travlr - Database Setup</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="content">
        <div class="card">
            <h1>Travlr database setup</h1>
            <p class="success">All four tables were created successfully:</p>
            <ul>
                <li><strong>members</strong> &ndash; registered accounts</li>
                <li><strong>profiles</strong> &ndash; travel bios</li>
                <li><strong>friends</strong> &ndash; follow relationships</li>
                <li><strong>messages</strong> &ndash; public and private messages</li>
            </ul>
            <p>You can now <a href="index.php">open Travlr</a>.</p>
            <p class="muted">For security, you may delete or rename this
               file once setup is complete.</p>
        </div>
    </main>
</body>
</html>
