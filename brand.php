<?php
session_start();
include 'config.php';

// Get brand from URL
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : '';

if (empty($brand)) {
  header("Location: index.php");
  exit();
}

$stmt = $conn->prepare("SELECT * FROM products WHERE brand = ?");
$stmt->bind_param("s", $brand);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= htmlspecialchars($brand) ?> Sneakers - SneakerVault</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>

<!-- ✅ Navbar same as index.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">SneakerVault</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="store.php">Shop</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
      </ul>

      <form action="store.php" method="get" class="d-flex">
        <input class="form-control me-2" type="search" name="search" placeholder="Search sneakers...">
        <button class="btn btn-outline-light" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>

<div class="container my-5">
  <h2 class="mb-4 text-center"><?= htmlspecialchars($brand) ?> Sneakers</h2>

  <?php if ($result->num_rows > 0): ?>
    <div class="row">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top"
                 alt="<?= htmlspecialchars($row['name']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
              <p class="card-text">$<?= number_format($row['price'], 2) ?></p>
              <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="btn btn-dark">Add to Cart</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p>No sneakers found for brand <strong><?= htmlspecialchars($brand) ?></strong>.</p>
    <a href="index.php" class="btn btn-secondary">Back to Home</a>
  <?php endif; ?>
</div>

</body>
</html>