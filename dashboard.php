<?php
include 'admin_required.php';

$totalSales = $conn->query("SELECT SUM(total_price) AS total FROM sales")->fetch_assoc();
$topProduct = $conn->query("SELECT product_id, COUNT(*) AS sales_count FROM sales GROUP BY product_id ORDER BY sales_count DESC LIMIT 1")->fetch_assoc();

echo "<h2>Dashboard</h2>";
echo "<p>Total Sales: $" . $totalSales['total'] . "</p>";

if ($topProduct) {
    $product = $conn->query("SELECT name FROM products WHERE id = " . $topProduct['product_id'])->fetch_assoc();
    echo "<p>Best-Selling Product: " . $product['name'] . " (Sold " . $topProduct['sales_count'] . " times)</p>";
} else {
    echo "<p>No sales data available yet.</p>";
}
?>