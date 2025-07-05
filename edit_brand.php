<?php
include 'admin_required.php';

// Get brand details
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$stmt = $conn->prepare("SELECT * FROM brands WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$brand = $result->fetch_assoc();

	$title = 'Edit Brand';
	if (!$brand) {
		echo "Product not found.";
		exit();
	} else {
		$title = "Edit Brand - {$brand['name']}";
	}
} else {
	echo "Invalid request.";
	exit();
}

// Handle brand update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name = $_POST['name'];

	// Handle image upload
	if (!empty($_FILES["image"]["name"])) {
		$target_dir = "uploads/";
		$image = $_FILES["image"]["name"];
		$target_file = $target_dir . basename($image);
		move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
	} else {
		$image = $brand['image_url'];
	}

	$stmt = $conn->prepare("UPDATE brands SET name=?, image_url=? WHERE id=?");
	$stmt->bind_param("ssdssi", $name, $image);
	if ($stmt->execute()) {
		$_SESSION['success_message'] = 'Brand updated successfuly.';
		header("Location: brand.php");
	} else {
		echo "Error updating brand.";
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php'; ?>

<div class="container my-4">
	<h2 class="text-center">Edit Brand <?= $brand['name'] ?></h2>

	<form action="" method="post" enctype="multipart/form-data" class="p-3 border rounded bg-white shadow-sm">
		<div class="mb-3">
			<label class="form-label">Brand Name</label>
			<input type="text" name="name" class="form-control" value="<?= $brand['name'] ?>" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Current Logo</label><br>
			<img src="uploads/<?= $brand['image_url'] ?>" width="100">
		</div>
		<div class="mb-3">
			<label class="form-label">Upload New Logo</label>
			<input type="file" name="image" class="form-control">
		</div>
		<button type="submit" class="btn btn-dark w-100">Update Brand</button>
		<a href="brand.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
	</form>
</div>

<?php include 'templates/footer.php' ?>

</html>