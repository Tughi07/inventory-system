<?php
session_start();
include('config.php');

// Ensure only logged user can access
$isLoggedIn = false;
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
    if ($token_expires_at < time()) {
      header("Location: login.php");
      exit();
    } else {
      $_SESSION['user_id'] = $data['user_id'];
    }
  } else {
    header("Location: login.php");
    exit();
  }
} else {
  header("Location: login.php");
  exit();
}
