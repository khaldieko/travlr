<?php
/*
 * friends.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * Shows a member's connections, split into three groups:
 *   - Mutual friends (you follow each other)
 *   - Followers      (they follow this member)
 *   - Following      (this member follows them)
 * Without ?view it shows the logged-in user's own connections.
 */

$page_title = 'Travlr - Friends';
require_once 'header.php';

if (!$loggedin) {
    echo "<div class='card'><p>Please <a href='login.php'>log in</a> " .
         "to see your friends.</p></div>";
    require_once 'footer.php';
    exit;
}

/* Decide whose friends we are showing. */
$view = isset($_GET['view']) ? trim($_GET['view']) : $user;

if (!member_exists($view)) {
    echo "<div class='card'><p>That member could not be found.</p></div>";
    require_once 'footer.php';
    exit;
}

$isOwn = ($view === $user);

/* People who follow $view. */
$followers = db_query(
    'SELECT follower FROM friends WHERE followee = ?', [$view]
)->fetchAll(PDO::FETCH_COLUMN);

/* People $view follows. */
$following = db_query(
    'SELECT followee FROM friends WHERE follower = ?', [$view]
)->fetchAll(PDO::FETCH_COLUMN);

/* Split into the three groups. */
$mutual         = array_intersect($followers, $following);
$followersOnly  = array_diff($followers, $mutual);
$followingOnly  = array_diff($following, $mutual);

/* Helper that prints one group as a list of profile links. */
function show_group($title, $people)
{
    if (count($people) === 0) {
        return;
    }
    echo "<h2>" . h($title) . "</h2><ul class='member-list'>";
    foreach ($people as $person) {
        echo "<li><a class='member-name' href='members.php?view=" .
             urlencode($person) . "'>" . h($person) . "</a></li>";
    }
    echo "</ul>";
}

$whose = $isOwn ? 'Your' : h($view) . "'s";
?>

<div class="card">
    <h1><?php echo $whose; ?> travel network</h1>

    <?php
        show_group('Mutual friends', $mutual);
        show_group($isOwn ? 'Your followers' : h($view) . "'s followers",
                   $followersOnly);
        show_group($isOwn ? 'People you follow'
                          : 'People ' . h($view) . ' follows',
                   $followingOnly);

        if (count($mutual) === 0 &&
            count($followersOnly) === 0 &&
            count($followingOnly) === 0):
    ?>
        <p class="muted">No connections yet. Visit
            <a href="members.php">Discover</a> to follow other travellers.</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
