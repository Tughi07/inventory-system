<?php
session_start();
include 'config.php';

$title = 'Order Success';

// Ensure the cart is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
	echo "<h2>No order to process.</h2>";
	exit();
}

// Get user info
$username = $_SESSION['user'];
$order_details = '';

// Process each item in the cart
foreach ($_SESSION['cart'] as $id => $quantity) {
	$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

	if ($product['stock_quantity'] >= $quantity) {
		// Deduct stock
		$conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $id");

		// Store order details for email
		$order_details .= "{$product['name']} (x$quantity) - $" . number_format($total_price, 2) . "<br>";
	}
}

// Clear cart after purchase
$_SESSION['cart'] = [];
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php' ?>

<div class="container h-100 py-5 text-center">
	<h2 class="text-success">🎉 Order Placed Successfully!</h2>
	<p>Your order has been processed, and a confirmation email has been sent.</p>

	<div class="alert alert-info">
		<h5>Order Summary</h5>
		<p><?= $order_details ?></p>
	</div>

	<a href="store.php" class="btn btn-dark">Continue Shopping</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'templates/footer.php' ?>

</html>