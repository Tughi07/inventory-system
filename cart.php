<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>🛒 Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-center">🛒 Shopping Cart</h2>

    <?php if (!empty($_SESSION['cart'])) { ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_price = 0;
                foreach ($_SESSION['cart'] as $id => $product) {
                    $subtotal = $product['price'] * $product['quantity'];
                    $total_price += $subtotal;
                    
                    // Ensure the image exists
                    $imagePath = !empty($product['image']) ? "uploads/" . htmlspecialchars($product['image']) : "placeholder.png";
                ?>
                <tr>
                    <td>
                        <img src="<?= $imagePath ?>" 
                             width="60" height="60" 
                             style="object-fit: cover; border-radius: 5px;">
                    </td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td>$<?= number_format($product['price'], 2) ?></td>
                    <td>
                        <form action="update_cart.php" method="POST">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="number" name="quantity" value="<?= $product['quantity'] ?>" 
                                   min="1" max="10" class="form-control" style="width: 80px; display: inline;">
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                    <td><strong>$<?= number_format($subtotal, 2) ?></strong></td>
                    <td>
                        <a href="remove_from_cart.php?id=<?= $id ?>" 
                           class="btn btn-danger btn-sm">❌ Remove</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <div class="d-flex justify-content-between align-items-center">
            <h4>Total: <strong>$<?= number_format($total_price, 2) ?></strong></h4>
            <div>
                <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
                <a href="store.php" class="btn btn-secondary btn-lg">Continue Shopping</a>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center">
            <h4>Your cart is empty.</h4>
            <a href="store.php" class="btn btn-primary">Go to Store</a>
        </div>
    <?php } ?>
</div>

</body>
</html>