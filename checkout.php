<?php
session_start();
include 'config.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>Your cart is empty.</h2>";
    exit();
}

$total_price = 0;
$order_summary = "";

foreach ($_SESSION['cart'] as $id => $item) {
    // Ensure price and quantity are extracted correctly
    $name = $item['name'];
    $price = (float) $item['price']; // Convert price to float
    $quantity = (int) $item['quantity']; // Convert quantity to integer
    $image = $item['image'];

    // Calculate total for each item
    $item_total = $price * $quantity;
    $total_price += $item_total; // Add to total price

    // Append order summary
    $order_summary .= "
        <div class='d-flex align-items-center mb-3'>
            <img src='uploads/$image' width='60' height='60' class='me-3' style='object-fit: cover;'>
            <div>
                <strong>$name</strong><br>
                Price: $" . number_format($price, 2) . "<br>
                Quantity: $quantity<br>
                <strong>Total:</strong> $" . number_format($item_total, 2) . "
            </div>
        </div>
    ";
}

$paypal_client_id = "YOUR_PAYPAL_CLIENT_ID"; // Replace with your actual client ID
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=<?= $paypal_client_id ?>&currency=USD"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center"><i class="fas fa-shopping-cart"></i> Checkout</h2>

    <div class="card p-4 shadow">
        <h5 class="text-center">Order Summary</h5>
        <hr>
        <?= $order_summary ?>
        <hr>
        <p class="text-end"><strong>Total Amount: </strong> $<?= number_format($total_price, 2) ?></p>

        <div class="text-center">
            <div id="paypal-button-container"></div>
        </div>
    </div>
</div>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: { value: '<?= $total_price ?>' }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                window.location.href = "order_success.php";
            });
        }
    }).render('#paypal-button-container');
</script>

</body>
</html>