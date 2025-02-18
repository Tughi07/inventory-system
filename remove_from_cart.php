<?php
session_start();
if (isset($_GET['id'])) {
    unset($_SESSION['cart'][$_GET['id']]);
}
header("Location: cart.php");
exit();
?>
<a href="remove_from_cart.php?id=<?= $id ?>" class="btn btn-danger btn-sm">Remove</a>