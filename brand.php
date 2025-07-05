<?php
include 'admin_required.php';

// Handle product addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];

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
      $stmt = $conn->prepare("INSERT INTO brands (name, image_url) VALUES (?, ?)");
      $stmt->bind_param("ss", $name, $image);
      $stmt->execute();
      $_SESSION['success_message'] = 'New brand added successfully!';
    } else {
      $_SESSION['success_error'] = 'Image upload failed. Check folder permission.';
    }
  }
}

// brands per page
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$searchParam = '%' . $conn->real_escape_string($search) . '%';

if (!empty($search)) {
  // Count total matching brands
  $countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM brands WHERE name LIKE ? OR description LIKE ?");
  $countStmt->bind_param("ss", $searchParam, $searchParam);
  $countStmt->execute();
  $countResult = $countStmt->get_result();
  $totalRow = $countResult->fetch_assoc();
  $totalbrands = $totalRow['total'];
  $countStmt->close();

  $totalPages = ceil($totalbrands / $limit);

  // Fetch matching brands
  $stmt = $conn->prepare("SELECT * FROM brands WHERE name LIKE ? ORDER BY name DESC LIMIT ? OFFSET ?");
  $stmt->bind_param("sii", $searchParam, $limit, $offset);
} else {
  // Count all brands
  $countResult = $conn->query("SELECT COUNT(*) AS total FROM brands");
  $totalRow = $countResult->fetch_assoc();
  $totalbrands = $totalRow['total'];
  $totalPages = ceil($totalbrands / $limit);

  // Fetch all brands
  $stmt = $conn->prepare("SELECT * FROM brands ORDER BY name DESC LIMIT ? OFFSET ?");
  $stmt->bind_param("ii", $limit, $offset);
}

// Fetch paginated brands
$stmt->execute();
$productResult = $stmt->get_result();
if (!$productResult) {
  die("Query error: " . $conn->error);
}

include 'receive_message.php';

$title = 'Add Product';
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php'; ?>

<div class="container h-100 pt-3">
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

  <div class="mt-3">
    <div class="d-flex justify-content-between align-items-center">
      <h4>Product List</h4>
      <form method="GET">
        <div class="input-group" style="max-width: 400px;">
          <input type="text" name="search" class="form-control" placeholder="Search brands..." value="<?= htmlspecialchars($_GET['search'] ?? NULL) ?>">
          <button type="submit" class="btn btn-dark">Search</button>
        </div>
      </form>
    </div>
    <table class="table table-bordered mt-2">
      <thead>
        <tr>
          <th>Logo</th>
          <th>Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($productResult->num_rows == 0): ?>
          <tr>
            <td colspan="6">No records found.</td>
          </tr>
        <?php endif ?>
        <?php while ($row = $productResult->fetch_assoc()): ?>
          <tr>
            <td>
              <?php if (!empty($row['image_url']) && file_exists("uploads/" . $row['image_url'])): ?>
                <img src="uploads/<?= $row['image_url'] ?>" width="60" height="60" style="object-fit: cover;">
              <?php else: ?>
                <img class="placeholder" width="60" height="60">
              <?php endif ?>
            </td>
            <td><?= $row['name'] ?></td>
            <td>
              <a href="edit_brand.php?id=<?= $row['id'] ?>" class="btn btn-dark btn-sm">Edit</a>
              <a href="delete_brand.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
          </tr>
        <?php endwhile ?>
      </tbody>
    </table>
    <?php if ($totalPages > 1): ?>
      <nav>
        <ul class="pagination justify-content-center mt-3">
          <?php if ($page > 1): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
            </li>
          <?php endif ?>

          <?php
          $range = 2; // pages before/after current
          $ellipsis_shown = false;

          for ($i = 1; $i <= $totalPages; $i++) {
            if (
              $i <= 1 ||
              $i > $totalPages - 1 ||
              ($i >= $page - $range && $i <= $page + $range) // around current page
            ) {
              $active = ($i == $page) ? 'active' : '';
              echo '<li class="page-item ' . $active . '">
                        <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                      </li>';
              $ellipsis_shown = false;
            } else {
              if (!$ellipsis_shown) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                $ellipsis_shown = true;
              }
            }
          }
          ?>

          <?php if ($page < $totalPages): ?>
            <li class="page-item">
              <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
            </li>
          <?php endif ?>
        </ul>
      </nav>
    <?php endif ?>
  </div>
  <form action="" method="post" enctype="multipart/form-data" class="p-3 border rounded bg-white shadow-sm my-3">
    <h5>Add Brand</h5>
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Upload Logo</label>
      <input type="file" name="image" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-dark w-100">Add Brand</button>
  </form>
</div>

<?php include 'templates/footer.php'; ?>

</html>