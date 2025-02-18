<?php
session_start();
include 'config.php';

// Ensure only admins can access this page
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Get all orders from the database
$result = $conn->query("
    SELECT sales.id, sales.product_id, sales.quantity, sales.total_price, sales.sale_date, 
           products.name AS product_name, users.username AS customer_name
    FROM sales
    JOIN products ON sales.product_id = products.id
    JOIN users ON sales.user_id = users.id
    ORDER BY sales.sale_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">📦 Manage Orders</h2>
    
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['customer_name'] ?></td>
                    <td><?= $row['product_name'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>$<?= number_format($row['total_price'], 2) ?></td>
                    <td><?= $row['sale_date'] ?></td>
                    <td>
                        <a href="delete_order.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <a href="inventory.php" class="btn btn-secondary">Back to Inventory</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>