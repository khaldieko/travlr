<?php
/*
 * signup.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * Lets a new visitor create a Travlr account. The username field is
 * checked for availability as the user types (an asynchronous call to
 * checkuser.php). The password is stored as a secure hash.
 */

$page_title = 'Travlr - Sign Up';
require_once 'header.php';

/* A logged-in user does not need the signup page. */
if ($loggedin) {
    echo "<div class='card'><p>You are already logged in as <strong>" .
         h($user) . "</strong>.</p>" .
         "<p><a href='index.php'>Go to your home page</a></p></div>";
    require_once 'footer.php';
    exit;
}

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['user'] ?? '');
    $password = $_POST['pass'] ?? '';

    /* --- Validate the submitted details --- */
    if ($username === '' || $password === '') {
        $error = 'Please fill in both fields.';
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,32}$/', $username)) {
        $error = 'Username must be 3-32 letters, numbers, or underscores.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif (member_exists($username)) {
        $error = 'Sorry, that username is already taken.';
    } else {
        /* --- Create the account --- */
        $hash = password_hash($password, PASSWORD_DEFAULT);
        db_query(
            'INSERT INTO members (user, pass, joined) VALUES (?, ?, NOW())',
            [$username, $hash]
        );
        /* Give the new member an empty profile row to edit later. */
        db_query('INSERT INTO profiles (user, text) VALUES (?, ?)',
            [$username, '']);

        echo "<div class='card'>" .
             "<h1>Welcome aboard, " . h($username) . "!</h1>" .
             "<p class='success'>Your Travlr account has been created.</p>" .
             "<p><a class='button' href='login.php'>Log in to continue</a></p>" .
             "</div>";
        require_once 'footer.php';
        exit;
    }
}
?>

<div class="card">
    <h1>Create your Travlr account</h1>
    <p class="muted">Join the community and start sharing your journeys.</p>

    <?php if ($error !== ''): ?>
        <p class="error"><?php echo h($error); ?></p>
    <?php endif; ?>

    <form method="post" action="signup.php">
        <label for="user">Username</label>
        <input type="text" id="user" name="user" maxlength="32"
               value="<?php echo h($username); ?>"
               onblur="checkUsername(this.value)" autocomplete="off">
        <div id="user-status" class="field-note"></div>

        <label for="pass">Password</label>
        <input type="password" id="pass" name="pass" maxlength="64">
        <div class="field-note">At least 6 characters.</div>

        <button type="submit">Sign Up</button>
    </form>

    <p class="muted">Already have an account? <a href="login.php">Log in</a>.</p>
</div>

<?php require_once 'footer.php'; ?>
