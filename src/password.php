<?php
include_once('hsts_session.php');

error_reporting(E_ALL);

/*
 CREATE TABLE `users_store` (
  `user` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `secure` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `users_store`
  ADD PRIMARY KEY (`user`);
 */

if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = 'Welcome';
}

include_once('pdo_instance.php');
global $pdo;

function select_users(PDO $pdo, $secure)
{
    $stmt = $pdo->prepare('SELECT user, password FROM users_store WHERE secure = ?');
    $stmt->execute([$secure]);
    return $stmt->fetchAll();
}

function upsert_user(PDO $pdo, $user, $password)
{
    $stmt = $pdo->prepare(
            'INSERT INTO users_store ( user, password, secure ) VALUES (:user, :password, 0)
                    ON DUPLICATE KEY UPDATE password=:password, secure=0;
                ');
    $stmt->bindValue(':password', $password);
    $stmt->bindValue(':user', $user);
    $stmt->execute();
    return 'OK';
}

function upsert_secure_user(PDO $pdo, $user, $password)
{
    $stmt = $pdo->prepare(
            'INSERT INTO users_store ( user, password, secure ) VALUES (:user, :password, 1)
                    ON DUPLICATE KEY UPDATE password=:password, secure=1;
                ');
    $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
    $stmt->bindValue(':user', $user);
    $stmt->execute();
    return 'OK';
}

function verify_password(PDO $pdo, $user, $password)
{
    $success = "NO MATCH";

    $stmt = $pdo->prepare('SELECT password FROM users_store WHERE user = :user');
    $stmt->bindValue(':user', $user);
    $stmt->execute();
    $result = $stmt->fetch();
    if ($result && sizeof($result) > 0) {
        if (password_verify($password, $result['password'])) {
            $success = "MATCH (secure)";
        } else {
            $stmt = $pdo->prepare('SELECT user FROM users_store WHERE user = :user AND password = :password');
            $stmt->bindValue(':password', $password);
            $stmt->bindValue(':user', $user);
            $stmt->execute();
            $result = $stmt->fetch();
            if ($result && sizeof($result) > 0) {
                $success = "MATCH (unsecure)";
            }
        }
    }

    return $success;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $user = $_POST['username'];
    $password = $_POST['password'];
    $secure = $_POST['secure'];
    if (empty($user)) {
        $_SESSION['message'] = 'Missing user';
    } elseif (empty($password)) {
        $_SESSION['message'] = 'Missing password';
    } elseif ($action == 'create-password') {
        if ($secure) {
            $result = upsert_secure_user($pdo, $user, $password);
        } else {
            $result = upsert_user($pdo, $user, $password);
        }
        $_SESSION['message'] = $action . ':' . $result;
    } elseif ($action == 'verify-password') {
        $result = verify_password($pdo, $user, $password);
        $_SESSION['message'] = $action . ':' . $result;
    }
    header("Location: /password.php");
    exit();
}
?>
<html>
<body>
<h1>Passwords</h1>
<div>Result: <span style="font-weight:bolder; color:red;"><?php echo $_SESSION['message'] ?></span></div>

<h2>Submit a password</h2>
<form method="post" action="/password.php">
    <input type="hidden" name="action" value="create-password">
    User name <input type="text" name="username">
    <br/>Password <input type="password" name="password">
    <br/>Store securely ? <input type="checkbox" name="secure">
    <br/><input type="submit">
</form>

<h2>Verify a password</h2>
<form method="post" action="/password.php">
    <input type="hidden" name="action" value="verify-password">
    User name <input type="text" name="username">
    <br/>Password <input type="password" name="password">
    <br/><input type="submit">
</form>

<h2>Passwords</h2>

<h3>Unencrypted</h3>
<?php
$rows = select_users($pdo, 0);
echo('<table>');
echo('<tr><th>User</th><th>Password</th></tr>');
foreach ($rows as $row) {
    echo('<tr>');
    echo('<td>' . htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8') . '</td>');
    echo('<td>' . htmlspecialchars($row['password'], ENT_QUOTES, 'UTF-8') . '</td>');
    echo('</tr>');
}
echo('</table>');
?>

<h3>Secure</h3>
<?php
$rows = select_users($pdo, 1);
echo('<table>');
echo('<tr><th>User</th><th>Password (secure)</th></tr>');
foreach ($rows as $row) {
    echo('<tr>');
    echo('<td>' . htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8') . '</td>');
    echo('<td>' . htmlspecialchars($row['password'], ENT_QUOTES, 'UTF-8') . '</td>');
    echo('</tr>');
}
echo('</table>');
?>
</body>
</html>

