<?php
session_start();
include 'config.php';
$title = 'Create your account';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$firstName = $_POST['first_name'];
	$lastName = $_POST['last_name'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	if ($_POST['password'] !== $_POST['repeat_password']) {
		$_SESSION['error_message'] = "Passwords do not match";
		header('register.php');
	} else {
		// Check if email exists
		$checkUser = $conn->query("SELECT * FROM users WHERE email='$email'");
		if ($checkUser->num_rows > 0) {
			$_SESSION['error_message'] = "Email already used";
			header('register.php');
		} else {
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
			$conn->query("INSERT INTO users (email, password_hash, first_name, last_name) VALUES ('$email', '$hashedPassword', '$firstName', '$lastName')");
			$_SESSION['success_message'] = "Your account has been created. You can login now.";
			header('login.php');
		}
	}
}

include 'receive_message.php';

?>

<!DOCTYPE html>
<html lang="en">

<?php include 'templates/header.php' ?>

<div class="h-100 d-flex justify-content-center align-items-start hero-section">
	<div class="card p-4 shadow mt-5" style="width: 400px;">
		<h3 class="text-center">Create your account</h3>

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
				<label class="form-label">First Name:</label>
				<input type="text" name="first_name" class="form-control" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Last Name:</label>
				<input type="text" name="last_name" class="form-control" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Email:</label>
				<input type="email" name="email" class="form-control" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Password:</label>
				<input type="password" name="password" class="form-control" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Password Repeat:</label>
				<input type="password" name="repeat_password" class="form-control" required>
			</div>
			<button type="submit" class="btn btn-dark w-100">Create Account</button>
		</form>
		<p class="text-center mt-3">
			<a href="login.php">Login to your account</a>
		</p>
	</div>
</div>

<?php include 'templates/footer.php' ?>

</html>