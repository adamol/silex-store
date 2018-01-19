<?php

require __DIR__.'/vendor/autoload.php';

$dsn = 'mysql:host=localhost;dbname=silex_store';
$user = 'root';
$pass = 'root';

$dbh = new PDO($dsn, $user, $pass);

echo "Dropping products table".PHP_EOL;
$dbh->query("DROP TABLE IF EXISTS products");

echo "Creating products table".PHP_EOL;
$dbh->query("
    CREATE TABLE products (
      id INT PRIMARY KEY AUTO_INCREMENT,
      display_name VARCHAR(50) NOT NULL,
      product_type VARCHAR(50) NOT NULL,
      price INT NOT NULL
    )
");

echo "Inserting product entries".PHP_EOL;
$productsRepository = new App\Products\Repository($dbh);
$productsRepository->insertMany([
    [
        'display_name' => 'Lenovo Legion',
        'product_type' => 'laptop',
        'price' => 10000,
    ],
    [
        'display_name' => 'Samsung Galaxy S7',
        'product_type' => 'tv',
        'price' => 10000,
    ]
]);
