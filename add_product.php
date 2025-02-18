<?php
session_start();
include 'config.php';

// Ensure only admins can access
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle product addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Handle image upload
    $target_dir = "uploads/";
    $image = $_FILES["image"]["name"];
    $image_tmp = $_FILES["image"]["tmp_name"];
    $target_file = $target_dir . basename($image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file type (Only allow JPG, PNG, GIF)
    $allowed_types = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_types)) {
        echo "<p style='color:red;'>Error: Only JPG, JPEG, PNG, and GIF files are allowed.</p>";
    } else {
        // Move uploaded file
        if (move_uploaded_file($image_tmp, $target_file)) {
            // Save product in database
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdss", $name, $description, $price, $stock, $image);
            $stmt->execute();
            echo "<p style='color:green;'>Product added successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error: Image upload failed. Check folder permissions.</p>";
        }
    }
}

// Fetch products
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-center">📦 Manage Products</h2>

    <form action="" method="post" enctype="multipart/form-data" class="p-3 border rounded bg-white shadow-sm">
        <h5>Add Product</h5>
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <input type="text" name="description" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price ($)</label>
            <input type="number" name="price" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload Image</label>
            <input type="file" name="image" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Product</button>
    </form>

    <h4 class="mt-4">Product List</h4>
    <table class="table table-bordered mt-2">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?php if (!empty($row['image']) && file_exists("uploads/" . $row['image'])) { ?>
                            <img src="uploads/<?= $row['image'] ?>" width="60" height="60" style="object-fit: cover;">
                        <?php } else { ?>
                            <img src="default.jpg" width="60" height="60" style="object-fit: cover;">
                        <?php } ?>
                    </td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td>$<?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['stock'] ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_product.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>