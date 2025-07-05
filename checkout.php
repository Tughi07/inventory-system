<?php
session_start();
include 'config.php';

$title = 'Checkout';

$total_price = 0;
$order_summary = "";

foreach ($_SESSION['cart'] as $id => $item) {
	// Ensure price and quantity are extracted correctly
	$name = $item['name'];
	$price = (float) $item['price']; // Convert price to float
	$quantity = (int) $item['quantity']; // Convert quantity to integer
	$image = $item['image'];

	// Calculate total for each item
	$item_total = $price * $quantity;
	$total_price += $item_total; // Add to total price

	// Append order summary
	$order_summary .= "
        <div class='d-flex align-items-center mb-3'>
            <img src='uploads/$image' width='60' height='60' class='me-3' style='object-fit: cover;'>
            <div>
                <strong>$name</strong><br>
                Price: $" . number_format($price, 2) . "<br>
                Quantity: $quantity<br>
                <strong>Total:</strong> $" . number_format($item_total, 2) . "
            </div>
        </div>
    ";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
	$stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
	$stmt->bind_param("id", $user_id, $total_price);
	if ($stmt->execute()) {
		$order_id = $stmt->insert_id;

		$item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)");
		foreach ($_SESSION['cart'] as $id => $item) {
			$product_id = (int) $id;
			$quantity = (int) $item['quantity'];
			$price = (float) $item['price'];
			$item_stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
			$item_stmt->execute();
		}
		$item_stmt->close();
		// Redirect with success message
		$_SESSION['success_message'] = "Your order has been placed successfully!";
		header("Location: order_success.php");
		exit();
	} else {
		echo "<div class='alert alert-danger'>Failed to place the order. Please try again.</div>";
	}
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
	echo "<h2>Your cart is empty.</h2>";
	exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'templates/header.php' ?>
<div class="container h-100">
	<form action="" method="post">
		<h2 class="text-center my-3"><i class="fas fa-shopping-cart"></i> Checkout</h2>
		<div class="card p-4 shadow">
			<h5 class="text-center">Order Summary</h5>
			<hr>
			<?= $order_summary ?>
			<hr>
			<p class="text-end"><strong>Total Amount: </strong> $<?= number_format($total_price, 2) ?></p>
			<button class="btn btn-dark" type="submit">
				Purchase
			</button>
		</div>
	</form>
</div>
<?php include 'templates/footer.php' ?>

</html>