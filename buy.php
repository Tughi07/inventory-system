<?php
session_start();
include 'config.php'; // Ensure the database connection is included

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p style='color:red;'>Error: Invalid product ID.</p>";
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session
$product_id = (int) $_GET['id']; // Sanitize input
$quantity = 1; // Default quantity (can be changed)
$total_price = 0;

// Check if the product exists
$product_query = $conn->prepare("SELECT price, stock FROM products WHERE id = ?");
$product_query->bind_param("i", $product_id);
$product_query->execute();
$product_result = $product_query->get_result();

if ($row = $product_result->fetch_assoc()) {
    // Check stock availability
    if ($row['stock'] < $quantity) {
        echo "<p style='color:red;'>Error: Not enough stock available.</p>";
        exit();
    }

    $total_price = $row['price'] * $quantity;

    // Insert into `sales` table
    $insert_query = $conn->prepare("INSERT INTO sales (user_id, product_id, quantity, total_price, sale_date) VALUES (?, ?, ?, ?, NOW())");
    if ($insert_query) {
        $insert_query->bind_param("iiid", $user_id, $product_id, $quantity, $total_price);
        if ($insert_query->execute()) {
            // Reduce stock after successful purchase
            $update_stock_query = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $update_stock_query->bind_param("ii", $quantity, $product_id);
            $update_stock_query->execute();

            echo "<p style='color:green;'>Purchase successful! <a href='store.php'>Go back to store</a></p>";
        } else {
            echo "<p style='color:red;'>Error: Could not complete the purchase.</p>";
        }
    } else {
        echo "<p style='color:red;'>Database error: Failed to prepare statement.</p>";
    }
} else {
    echo "<p style='color:red;'>Error: Product not found.</p>";
}

// Close database connections if initialized
if (isset($product_query)) $product_query->close();
if (isset($insert_query)) $insert_query->close();
if (isset($update_stock_query)) $update_stock_query->close();
$conn->close();
?>