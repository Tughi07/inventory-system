<?php
session_start();
include 'config.php';

// Ensure the cart is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>No order to process.</h2>";
    exit();
}

// Get user info
$username = $_SESSION['user'];
$order_details = "";

// Process each item in the cart
foreach ($_SESSION['cart'] as $id => $quantity) {
    $product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

    if ($product['stock'] >= $quantity) {
        // Deduct stock
        $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $id");

        // Record sale
        $total_price = $product['price'] * $quantity;
        $conn->query("INSERT INTO sales (product_id, quantity, total_price) VALUES ($id, $quantity, $total_price)");

        // Store order details for email
        $order_details .= "{$product['name']} (x$quantity) - $" . number_format($total_price, 2) . "<br>";
    }
}

// Clear cart after purchase
$_SESSION['cart'] = [];

// Prepare email content
$to = "customer@example.com"; // Replace with actual customer email (Needs implementation)
$subject = "Order Confirmation - Thank You!";
$message = "
    <html>
    <head>
        <title>Order Confirmation</title>
    </head>
    <body>
        <h2>Thank You for Your Purchase, $username!</h2>
        <p>Your order has been successfully placed. Below are the details:</p>
        <p>$order_details</p>
        <p>We appreciate your business and look forward to serving you again.</p>
    </body>
    </html>
";
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: noreply@yourwebsite.com";

// Send Email (Only works on a real server)
mail($to, $subject, $message, $headers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5 text-center">
    <h2 class="text-success">🎉 Order Placed Successfully!</h2>
    <p>Your order has been processed, and a confirmation email has been sent.</p>
    
    <div class="alert alert-info">
        <h5>Order Summary</h5>
        <p><?= $order_details ?></p>
    </div>

    <a href="store.php" class="btn btn-primary">Continue Shopping</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>