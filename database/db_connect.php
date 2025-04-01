<?php

$host = 'localhost';
$db_name = 'eshop_db1';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die('Nepodařilo se připojit k databázi. Chyba: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' Kč';
}

function priceWithVAT($price) {
    return $price * 1.21;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>