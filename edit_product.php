<?php
include 'admin_required.php';

// Get product details
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$product = $result->fetch_assoc();

	$title = 'Edit Product';
	if (!$product) {
		echo "Product not found.";
		exit();
	} else {
		$title = "Edit Product - {$product['name']}";
	}
} else {
	echo "Invalid request.";
	exit();
}

// Handle product update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = $_POST['name'];
	$description = $_POST['description'];
	$price = $_POST['price'];
	$stock = $_POST['stock'];

	// Handle image upload
	if (!empty($_FILES["image"]["name"])) {
		$target_dir = "uploads/";
		$image = $_FILES["image"]["name"];
		$target_file = $target_dir . basename($image);
		move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
	} else {
		$image = $product['image_url'];
	}

	$stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock_quantity=?, image_url=? WHERE id=?");
	$stmt->bind_param("ssdssi", $name, $description, $price, $stock, $image, $id);
	if ($stmt->execute()) {
		$_SESSION['success_message'] = 'Product updated successfuly.';
		header("Location: add_product.php");
	} else {
		echo "Error updating product.";
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php'; ?>

<div class="container my-4">
	<h2 class="text-center">Edit Product <?= $product['name'] ?></h2>

	<form action="" method="post" enctype="multipart/form-data" class="p-3 border rounded bg-white shadow-sm">
		<div class="mb-3">
			<label class="form-label">Product Name</label>
			<input type="text" name="name" class="form-control" value="<?= $product['name'] ?>" required>
		</div>
		<div class="mb-3">
			<label for="description" class="form-label">Description</label>
			<textarea class="form-control" name="description" rows="3"><?= $product['description'] ?></textarea>
		</div>
		<div class="mb-3">
			<label class="form-label">Price (€)</label>
			<input type="number" name="price" class="form-control" step="0.01" value="<?= $product['price'] ?>" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Stock</label>
			<input type="number" name="stock" class="form-control" value="<?= $product['stock_quantity'] ?>" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Current Image</label><br>
			<img src="uploads/<?= $product['image_url'] ?>" width="100">
		</div>
		<div class="mb-3">
			<label class="form-label">Upload New Image</label>
			<input type="file" name="image" class="form-control">
		</div>
		<button type="submit" class="btn btn-dark w-100">Update Product</button>
		<a href="add_product.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
	</form>
</div>

<?php include 'templates/footer.php' ?>

</html>