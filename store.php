<?php
session_start();
include 'config.php';

// Get the search term from the URL if it exists
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base query: get all products
$query = "SELECT * FROM products WHERE 1";

// If search is NOT empty, filter products
if (!empty($search)) {
  $safe_search = $conn->real_escape_string($search);
  $query .= " AND name LIKE '%$safe_search%'";
}

// Run the query
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SneakerVault - Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">SneakerVault</a>

    <!-- Mobile toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar content -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <!-- Nav links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="store.php">Shop</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
      </ul>

      <!--  search bar -->
      <form action="store.php" method="get" class="d-flex">
        <input class="form-control me-2" type="search"
               name="search"
               placeholder="Search sneakers..."
               value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-light" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>

<!-- SHOP ALL PRODUCTS -->
<div class="container my-5">
  <h2 class="text-center mb-4">All Sneakers</h2>
  <div class="row">
    <?php while ($row = $result->fetch_assoc()) { ?>
      <div class="col-md-3">
        <div class="card mb-4 shadow-sm">
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
            <p class="card-text">$<?= number_format($row['price'], 2) ?></p>
            <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="btn btn-dark w-100">Add to Cart</a>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>

<!-- FOOTER -->
<footer class="bg-dark text-white text-center py-3">
  &copy; 2025 SneakerVault. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>