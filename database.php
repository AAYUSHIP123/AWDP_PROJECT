<?php
/* The Wildflower Studio - database connection and automatic setup */
mysqli_report(MYSQLI_REPORT_OFF);

$host = '127.0.0.1';
$database = 'ecommerce';
$attempts = [
    ['root', 'rootpass', 3307],
    ['root', '', 3306],
    ['root', '', 3307],
    ['root', 'rootpass', 3306]
];

$conn = null;
foreach ($attempts as $a) {
    $test = @new mysqli($host, $a[0], $a[1], $database, $a[2]);
    if (!$test->connect_error) {
        $conn = $test;
        break;
    }
}

/* If the database does not exist, create it using a server-only connection. */
if (!$conn) {
    foreach ($attempts as $a) {
        $test = @new mysqli($host, $a[0], $a[1], '', $a[2]);
        if (!$test->connect_error) {
            $test->query("CREATE DATABASE IF NOT EXISTS `$database`");
            $test->select_db($database);
            $conn = $test;
            break;
        }
    }
}

if (!$conn || $conn->connect_error) {
    die('Database connection failed. Open XAMPP MySQL and check the MySQL port/password.');
}
$conn->set_charset('utf8mb4');

/* Create tables required by the assignment. Existing data is preserved. */
$tables = [
    "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(150) UNIQUE NOT NULL,mobile VARCHAR(20),password VARCHAR(255) NOT NULL,address TEXT,city VARCHAR(80),state VARCHAR(80),pincode VARCHAR(10),created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) UNIQUE NOT NULL,image VARCHAR(255),description TEXT,status ENUM('active','inactive') DEFAULT 'active',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(150) NOT NULL,description TEXT,price DECIMAL(10,2) NOT NULL DEFAULT 0,discount DECIMAL(10,2) DEFAULT 0,stock INT DEFAULT 0,category VARCHAR(100),category_id INT NULL,image VARCHAR(255),status ENUM('active','inactive') DEFAULT 'active',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS orders (id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,name VARCHAR(100),email VARCHAR(150),mobile VARCHAR(20),billing_address TEXT,shipping_address TEXT,city VARCHAR(80),state VARCHAR(80),pincode VARCHAR(10),total DECIMAL(10,2),payment_method VARCHAR(50),payment_status VARCHAR(30) DEFAULT 'Pending',order_status VARCHAR(30) DEFAULT 'Pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS order_items (id INT AUTO_INCREMENT PRIMARY KEY,order_id INT NOT NULL,product_id INT,name VARCHAR(150),price DECIMAL(10,2),quantity INT)",
    "CREATE TABLE IF NOT EXISTS contacts (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100),phone VARCHAR(30),email VARCHAR(150),message TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS admins (id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(80) UNIQUE,password VARCHAR(255) NOT NULL)"
];
foreach ($tables as $sql) $conn->query($sql);

/* Add columns when an older version of the project already has products. */
$columns = [
    'discount' => 'DECIMAL(10,2) DEFAULT 0',
    'stock' => 'INT DEFAULT 0',
    'category' => 'VARCHAR(100)',
    'category_id' => 'INT NULL',
    'status' => "ENUM('active','inactive') DEFAULT 'active'",
    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
];
foreach ($columns as $column => $definition) {
    $safe = $conn->real_escape_string($column);
    $check = $conn->query("SHOW COLUMNS FROM products LIKE '$safe'");
    if ($check && $check->num_rows === 0) $conn->query("ALTER TABLE products ADD `$column` $definition");
}

/* Add contact phone storage for existing contact form submissions. */
$contactPhoneColumn = $conn->query("SHOW COLUMNS FROM contacts LIKE 'phone'");
if ($contactPhoneColumn && $contactPhoneColumn->num_rows === 0) {
    $conn->query('ALTER TABLE contacts ADD phone VARCHAR(30) NULL AFTER name');
}

/* Convert old text categories into the new category table. */
$count = $conn->query("SELECT COUNT(*) AS n FROM categories");
if ($count && (int)$count->fetch_assoc()['n'] === 0) {
    $old = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND TRIM(category) <> ''");
    if ($old) {
        while ($row = $old->fetch_assoc()) {
            $name = trim($row['category']);
            $st = $conn->prepare("INSERT IGNORE INTO categories(name,description,status) VALUES(?, 'Handmade products', 'active')");
            $st->bind_param('s', $name);
            $st->execute();
        }
    }
}
$conn->query("UPDATE products p JOIN categories c ON LOWER(TRIM(p.category))=LOWER(TRIM(c.name)) SET p.category_id=c.id WHERE p.category_id IS NULL");

/* Create the demo admin account if it is not already present. */
$hash = password_hash('admin@123', PASSWORD_DEFAULT);
$st = $conn->prepare('INSERT IGNORE INTO admins(username,password) VALUES(?,?)');
$admin = 'admin';
$st->bind_param('ss', $admin, $hash);
$st->execute();
?>
