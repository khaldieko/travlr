<?php
/*
 * logout.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * Ends the current session and shows a confirmation message.
 */

$page_title = 'Travlr - Log Out';
require_once 'header.php';

if ($loggedin) {
    destroy_session();
    echo "<div class='card'>" .
         "<h1>You have been logged out</h1>" .
         "<p class='success'>Thanks for visiting Travlr. Safe travels!</p>" .
         "<p><a class='button' href='login.php'>Log back in</a></p>" .
         "</div>";
} else {
    echo "<div class='card'>" .
         "<h1>You are not logged in</h1>" .
         "<p><a class='button' href='login.php'>Log in</a></p>" .
         "</div>";
}

require_once 'footer.php';
?>
