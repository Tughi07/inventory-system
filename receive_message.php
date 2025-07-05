<?php
$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

// Unset immediately to prevent re-display on refresh
if (isset($_SESSION['success_message'])) {
  unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
  unset($_SESSION['error_message']);
}
