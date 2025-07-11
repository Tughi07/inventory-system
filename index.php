<?php
session_start();
include 'config.php';

// Pull 5 featured sneakers randomly
$productResult = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 5");
$brandResult = $conn->query("SELECT * FROM brands ORDER BY name");
$currentPage = 'home';
$title = 'Sneaker Vault - Home';
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php' ?>

<section class="hero-section text-white d-flex align-items-center justify-content-center text-center">
  <div class="overlay"></div>
  <div class="content">
    <h1 class="display-4 fw-bold">Step Up Your Sneaker Game</h1>
    <p class="lead mb-4">Shop fresh drops & iconic kicks from top brands. 100% Authentic. Fast shipping.</p>
    <a href="store.php" class="btn btn-dark btn-lg px-5">Shop Now</a>
  </div>
</section>

<div class="container my-5">
  <div class="d-flex align-items-center">
    <h2 class="text-center mb-4">Featured Sneakers</h2>
    <a href="store.php" class="ms-auto">See More Shoes</a>
  </div>
  <div class="row">
    <?php while ($row = $productResult->fetch_assoc()): ?>
      <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
          <img src="uploads/<?= htmlspecialchars($row['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
            <p class="card-text">$<?= number_format($row['price'], 2) ?></p>
            <p class="text-warning mb-2"><i class="bi bi-star-fill"></i> 4.8</p>
            <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark w-100">Add to Cart</a>
          </div>
        </div>
      </div>
    <?php endwhile ?>
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
  <div class="row justify-content-center align-items-stretch">
    <?php while ($row = $brandResult->fetch_assoc()): ?>
      <div class="col-6 col-md-3 d-flex align-items-stretch">
        <a href="store.php?brand=<?= $row['id'] ?>" class="text-decoration-none w-100 h-100 d-flex align-items-stretch">
          <div class="card shadow-sm border-0 text-center p-3 flex-fill h-100 d-flex flex-column justify-content-center">
            <img src="uploads/<?= $row['image_url'] ?>" class="brand-logo mb-2 h-50" alt="<?= $row['name'] ?>">
            <h6 class="mb-0"><?= $row['name'] ?></h6>
          </div>
        </a>
      </div>
    <?php endwhile ?>
  </div>
</section>

<?php include 'templates/footer.php' ?>

</html>