<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $result = $conn->query("SELECT price, stock FROM products WHERE id = $product_id");
    $row = $result->fetch_assoc();

    if ($row['stock'] >= $quantity) {
        $total_price = $row['price'] * $quantity;
        $conn->query("INSERT INTO sales (product_id, quantity, total_price) VALUES ($product_id, $quantity, $total_price)");
        $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
        echo "Sale recorded successfully!";
    } else {
        echo "Not enough stock!";
    }
}

$result = $conn->query("SELECT * FROM products");
?>
<form method="post">
    <select name="product_id">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <option value="<?= $row['id'] ?>"><?= $row['name'] ?> (Stock: <?= $row['stock'] ?>)</option>
        <?php } ?>
    </select>
    Quantity: <input type="number" name="quantity" required>
    <input type="submit" value="Process Sale">
</form>