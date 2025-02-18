<?php
session_start();
include 'config.php';

// Debugging - check session values
if (!isset($_SESSION['role'])) {
    echo "<p style='color:red;'>Error: No role found in session.</p>";
    exit();
}

// Ensure the user is an admin
if ($_SESSION['role'] !== 'admin') {
    echo "<p style='color:red;'>Access denied.</p>";
    exit();
}

// Check if product ID is provided
if (!isset($_GET['id'])) {
    echo "<p style='color:red;'>Error: No product ID provided.</p>";
    exit();
}

$product_id = $_GET['id'];

// Delete product from database
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    echo "<p style='color:green;'>Product deleted successfully.</p>";
} else {
    echo "<p style='color:red;'>Error: Could not delete the product.</p>";
}

// Redirect back to inventory
header("Location: inventory.php");
exit();
?>