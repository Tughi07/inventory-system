<?php
$host = 'localhost';
$db = 'inventory_system';
$user = 'inventory_system_user';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Create hashed passwords
    $adminPassword = password_hash('password', PASSWORD_DEFAULT);
    $customerPassword = password_hash('', PASSWORD_DEFAULT);

    // Insert admin user if not exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@example.com']);
    if (!$stmt->fetch()) {
        $insertStmt = $pdo->prepare("
            INSERT INTO users (email, password_hash, first_name, last_name, role)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $insertStmt->execute([
            'admin@example.com',
            $adminPassword,
            'Admin',
            'User',
            'admin'
        ]);
        echo "Admin account created.";
    } else {
        echo "Admin account already exists, skipping insert.\n";
    }

    // Insert customer user if not exists
    $stmt->execute(['customer@example.com']);
    if (!$stmt->fetch()) {
        $insertStmt->execute([
            'customer@example.com',
            $customerPassword,
            'Customer',
            'User',
            'customer'
        ]);
        echo "Customer account created.\n";
    } else {
        echo "Customer account already exists, skipping insert.\n";
    }

    echo "Seed complete.\n";
    echo "Admin: admin@example.com | Password: admin123\n";
    echo "Customer: customer@example.com | Password: user123\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit;
}
?>
