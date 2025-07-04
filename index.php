<?php
session_start();
include 'config.php';

// Pull 3 featured sneakers randomly
$result = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SneakerVault - Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">SneakerVault</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="store.php">Shop</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>

      <form action="store.php" method="get" class="d-flex">
        <input class="form-control me-2" type="search" name="search" placeholder="Search sneakers..." aria-label="Search">
        <button class="btn btn-outline-light" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>

<section class="hero-section text-white d-flex align-items-center justify-content-center text-center">
  <div class="overlay"></div>
  <div class="content">
    <h1 class="display-4 fw-bold">Step Up Your Sneaker Game</h1>
    <p class="lead mb-4">Shop fresh drops & iconic kicks from top brands. 100% Authentic. Fast shipping.</p>
    <a href="store.php" class="btn btn-dark btn-lg px-5">Shop Now</a>
  </div>
</section>

<div class="container my-5">
  <h2 class="text-center mb-4">🔥 Featured Sneakers</h2>
  <div class="row">
    <?php while ($row = $result->fetch_assoc()) { ?>
      <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
          <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
            <p class="card-text">$<?= number_format($row['price'], 2) ?></p>
            <p class="text-warning mb-2"><i class="bi bi-star-fill"></i> 4.8</p>
            <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark w-100">Add to Cart</a>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>

<section class="bg-light py-4">
  <div class="container text-center">
    <h5 class="fw-bold mb-2">Trusted by 1,000+ Sneakerheads Worldwide</h5>
    <p class="text-muted">Shop with confidence — secure checkout & guaranteed authenticity.</p>
  </div>
</section>

<section class="container my-5">
  <h3 class="mb-4 text-center">Shop by Brands</h3>

  <div class="row g-4 justify-content-center">

    <!-- Nike -->
    <div class="col-6 col-md-3">
      <a href="brand.php?brand=Nike" class="text-decoration-none">
        <div class="card shadow-sm border-0 text-center p-3">
          <img src="uploads/nike-logo.png" class="brand-logo mb-2" alt="Nike">
          <h6 class="mb-0">Nike</h6>
        </div>
      </a>
    </div>

    <!-- Adidas -->
    <div class="col-6 col-md-3">
      <a href="brand.php?brand=Adidas" class="text-decoration-none">
        <div class="card shadow-sm border-0 text-center p-3">
          <img src="uploads/adidas-logo.png" class="brand-logo mb-2" alt="Adidas">
          <h6 class="mb-0">Adidas</h6>
        </div>
      </a>
    </div>

    <!-- Puma -->
    <div class="col-6 col-md-3">
      <a href="brand.php?brand=Puma" class="text-decoration-none">
        <div class="card shadow-sm border-0 text-center p-3">
          <img src="uploads/puma-logo.png" class="brand-logo mb-2" alt="Puma">
          <h6 class="mb-0">Puma</h6>
        </div>
      </a>
    </div>
  </div>
</section>

<footer class="bg-dark text-white text-center py-4">
  <div class="mb-2">
    <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
    <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
    <a href="#" class="text-white"><i class="bi bi-twitter"></i></a>
  </div>
  <p class="mb-0">&copy; 2025 SneakerVault. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>