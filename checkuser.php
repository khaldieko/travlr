<?php
/*
 * checkuser.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * A small endpoint used by the signup page. The JavaScript function
 * checkUsername() sends a username here asynchronously, and this file
 * replies with a short message saying whether the name is available.
 * It is not a full page - it only outputs a snippet of HTML.
 */

require_once 'functions.php';

$username = trim($_POST['user'] ?? '');

if ($username === '') {
    exit; // Nothing typed yet - say nothing.
}

if (!preg_match('/^[A-Za-z0-9_]{3,32}$/', $username)) {
    echo "<span class='taken'>&#10007; 3-32 letters, numbers or underscores only</span>";
    exit;
}

if (member_exists($username)) {
    echo "<span class='taken'>&#10007; '" . h($username) . "' is already taken</span>";
} else {
    echo "<span class='available'>&#10003; '" . h($username) . "' is available</span>";
}

