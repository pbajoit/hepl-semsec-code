<?php
include_once('hsts_session.php');

error_reporting(E_ALL);

if (!isset($_SESSION['secret'])) {
    $_SESSION['secret'] = uniqid("", true);
}

include_once('pdo_instance.php');
global $pdo;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = $_POST['product'];
    $comment = $_POST['new_comment'];
    if (empty($productId)) {
        header("Location: /index-secure.php");
        exit();
    } elseif (!empty($comment)) {
        insert_comment($pdo, $productId, $comment);
    }
    header("Location: /index-secure.php?product=" . $productId);
    exit();
}
?>
<html>
<body>
<h1>ProductS & reviewS</h1>
<p>Your secret is <strong><code><?php echo($_SESSION['secret']); ?></code></strong></p>

<?php
if (!isset($_REQUEST['product'])) {
    display_products_list($pdo);
} else {
    $productId = $_REQUEST['product'];
    display_product_detail($pdo, $productId);
}

function insert_comment(PDO $pdo, $productId, $comment)
{
    $stmt = $pdo->prepare('INSERT INTO comments(product_id, comment) VALUES(?, ?)');
    $stmt->execute([$productId, $comment]);
}

function display_products_list(PDO $pdo)
{
    echo('<h2>List of Products</h2>');
    $stmt = $pdo->query('SELECT id, name, description, price, sales FROM product');
    echo('<table>');
    echo('<tr><th>Name</th><th>Description</th><th>Price</th><th>Sales</th></tr>');
    while ($row = $stmt->fetch()) {
        echo('<tr>');
        echo('<td><a href="/index-secure.php?product=' . $row['id'] . '">' . $row['name'] . '</a></td>');
        echo('<td>' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</td>');
        echo('<td>' . $row['price'] . ' €</td>');
        echo('<td>' . $row['sales'] . '</td>');
        echo('</tr>');
    }
    echo('</table>');
}

function display_product_detail(PDO $pdo, $productId)
{
    echo("<h3>User comments</h3>");
    $stmt = $pdo->prepare('SELECT ts, comment FROM comments WHERE product_id=?');
    $stmt->execute([$productId]);
    echo('<table>');
    echo('<tr><th>Timestamp</th><th>Comment</th></tr>');
    while ($row = $stmt->fetch()) {
        echo('<tr>');
        echo('<td>' . $row['ts'] . '</td>');
        echo('<td>' . htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8') . '</td>');
        echo('</tr>');
    }
    echo('</table>');

    echo("<h2>Product detail</h2>");
    $stmt = $pdo->prepare('SELECT id, name, description, price, sales FROM product WHERE id=?');
    $stmt->execute([$productId]);
    $row = $stmt->fetch();
    if ($row) {
        echo('<table>');
        echo('<tr><th>Name</th><td>' .
                htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</td></tr>');
        echo('<tr><th>Description</th><td>' .
                htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</td></tr>');
        echo('<tr><th>Price</th><td>' . $row['price'] . ' €</td></tr>');
        echo('<tr><th>Sales</th><td>' . $row['sales'] . '</td></tr>');
        echo('</table>');
    }

    echo("<h3>Your feedback</h3>");
    echo('<form method="post" action="/index-secure.php">');
    echo('Your opinion about the product<br/>');
    echo('<input type="hidden" name="product" value="' . $productId . '">');
    echo('<textarea name="new_comment" rows="5" cols="40"></textarea>');
    echo('<br/><input type="submit">');
    echo('</form>');

    echo('<p><a href="/index-secure.php">Retour à la liste</a></p>');
}

?>
</body>
</html>

