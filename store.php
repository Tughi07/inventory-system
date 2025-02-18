<?php
session_start();
include 'config.php';

// Fetch products from database
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="text-center">🛍 Online Store</h2>
        <div>
            <a href="cart.php" class="btn btn-warning">🛒 Cart</a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row mt-4">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="uploads/<?= $row['image'] ?>" class="card-img-top" alt="<?= $row['name'] ?>" style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"> <?= $row['name'] ?> </h5>
                        <p class="card-text text-muted"> <?= $row['description'] ?> </p>
                        <p><strong>Price:</strong> $<?= number_format($row['price'], 2) ?></p>
                        <p><strong>Stock:</strong> <?= $row['stock'] ?></p>
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="product_name" value="<?= $row['name'] ?>">
                            <input type="hidden" name="price" value="<?= $row['price'] ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?= $row['stock'] ?>" class="form-control mb-2" required>
                            <button type="submit" class="btn btn-success w-100">🛒 Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
