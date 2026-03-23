<?php
// version de base, valeurs par défaut
session_start();

if (!empty($_SERVER['HTTPS'])) {
    header("Strict-Transport-Security: max-age=31536000");
}

// reporting maximal
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if (!isset($_SESSION['secret'])) {
    $_SESSION['secret'] = uniqid("", true);
}
if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 0;
}
?>

<h1>Sessions</h1>
<p>Your sessionId <strong><code><?php echo(session_id()); ?></code></strong></p>
<p>Your secret is <strong><code><?php echo($_SESSION['secret']); ?></code></strong></p>
<?php
$_SESSION['counter'] = 1 + $_SESSION['counter'];
?>
<p>Your visit #<?php echo($_SESSION['counter']); ?></p>

