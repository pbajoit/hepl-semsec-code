<?php
// version sécurisée
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// optionnel
ini_set('session.cookie_lifetime', 120);

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
?>

<h1>Sessions</h1>
<p>Your sessionId <strong><code><?php echo(session_id()); ?></code></strong></p>
<p>Your secret is <strong><code><?php echo($_SESSION['secret']); ?></code></strong></p>

<script>alert(document.cookie)</script>
