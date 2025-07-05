<?php
session_start();
include 'config.php';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid product ID.");
}

$product_id = intval($_GET['id']);

// Get product details from your DB
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("Product not found.");
}

$product = $result->fetch_assoc();

// Build cart item
$cart_item = [
  'name' => $product['name'],
  'price' => $product['price'],
  'quantity' => 1,
  'image' => !empty($product['image_url']) ? $product['image_url'] : 'placeholder.jpg'
];

// If already in cart, increase quantity
if (isset($_SESSION['cart'][$product_id])) {
  $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
  $_SESSION['cart'][$product_id] = $cart_item;
}

// Redirect to cart with a friendly message
$_SESSION['success_message'] = 'Product added successfuly!';
header("Location: cart.php");
exit();