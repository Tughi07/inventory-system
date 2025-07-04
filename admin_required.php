<?php
  include('login_required.php');

  if ($_SESSION['role'] != 'admin') {
    header("HTTP/1.0 403 Forbidden");
    exit();
  }
?>