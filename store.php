<?php
session_start();
include 'config.php';

$limit = 16;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$filters = [];
$params = [];
$types = '';

$brandResult = $conn->query("SELECT * FROM brands ORDER BY name");

// Filter: gender
if (!empty($_GET['gender'])) {
  $filters[] = "gender = ?";
  $params[] = $_GET['gender'];
  $types .= "s";
}

// Filter: brand
if (!empty($_GET['brand'])) {
  $filters[] = "brand_id = ?";
  $params[] = (int)$_GET['brand'];
  $types .= "i";
}

// Filter: price range
if (!empty($_GET['price_range'])) {
  switch ($_GET['price_range']) {
    case '0-50':
      $filters[] = "price BETWEEN 0 AND 50";
      break;
    case '51-100':
      $filters[] = "price BETWEEN 51 AND 100";
      break;
    case '101-200':
      $filters[] = "price BETWEEN 101 AND 200";
      break;
    case '201+':
      $filters[] = "price >= 201";
      break;
  }
}

// Get the search term from the URL if it exists
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base query: get all products
$query = "SELECT * FROM products";
$where = "";
$searchParam = null;

if (count($filters) > 0) {
  $where = " WHERE " . implode(" AND ", $filters);
}

if (!empty($search)) {
  if ($where) {
    $where .= " AND name LIKE ?";
  } else {
    $where = " WHERE name LIKE ?";
  }
  $searchParam = "%$search%";
  $types .= "s";
  $params[] = $searchParam;
}

$query .= $where;
$query .= " LIMIT ? OFFSET ?";
$types .= "ii";
$params[] = $limit;
$params[] = $offset;

// Prepare statement
$stmt = $conn->prepare($query);

if ($stmt === false) {
  die("Prepare failed: " . $conn->error);
}

if ($types) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$productResult = $stmt->get_result();
$currentPage = 'shop';
$title = 'SneakerValut - Shopping'
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php' ?>

<div class="container h-100">
  <?php if ($isAdmin): ?>
    <div class="d-flex justify-content-between align-items-center my-4">
      <h2>All Sneakers</h2>
      <a type="button" class="btn btn-dark" href="add_product.php">Add Product</a>
    </div>
  <?php else: ?>
    <h2 class="my-4">All Sneakers</h2>
  <?php endif ?>
  <form method="GET" class="mb-3 row g-2 justify-content-center">

    <div class="col-auto">
      <select name="gender" class="form-select">
        <option value="">Gender (All)</option>
        <option value="male" <?= (isset($_GET['gender']) && $_GET['gender'] == 'male') ? 'selected' : '' ?>>Male</option>
        <option value="female" <?= (isset($_GET['gender']) && $_GET['gender'] == 'female') ? 'selected' : '' ?>>Female</option>
        <option value="unisex" <?= (isset($_GET['gender']) && $_GET['gender'] == 'unisex') ? 'selected' : '' ?>>Unisex</option>
      </select>
    </div>

    <div class="col-auto">
      <select name="brand" class="form-select">
        <option value="">Brand (All)</option>
        <?php while ($row = $brandResult->fetch_assoc()): ?>
          <?php $selected = (isset($_GET['brand']) && $_GET['brand'] == $row['id']) ? 'selected' : ''; ?>
          <?php echo "<option value=\"{$row['id']}\" $selected>{$row['name']}</option>"; ?>
        <?php endwhile ?>
      </select>
    </div>

    <div class="col-auto">
      <select name="price_range" class="form-select">
        <option value="">Price Range (All)</option>
        <option value="0-50" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '0-50') ? 'selected' : '' ?>>€0 - €50</option>
        <option value="51-100" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '51-100') ? 'selected' : '' ?>>€51 - €100</option>
        <option value="101-200" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '101-200') ? 'selected' : '' ?>>€101 - €200</option>
        <option value="201+" <?= (isset($_GET['price_range']) && $_GET['price_range'] == '201+') ? 'selected' : '' ?>>€201+</option>
      </select>
    </div>

    <div class="col-auto">
      <button type="submit" class="btn btn-dark">Filter</button>
    </div>

  </form>
  <div class="row mb-3">
    <?php if ($productResult->num_rows === 0):  ?>
      <h2 class="text-center">Search result empty</h2>
    <?php endif ?>
    <?php while ($row = $productResult->fetch_assoc()):  ?>
      <div class="col-md-3">
        <div class="card shadow-sm">
          <img src="uploads/<?= htmlspecialchars($row['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
            <p class="card-text">$<?= number_format($row['price'], 2) ?></p>
            <a href="add_to_cart.php?id=<?= $row['id'] ?>" class="btn btn-dark w-100">Add to Cart</a>
          </div>
        </div>
      </div>
    <?php endwhile ?>
  </div>
</div>

<?php include 'templates/footer.php' ?>

</html>