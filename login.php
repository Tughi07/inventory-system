<?php
include 'config.php';
session_start();
$title = "Login";

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
			$_SESSION['error_message'] = "Invalid email or password!";
		}
	} else {
		$_SESSION['error_message'] = "Invalid email or password!";
	}
}

include 'receive_message.php';

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php' ?>

<div class="h-100 d-flex justify-content-center align-items-start hero-section">
	<div class="card p-4 shadow mt-5" style="width: 400px;">
		<h3 class="text-center">Login to your account</h3>

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

		<form method="post">
			<div class="mb-3">
				<label class="form-label">Email:</label>
				<input type="email" name="email" class="form-control" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Password:</label>
				<input type="password" name="password" class="form-control">
			</div>
			<button type="submit" class="btn btn-dark w-100">Login</button>
		</form>

		<p class="text-center mt-3">
			<a href="register.php">Create an account</a>
		</p>
	</div>
</div>

<?php include 'templates/footer.php' ?>

</html>