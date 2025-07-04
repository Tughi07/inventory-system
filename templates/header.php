<?php
include 'config.php';
$isLoggedIn = false;
$isAdmin = false;
if (isset($_SESSION['session_token'])) {
  $token = $_SESSION['session_token'];
  include 'config.php';
  $stmt = $conn->prepare(
    "SELECT user_sessions.expires_at, users.id AS user_id, 
    users.role FROM user_sessions JOIN users ON 
    user_sessions.user_id = users.id WHERE user_sessions.session_token=?"
  );
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows == 1) {
    $data = $result->fetch_assoc();
    $token_expires_at = strtotime($data['expires_at']);
    if ($token_expires_at > time()) {
      $isLoggedIn = true;
      // check for role
      if ($data['role'] == 'admin') {
        $isAdmin = true;
      }
    }
  }
}
?>

<head>
  <meta charset="UTF-8" />
  <title>SneakerVault</title>
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
        </ul>

        <form action="store.php" method="get" class="d-flex">
          <input class="form-control me-2" type="search" name="search" placeholder="Search sneakers..." aria-label="Search">
          <button class="btn btn-outline-light" type="submit">Search</button>
        </form>
        <ul class="navbar-nav mb-2 mb-lg-0">
          <?php if ($isLoggedIn): ?>
            <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i></a></li>
            <?php if ($isAdmin): ?>
              <li class="nav-item"><a class="nav-link" href="admin_orders.php">Orders</a></li>
            <?php endif ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
          <?php endif ?>
        </ul>
      </div>
    </div>
  </nav>