<?php
include 'config.php';
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sales_report.xls");

$result = $conn->query("SELECT sales.*, products.name FROM sales JOIN products ON sales.product_id = products.id");

echo "Product\tQuantity\tTotal Price\tSale Date\n";
while ($row = $result->fetch_assoc()) {
    echo "{$row['name']}\t{$row['quantity']}\t\${$row['total_price']}\t{$row['sale_date']}\n";
}
?>