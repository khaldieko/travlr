<?php
/*
 * profile.php
 * Travlr - A Travel-Focused Social Networking Platform
 *
 * Lets the logged-in member edit their travel bio and upload a profile
 * photo. Uploaded photos are resized to a small square thumbnail with
 * PHP's GD image library and saved as uploads/<username>.jpg.
 */

$page_title = 'Travlr - Edit Profile';
require_once 'header.php';

/* This page is only for logged-in members. */
if (!$loggedin) {
    echo "<div class='card'><p>Please <a href='login.php'>log in</a> " .
         "to edit your profile.</p></div>";
    require_once 'footer.php';
    exit;
}

$message = '';
$error   = '';

/* ------------------------------------------------------------------
 * make_thumbnail()
 * Reads an uploaded image, resizes it to a square thumbnail no larger
 * than $size pixels, and saves it as a JPEG at $destination.
 * Returns true on success, false if the file is not a usable image.
 * ------------------------------------------------------------------ */
function make_thumbnail($source, $type, $destination, $size = 200)
{
    switch ($type) {
        case 'image/jpeg': $image = imagecreatefromjpeg($source); break;
        case 'image/png':  $image = imagecreatefrompng($source);  break;
        case 'image/gif':  $image = imagecreatefromgif($source);  break;
        default:           return false;
    }

    if (!$image) {
        return false;
    }

    $width  = imagesx($image);
    $height = imagesy($image);

    /* Take a centred square crop so portraits and landscapes both
       end up as a tidy square thumbnail. */
    $side = min($width, $height);
    $x    = (int) (($width  - $side) / 2);
    $y    = (int) (($height - $side) / 2);

    $thumb = imagecreatetruecolor($size, $size);
    imagecopyresampled($thumb, $image, 0, 0, $x, $y,
        $size, $size, $side, $side);

    imagejpeg($thumb, $destination, 90);

    imagedestroy($image);
    imagedestroy($thumb);

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* --- Save the travel bio --- */
    $bio = trim($_POST['bio'] ?? '');
    if (strlen($bio) > 2000) {
        $bio = substr($bio, 0, 2000);
    }
    db_query('UPDATE profiles SET text = ? WHERE user = ?', [$bio, $user]);
    $message = 'Your travel bio has been saved.';

    /* --- Handle an optional photo upload --- */
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

        $tmp  = $_FILES['photo']['tmp_name'];
        $type = mime_content_type($tmp);

        if (!is_dir('uploads')) {
            mkdir('uploads', 0755);
        }

        if (make_thumbnail($tmp, $type, 'uploads/' . $user . '.jpg')) {
            $message = 'Your profile photo and travel bio have been saved.';
        } else {
            $error = 'The photo must be a JPEG, PNG, or GIF image.';
        }
    }
}

/* Load the current bio so the textarea can be pre-filled. */
$stmt = db_query('SELECT text FROM profiles WHERE user = ?', [$user]);
$row  = $stmt->fetch();
$currentBio = $row ? $row['text'] : '';
?>

<div class="card">
    <h1>Your Travlr profile</h1>

    <?php if ($message !== ''): ?>
        <p class="success"><?php echo h($message); ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="error"><?php echo h($error); ?></p>
    <?php endif; ?>

    <h2>How your profile looks now</h2>
    <?php show_profile($user); ?>

    <h2>Update your details</h2>
    <form method="post" action="profile.php" enctype="multipart/form-data">
        <label for="bio">Travel bio</label>
        <textarea id="bio" name="bio" rows="5"
            placeholder="Tell other travellers about yourself and where you have been..."><?php
            echo h($currentBio); ?></textarea>

        <label for="photo">Profile photo (JPEG, PNG, or GIF)</label>
        <input type="file" id="photo" name="photo" accept="image/*">

        <button type="submit">Save Profile</button>
    </form>
</div>

<?php require_once 'footer.php'; ?>
