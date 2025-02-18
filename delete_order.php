<?php
session_start();
include 'config.php';

// Debugging session values
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Ensure only admins can delete orders
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    echo "You do not have permission to delete orders.";
    exit();
}

// Get order ID from URL
if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']); // Ensure ID is a valid integer

    // Debugging: Check if the order ID is received
    echo "Deleting order ID: " . $order_id . "<br>";

    // Run delete query
    $query = "DELETE FROM sales WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        echo "Order deleted successfully!";
    } else {
        echo "Error deleting order: " . $conn->error;
    }

    // Redirect back to orders page
    header("Location: admin_orders.php?deleted=success");
    exit();
} else {
    echo "No order ID provided.";
}
?>