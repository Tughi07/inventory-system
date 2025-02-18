<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash password

    // Prepare the statement to fetch `id`, `username`, and `role`
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Start session securely
        session_regenerate_id(true); // Prevent session fixation

        $_SESSION['user_id'] = $row['id']; // Store user ID correctly
        $_SESSION['user'] = $row['username']; // Store username
        $_SESSION['role'] = $row['role']; // Store role

        // Redirect based on role
        if ($row['role'] === 'admin') {
            header("Location: inventory.php");
        } else {
            header("Location: store.php");
        }
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

<div class="card p-4 shadow" style="width: 350px;">
    <h3 class="text-center">Login</h3>
    
    <?php if (isset($error)) { ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php } ?>
    
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <p class="text-center mt-3">
        <a href="register.php">Create an account</a>
    </p>
</div>

</body>
</html>