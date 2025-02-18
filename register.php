<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Encrypt password
    $role = 'customer'; // Default role for new users

    // Check if username exists
    $checkUser = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($checkUser->num_rows > 0) {
        echo "<p style='color:red;'>Username already exists!</p>";
    } else {
        $conn->query("INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
        echo "<p style='color:green;'>Registration successful! <a href='login.php'>Login here</a></p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Register</h2>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>
</html>