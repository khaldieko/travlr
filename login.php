<?php
/*
 * login.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * Logs an existing member in. The form is processed BEFORE any HTML is
 * sent so that, on success, the page can redirect with header().
 * The submitted password is checked against the stored hash with
 * password_verify().
 */

require_once 'functions.php';

/* Start the session before any output so login can set session data
   and redirect cleanly. header.php will not start it a second time. */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$alreadyIn = isset($_SESSION['user']);
$error     = '';
$username  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyIn) {

    $username = trim($_POST['user'] ?? '');
    $password = $_POST['pass'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $stmt = db_query('SELECT user, pass FROM members WHERE user = ?',
            [$username]);
        $row = $stmt->fetch();

        if ($row && password_verify($password, $row['pass'])) {
            /* Credentials are correct - start the logged-in session
               and send the member to their home page. */
            session_regenerate_id(true);
            $_SESSION['user'] = $row['user'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

/* From here on the page produces output, so header.php can be included. */
$page_title = 'Travlr - Log In';
require_once 'header.php';

if ($loggedin) {
    echo "<div class='card'><p>You are already logged in as <strong>" .
         h($user) . "</strong>.</p>" .
         "<p><a href='index.php'>Go to your home page</a></p></div>";
    require_once 'footer.php';
    exit;
}
?>

<div class="card">
    <h1>Log in to Travlr</h1>
    <p class="muted">Welcome back &ndash; pick up where you left off.</p>

    <?php if ($error !== ''): ?>
        <p class="error"><?php echo h($error); ?></p>
    <?php endif; ?>

    <form method="post" action="login.php">
        <label for="user">Username</label>
        <input type="text" id="user" name="user" maxlength="32"
               value="<?php echo h($username); ?>">

        <label for="pass">Password</label>
        <input type="password" id="pass" name="pass" maxlength="64">

        <button type="submit">Log In</button>
    </form>

    <p class="muted">New here? <a href="signup.php">Create an account</a>.</p>
</div>

<?php require_once 'footer.php'; ?>
