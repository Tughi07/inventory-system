<?php
include 'admin_required.php';

// Get all orders from the database
$sql = 'SELECT orders.id, orders.total_amount, orders.status,
 	orders.created_at, users.first_name as customer_name,
	order_items.quantity, products.name as product
 	FROM orders JOIN users ON orders.user_id = users.id 
	INNER JOIN order_items ON orders.id = order_items.order_id
	JOIN products ON order_items.product_id = products.id 
	ORDER BY orders.created_at DESC';

$result = mysqli_query($conn, $sql);

$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php' ?>

<div class="container mt-4">
	<h2 class="text-center">📦 Manage Orders</h2>

	<table class="table table-bordered table-striped">
		<thead class="table-dark">
			<tr>
				<th>Order ID</th>
				<th>Customer</th>
				<th>Total Price</th>
				<th>Order Date</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($rows as $row): ?>
				<tr>
					<td><?= $row['id'] ?></td>
					<td><?= $row['customer_name'] ?></td>
					<td>$<?= number_format($row['total_amount'], 2) ?></td>
					<td><?= $row['created_at'] ?></td>
					<td>
						<a href="delete_order.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?');">Delete</a>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>

	<a href="inventory.php" class="btn btn-secondary">Back to Inventory</a>
</div>

<?php include 'templates/footer.php' ?>

</html>