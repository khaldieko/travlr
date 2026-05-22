<?php
/*
 * index.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * The home page. Guests see a short welcome that invites them to join.
 * Logged-in members see a personal dashboard with quick links and a
 * summary of their travel network.
 */

$page_title = 'Travlr - Home';
require_once 'header.php';
?>

<?php if (!$loggedin): ?>

    <div class="hero">
        <h1>Travel is better together.</h1>
        <p>Travlr is a social network for travellers. Build a profile,
           connect with other explorers, and share your journeys.</p>
        <p>
            <a class="button" href="signup.php">Get Started</a>
            <a class="button button-ghost" href="login.php">Log In</a>
        </p>
    </div>

    <div class="feature-grid">
        <div class="feature">
            <h3>Create your profile</h3>
            <p>Add a photo and a travel bio so other members know your story.</p>
        </div>
        <div class="feature">
            <h3>Find fellow travellers</h3>
            <p>Discover members and follow the people whose trips inspire you.</p>
        </div>
        <div class="feature">
            <h3>Share the journey</h3>
            <p>Post public updates or send private messages to your friends.</p>
        </div>
    </div>

<?php else: ?>

    <?php
        /* Gather a few numbers for the member's dashboard. */
        $following = (int) db_query(
            'SELECT COUNT(*) FROM friends WHERE follower = ?', [$user]
        )->fetchColumn();

        $followers = (int) db_query(
            'SELECT COUNT(*) FROM friends WHERE followee = ?', [$user]
        )->fetchColumn();

        $inbox = (int) db_query(
            'SELECT COUNT(*) FROM messages WHERE recip = ?', [$user]
        )->fetchColumn();
    ?>

    <div class="card">
        <h1>Welcome back, <?php echo h($user); ?>!</h1>
        <p class="muted">Here is a quick look at your Travlr activity.</p>

        <div class="stat-row">
            <div class="stat">
                <span class="stat-number"><?php echo $following; ?></span>
                <span class="stat-label">Following</span>
            </div>
            <div class="stat">
                <span class="stat-number"><?php echo $followers; ?></span>
                <span class="stat-label">Followers</span>
            </div>
            <div class="stat">
                <span class="stat-number"><?php echo $inbox; ?></span>
                <span class="stat-label">Messages</span>
            </div>
        </div>

        <h2>What would you like to do?</h2>
        <p>
            <a class="button" href="members.php">Discover travellers</a>
            <a class="button" href="messages.php">View your messages</a>
            <a class="button" href="profile.php">Edit your profile</a>
        </p>
    </div>

<?php endif; ?>

<?php require_once 'footer.php'; ?>
