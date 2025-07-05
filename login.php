<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$email = $_POST['email'];
	$password = $_POST['password'];
	// Prepare the statement to fetch `id`, `email`, and `role`
	$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		if (password_verify($password, $row['password_hash'])) {
			// Start session securely
			session_regenerate_id(true);
			// Create user_session in the database and
			// save the token to the session
			$token = bin2hex(random_bytes(32));
			$user_id = $row['id'];
			$current = new DateTime();
			$expires_at = $current->modify("+3 days")->format('Y-m-d H:i:s');

			// Insert the session token into user_sessions table
			$insert_stmt = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
			$insert_stmt->bind_param("iss", $user_id, $token, $expires_at);
			$insert_stmt->execute();

			$_SESSION['session_token'] = $token;
			$_SESSION['user_id'] = $row['id']; // Store user ID correctly
			$_SESSION['user'] = $row['email']; // Store email
			$_SESSION['role'] = $row['role']; // Store role

			// Redirect based on role
			header('Location: index.php');
			exit();
		} else {
			$error = "Invalid email or password!";
		}
	} else {
		$error = "Invalid email or password!";
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
				<label class="form-label">Email:</label>
				<input type="email" name="email" class="form-control" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Password:</label>
				<input type="password" name="password" class="form-control">
			</div>
			<button type="submit" class="btn btn-primary w-100">Login</button>
		</form>

		<p class="text-center mt-3">
			<a href="register.php">Create an account</a>
		</p>
	</div>

</body>

</html>