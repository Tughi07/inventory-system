<?php 
  session_start();
  include('config.php');

  // Ensure only logged user can access
  if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
  }
?>