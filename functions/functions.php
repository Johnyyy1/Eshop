<?php

function getAllCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

function getCategoryById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getProductsByCategory($pdo, $category_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    return $stmt->fetchAll();
}

function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.id as category_id FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Funkce pro přidání produktu do košíku
function addToCart($product_id, $quantity = 1) {
    // Pokud produkt již v košíku existuje, zvýšíme množství
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        // Jinak přidáme nový produkt do košíku
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function updateCartItem($product_id, $quantity) {
    if ($quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
    } else {
        removeFromCart($product_id);
    }
}

function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function getCartItemCount() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $quantity) {
            $count += $quantity;
        }
    }
    return $count;
}

function getCartTotal($pdo) {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = getProductById($pdo, $product_id);
            if ($product) {
                $total += $product['price'] * $quantity;
            }
        }
    }
    return $total;
}

function getCartTotalWithVAT($pdo) {
    return priceWithVAT(getCartTotal($pdo));
}

function createOrder($pdo, $fullName, $street, $houseNumber, $postalCode, $shippingMethod, $totalPrice) {
    $stmt = $pdo->prepare("INSERT INTO orders (full_name, street, house_number, postal_code, shipping_method, total_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullName, $street, $houseNumber, $postalCode, $shippingMethod, $totalPrice]);
    return $pdo->lastInsertId();
}

function createOrderItems($pdo, $orderId) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = getProductById($pdo, $product_id);
        if ($product) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $product_id, $quantity, $product['price']]);
        }
    }
}

function clearCart() {
    $_SESSION['cart'] = [];
}

function getOrderById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getOrderItems($pdo, $orderId) {
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}
?>