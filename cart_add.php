<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($action === 'add' && $productId > 0) {
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += 1;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }
    
    $totalItems = array_sum($_SESSION['cart']);
    echo json_encode(['success' => true, 'cartCount' => $totalItems]);
    exit;
}

if ($action === 'remove' && $productId > 0) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
    $totalItems = array_sum($_SESSION['cart']);
    echo json_encode(['success' => true, 'cartCount' => $totalItems]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
