<?php
/*
 * messages.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * A member's message wall. Visitors can post a PUBLIC message (visible
 * to everyone) or a PRIVATE message (visible only to the sender and the
 * wall owner). Without ?view it shows the logged-in user's own wall.
 */

$page_title = 'Travlr - Messages';
require_once 'header.php';

if (!$loggedin) {
    echo "<div class='card'><p>Please <a href='login.php'>log in</a> " .
         "to read and post messages.</p></div>";
    require_once 'footer.php';
    exit;
}

/* Whose wall are we looking at? */
$view = isset($_GET['view']) ? trim($_GET['view']) : $user;

if (!member_exists($view)) {
    echo "<div class='card'><p>That member could not be found.</p></div>";
    require_once 'footer.php';
    exit;
}

$isOwn = ($view === $user);

/* ------------------------------------------------------------------
 * Posting a new message.
 * ------------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = trim($_POST['message'] ?? '');
    $pm   = (isset($_POST['pm']) && $_POST['pm'] === '1') ? 1 : 0;

    if ($text !== '') {
        if (strlen($text) > 2000) {
            $text = substr($text, 0, 2000);
        }
        db_query(
            'INSERT INTO messages (auth, recip, pm, time, message)
             VALUES (?, ?, ?, ?, ?)',
            [$user, $view, $pm, time(), $text]
        );
    }
}

/* ------------------------------------------------------------------
 * Erasing a message from your own wall.
 * ------------------------------------------------------------------ */
if (isset($_GET['erase'])) {
    $eraseId = (int) $_GET['erase'];
    db_query(
        'DELETE FROM messages WHERE id = ? AND recip = ?',
        [$eraseId, $user]
    );
}

/* ------------------------------------------------------------------
 * Load the messages on this wall, newest first.
 * ------------------------------------------------------------------ */
$messages = db_query(
    'SELECT * FROM messages WHERE recip = ? ORDER BY time DESC',
    [$view]
)->fetchAll();

$whose = $isOwn ? 'Your' : h($view) . "'s";
?>

<div class="card">
    <h1><?php echo $whose; ?> message wall</h1>
    <?php show_profile($view); ?>

    <h2>Leave a message</h2>
    <form method="post" action="messages.php?view=<?php echo urlencode($view); ?>">
        <textarea name="message" rows="3" maxlength="2000"
            placeholder="Share a travel tip or say hello..."></textarea>

        <div class="radio-row">
            <label><input type="radio" name="pm" value="0" checked> Public</label>
            <label><input type="radio" name="pm" value="1"> Private</label>
        </div>

        <button type="submit">Post Message</button>
    </form>

    <h2>Messages</h2>
    <?php if (count($messages) === 0): ?>
        <p class="muted">No messages yet.</p>
    <?php else: ?>
        <?php foreach ($messages as $m): ?>
            <?php
                /* Private messages are visible only to the sender and
                   the wall owner. Skip any the viewer may not see. */
                if ($m['pm'] == 1 &&
                    $m['auth'] !== $user &&
                    $m['recip'] !== $user) {
                    continue;
                }
            ?>
            <div class="message <?php echo $m['pm'] == 1 ? 'private' : ''; ?>">
                <div class="message-head">
                    <a href="messages.php?view=<?php echo urlencode($m['auth']); ?>">
                        <?php echo h($m['auth']); ?></a>
                    <?php echo $m['pm'] == 1
                        ? '<span class="tag tag-private">whispered</span>'
                        : ''; ?>
                    <span class="message-time">
                        <?php echo date('M j, Y g:i a', $m['time']); ?></span>
                </div>
                <div class="message-body"><?php echo h($m['message']); ?></div>
                <?php if ($m['recip'] === $user): ?>
                    <a class="action drop"
                       href="messages.php?view=<?php echo urlencode($view); ?>&amp;erase=<?php
                       echo (int) $m['id']; ?>">Erase</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
