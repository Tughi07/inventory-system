<?php
session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$price = $_POST['price'];
$quantity = $_POST['quantity'];
$image = $_POST['image'];

// Check if product already in cart, update quantity if exists
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = [
        'name' => $product_name,
        'price' => $price,
        'quantity' => $quantity,
        'image' => $image
    ];
}

// Redirect to cart page
header("Location: cart.php");
exit();
?>