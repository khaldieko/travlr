<?php
/*
 * members.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * The "Discover" page. It has two modes:
 *   1. With ?view=<username>  - shows one member's profile.
 *   2. With no parameters     - lists every member so the user can
 *                               follow or unfollow them.
 * Follow / unfollow actions arrive as ?follow=<user> or ?unfollow=<user>.
 */

$page_title = 'Travlr - Discover Members';
require_once 'header.php';

if (!$loggedin) {
    echo "<div class='card'><p>Please <a href='login.php'>log in</a> " .
         "to discover other travellers.</p></div>";
    require_once 'footer.php';
    exit;
}

/* ------------------------------------------------------------------
 * Handle a follow or unfollow request.
 * ------------------------------------------------------------------ */
if (isset($_GET['follow'])) {
    $target = trim($_GET['follow']);
    if ($target !== '' && $target !== $user && member_exists($target)) {
        /* Only add the relationship if it does not already exist. */
        $stmt = db_query(
            'SELECT 1 FROM friends WHERE follower = ? AND followee = ?',
            [$user, $target]
        );
        if ($stmt->rowCount() === 0) {
            db_query(
                'INSERT INTO friends (follower, followee) VALUES (?, ?)',
                [$user, $target]
            );
        }
    }
}

if (isset($_GET['unfollow'])) {
    $target = trim($_GET['unfollow']);
    if ($target !== '') {
        db_query(
            'DELETE FROM friends WHERE follower = ? AND followee = ?',
            [$user, $target]
        );
    }
}

/* ------------------------------------------------------------------
 * Mode 1: view a single member's profile.
 * ------------------------------------------------------------------ */
if (isset($_GET['view'])) {
    $view = trim($_GET['view']);

    if (!member_exists($view)) {
        echo "<div class='card'><p>That member could not be found.</p></div>";
        require_once 'footer.php';
        exit;
    }

    $whose = ($view === $user) ? 'Your' : h($view) . "'s";
    echo "<div class='card'>";
    echo "<h1>$whose profile</h1>";
    show_profile($view);
    echo "<p><a class='button' href='messages.php?view=" . urlencode($view) .
         "'>View $whose messages</a> ";
    echo "<a class='button' href='friends.php?view=" . urlencode($view) .
         "'>View $whose friends</a></p>";
    echo "<p><a href='members.php'>&larr; Back to all members</a></p>";
    echo "</div>";

    require_once 'footer.php';
    exit;
}

/* ------------------------------------------------------------------
 * Mode 2: list every member.
 * ------------------------------------------------------------------ */
$members = db_query('SELECT user FROM members ORDER BY user')->fetchAll();
?>

<div class="card">
    <h1>Discover travellers</h1>
    <p class="muted">Follow other members to keep up with their journeys.</p>

    <ul class="member-list">
    <?php foreach ($members as $row): ?>
        <?php
            $name = $row['user'];
            if ($name === $user) {
                continue; // Do not list the current user.
            }

            /* Do I follow them? Do they follow me? */
            $iFollow = db_query(
                'SELECT 1 FROM friends WHERE follower = ? AND followee = ?',
                [$user, $name]
            )->rowCount() > 0;

            $followsMe = db_query(
                'SELECT 1 FROM friends WHERE follower = ? AND followee = ?',
                [$name, $user]
            )->rowCount() > 0;

            if ($iFollow && $followsMe) {
                $label = "<span class='tag tag-mutual'>Mutual friend</span>";
            } elseif ($iFollow) {
                $label = "<span class='tag'>You follow them</span>";
            } elseif ($followsMe) {
                $label = "<span class='tag'>Follows you</span>";
            } else {
                $label = '';
            }
        ?>
        <li>
            <a class="member-name" href="members.php?view=<?php
                echo urlencode($name); ?>"><?php echo h($name); ?></a>
            <?php echo $label; ?>
            <?php if ($iFollow): ?>
                <a class="action drop" href="members.php?unfollow=<?php
                    echo urlencode($name); ?>">Unfollow</a>
            <?php else: ?>
                <a class="action" href="members.php?follow=<?php
                    echo urlencode($name); ?>">Follow</a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>

    <?php if (count($members) <= 1): ?>
        <p class="muted">No other members have joined yet.</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
