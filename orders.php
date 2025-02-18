<?php
session_start();
include 'config.php';

// Ensure only logged-in customers can access this page
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's orders
$username = $_SESSION['user'];
$result = $conn->query("
    SELECT sales.*, products.name, products.price
    FROM sales
    JOIN products ON sales.product_id = products.id
    JOIN users ON users.username = '$username'
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">My Orders</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>$<?= number_format($row['total_price'], 2) ?></td>
                    <td><?= $row['sale_date'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="store.php" class="btn btn-primary">Back to Store</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>