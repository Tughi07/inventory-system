<?php
include 'config.php';
session_start();
include 'receive_message.php';
$title = 'Your Cart';
?>

<?php include 'templates/header.php' ?>

<div class="container py-4">
  <?php if ($success_message): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($success_message) ?>
    </div>
  <?php endif; ?>
  <?php if ($error_message): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($error_message) ?>
    </div>
  <?php endif; ?>
  <h2 class="text-center mb-4">
    Your Cart
  </h2>
  <?php if (!empty($_SESSION['cart'])): ?>
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-dark">
          <tr>
            <th>Image</th>
            <th>Sneaker</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total_price = 0;
          foreach ($_SESSION['cart'] as $id => $item):
            $subtotal = $item['price'] * $item['quantity'];
            $total_price += $subtotal;
            $img = !empty($item['image']) ? $item['image'] : 'placeholder.jpg';
          ?>
            <tr>
              <td>
                <img src="uploads/<?= htmlspecialchars($img) ?>" width="60" alt="<?= htmlspecialchars($item['name']) ?>">
              </td>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td>$<?= number_format($item['price'], 2) ?></td>
              <td><?= $item['quantity'] ?></td>
              <td>$<?= number_format($subtotal, 2) ?></td>
              <td>
                <a href="remove_from_cart.php?id=<?= $id ?>" class="btn btn-sm btn-danger">❌ Remove</a>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <h4>Total: <strong>$<?= number_format($total_price, 2) ?></strong></h4>
      <div>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        <a href="store.php" class="btn btn-dark">Continue Shopping</a>
      </div>
    </div>

  <?php else: ?>
    <div class="text-center p-5 bg-white shadow-sm rounded">
      <p>Your cart is empty.</p>
      <a href="index.php" class="btn btn-dark">Back to Shop</a>
    </div>
  <?php endif; ?>
</div>

</body>

</html>