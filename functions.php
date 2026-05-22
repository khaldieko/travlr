<?php
/*
 * functions.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * This file holds the database connection settings and the shared
 * helper functions used by every other page in the application.
 * It is included automatically by header.php.
 */

/* ------------------------------------------------------------------
 * Database configuration
 * Change these four values to match your local environment.
 * The defaults work with a standard XAMPP installation.
 * ------------------------------------------------------------------ */
$db_host    = 'localhost';
$db_name    = 'travlr';
$db_user    = 'root';
$db_pass    = '';
$db_charset = 'utf8mb4';

/* ------------------------------------------------------------------
 * Connect to MySQL using PDO
 * ------------------------------------------------------------------ */
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";

$pdo_options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $pdo_options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

/* ------------------------------------------------------------------
 * db_query()
 * Runs a SQL statement as a prepared statement. Passing the values
 * in the $params array keeps the application safe from SQL injection.
 * Returns the PDOStatement so the caller can fetch rows if needed.
 * ------------------------------------------------------------------ */
function db_query($sql, $params = [])
{
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/* ------------------------------------------------------------------
 * create_table()
 * Creates a table only if it does not already exist, so setup.php
 * can be run more than once without causing errors.
 * ------------------------------------------------------------------ */
function create_table($name, $definition)
{
    global $pdo;
    $pdo->query("CREATE TABLE IF NOT EXISTS $name ($definition)");
}

/* ------------------------------------------------------------------
 * h()
 * Escapes a value for safe display in HTML. This protects the app
 * against cross-site scripting (XSS) when showing user content.
 * ------------------------------------------------------------------ */
function h($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/* ------------------------------------------------------------------
 * destroy_session()
 * Clears all session data and the session cookie to log a user out.
 * ------------------------------------------------------------------ */
function destroy_session()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/* ------------------------------------------------------------------
 * show_profile()
 * Displays a member's profile photo (if one was uploaded) and their
 * travel bio. Used on the profile, members, and messages pages.
 * ------------------------------------------------------------------ */
function show_profile($username)
{
    $photo = 'uploads/' . $username . '.jpg';

    echo "<div class='profile-card'>";

    if (file_exists($photo)) {
        // The time() query string forces the browser to load a fresh
        // copy of the image whenever the profile photo is updated.
        echo "<img class='avatar' src='" . h($photo) . "?t=" . time() .
             "' alt='" . h($username) . "'>";
    } else {
        echo "<div class='avatar avatar-blank'>" .
             strtoupper(h(substr($username, 0, 1))) . "</div>";
    }

    $stmt = db_query('SELECT text FROM profiles WHERE user = ?', [$username]);
    $row  = $stmt->fetch();

    if ($row && trim($row['text']) !== '') {
        echo "<p class='bio'>" . h($row['text']) . "</p>";
    } else {
        echo "<p class='muted'>No travel bio yet.</p>";
    }

    echo "</div>";
}

/* ------------------------------------------------------------------
 * member_exists()
 * Returns true if the given username is registered.
 * ------------------------------------------------------------------ */
function member_exists($username)
{
    $stmt = db_query('SELECT 1 FROM members WHERE user = ?', [$username]);
    return $stmt->rowCount() > 0;
}

/* Note: this pure-PHP file deliberately has no closing "?>" tag, so that
   no stray whitespace is ever sent to the browser when it is included. */

