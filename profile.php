<?php
include 'login_required.php';

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $firstName = $_POST['first_name'];
  $lastName = $_POST['last_name'];
  $email = $_POST['email'];
  $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
  if (!empty($newPassword)) {
    if (!isset($_POST['repeat_password'])) {
      $_SESSION['error_message'] = "Must type repeat password";
      header('profile.php');
    } else {
      if ($_POST['new_password'] !== $_POST['repeat_password']) {
        $_SESSION['error_message'] = "Passwords do not match";
        header('profile.php');
      } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, password_hash=? WHERE id=?");
        $stmt->bind_param("ssssi", $firstName, $lastName, $email, $hashedPassword, $_SESSION['user_id']);
        $stmt->execute();
        $_SESSION['success_message'] = "Profile updated successfully";
        header('profile.php');
      }
    }
  } else {
    $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=? WHERE id=?");
    $stmt->bind_param("sssi", $firstName, $lastName, $email, $_SESSION['user_id']);
    $stmt->execute();
    $_SESSION['success_message'] = "Profile updated successfully";
    header('profile.php');
  }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// products per page
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';
$searchParam = '%' . $conn->real_escape_string($search) . '%';

// Count total matching products
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE user_id=?");
$countStmt->bind_param("i", $_SESSION['user_id']);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRow = $countResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$countStmt->close();

$totalPages = ceil($totalProducts / $limit);

// Fetch matching products
$pastOrders = $conn->prepare("SELECT 
    orders.*,
    order_items.price_at_purchase AS item_price,
    products.name AS product_name
FROM orders
INNER JOIN order_items ON orders.id = order_items.order_id
INNER JOIN products ON order_items.product_id = products.id
WHERE orders.user_id = ?
");
$pastOrders->bind_param("i", $_SESSION['user_id']);
$pastOrders->execute();
$pastOrderResult = $pastOrders->get_result();

include 'receive_message.php';

$title = 'Add Product';
?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php'; ?>

<div class="container py-3">
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

  <form action="" method="post" enctype="multipart/form-data" class="p-3 border rounded bg-white shadow-sm mt-5">
    <h5>My profile</h5>
    <div class="mb-3">
      <label class="form-label">First Name</label>
      <input type="text" name="first_name" class="form-control" value="<?= $user['first_name'] ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Last Name</label>
      <input type="text" name="last_name" class="form-control" value="<?= $user['last_name'] ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">New Password</label>
      <input type="password" name="new_password" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Repeat Password</label>
      <input type="password" name="repeat_password" class="form-control">
    </div>
    <button type="submit" class="btn btn-dark w-100">Save Changes</button>
  </form>
  <div class="mt-5">
    <div class="d-flex justify-content-between align-items-center">
      <h4>My Orders</h4>
      <form method="GET">
        <div class="input-group" style="max-width: 400px;">
          <input type="text" name="firstNamearch" class="form-control" placeholder="Search past orders..." value="<?= htmlspecialchars($_GET['search'] ?? NULL) ?>">
          <button type="submit" class="btn btn-dark">Search</button>
        </div>
      </form>
    </div>
    <table class="table table-bordered mt-2">
      <thead>
        <tr>
          <th>Item Names</th>
          <th>Total Price (€)</th>
          <th>Purchased Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($pastOrderResult->num_rows == 0): ?>
          <tr>
            <td colspan="6">No records found.</td>
          </tr>
        <?php endif ?>
        <?php while ($row = $pastOrderResult->fetch_assoc()): ?>
          <tr>
            <td><?= $row['product_name'] ?></td>
            <td>€<?= number_format($row['total_amount'], 2) ?></td>
            <td><?= $row['created_at'] ?></td>
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
          $range = 2;
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
</div>

<?php include 'templates/footer.php'; ?>

</html>