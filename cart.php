<?php
session_start();
include 'config/database.php';
include 'includes/functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        header("Location: cart.php");
        exit;
    }
    if (isset($_POST['remove_item'])) {
        $idToRemove = intval($_POST['remove_item']);
        unset($_SESSION['cart'][$idToRemove]);
        header("Location: cart.php");
        exit;
    }
}

$cartItems = [];
$totalPrice = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    // Safe because keys are integers from our session logic, but still good to be careful
    $stmt = $pdo->query("SELECT * FROM spares WHERE id IN ($ids)");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['qty'] = $qty;
        $row['subtotal'] = $row['price'] * $qty;
        $totalPrice += $row['subtotal'];
        $cartItems[] = $row;
    }
}

include 'partials/header.php';
?>

<link rel="stylesheet" href="bmw-spares.css">

<body class="spares-page">
    <div style="height:80px; background:#111;"></div> <!-- spacer -->

    <div class="sp-container" style="padding: 60px 5%; min-height:60vh;">
        <h2 style="font-weight:900; font-size:2.5rem; margin-bottom:10px;">Your Shopping Cart</h2>
        <p style="color:var(--sp-text-muted); margin-bottom:40px;">Review your selected BMW spares and accessories.</p>

        <?php if (empty($cartItems)): ?>
            <div class="sp-glass-card" style="padding:50px; text-align:center;">
                <i class="fas fa-shopping-cart" style="font-size:3rem; color:var(--sp-text-muted); margin-bottom:20px;"></i>
                <h3 style="margin-bottom:10px;">Your cart is empty</h3>
                <p style="color:var(--sp-text-muted); margin-bottom: 30px;">Looks like you haven't added any premium parts yet.</p>
                <a href="bmw-spares.php#featured" class="sp-btn sp-btn--primary">Browse BMW Spares</a>
            </div>
        <?php else: ?>
            <div style="display:flex; gap:40px; flex-wrap:wrap; align-items:flex-start;">
                <!-- Cart Items List -->
                <div style="flex:2; min-width:300px;">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="sp-glass-card" style="display:flex; gap:20px; padding:20px; margin-bottom:15px; align-items:center;">
                        <div style="width:100px; height:100px; background:#111; overflow:hidden; border-radius:8px;">
                            <img src="<?= htmlspecialchars($item['image'] ?: 'https://images.unsplash.com/photo-1621685800588-433e5ab7dc10?w=200&q=80') ?>" alt="Part" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div style="flex-grow:1;">
                            <h4 style="font-size:1.1rem; margin-bottom:5px; font-weight:700;"><?= htmlspecialchars($item['name']) ?></h4>
                            <p style="font-size:0.85rem; color:var(--sp-text-muted); margin-bottom:10px;">Part No: <?= htmlspecialchars($item['part_number'] ?: 'N/A') ?></p>
                            <div style="color:var(--sp-blue); font-weight:800;">₹<?= number_format($item['price'], 2) ?> x <?= $item['qty'] ?></div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:1.2rem; font-weight:800; margin-bottom:10px;">₹<?= number_format($item['subtotal'], 2) ?></div>
                            <form method="POST" action="cart.php">
                                <button type="submit" name="remove_item" value="<?= $item['id'] ?>" class="sp-btn sp-btn--outline" style="padding:6px 12px; font-size:0.75rem; border-color:#d32f2f; color:#d32f2f;"><i class="fas fa-trash"></i> Remove</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <form method="POST" action="cart.php" style="margin-top:20px;">
                        <button type="submit" name="clear_cart" class="sp-btn sp-btn--outline" style="font-size:0.8rem;"><i class="fas fa-times"></i> Clear Cart</button>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="sp-glass-card" style="flex:1; min-width:280px; padding:30px;">
                    <h3 style="margin-bottom:20px; border-bottom:1px solid #333; padding-bottom:10px;">Order Summary</h3>
                    
                    <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--sp-text-muted);">
                        <span>Subtotal (<?= array_sum($_SESSION['cart']) ?> items)</span>
                        <span>₹<?= number_format($totalPrice, 2) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--sp-text-muted);">
                        <span>Tax (Estimated)</span>
                        <span>₹<?= number_format($totalPrice * 0.08, 2) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--sp-text-muted);">
                        <span>Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                    
                    <div style="border-top:1px solid #333; margin:20px 0;"></div>
                    
                    <div style="display:flex; justify-content:space-between; margin-bottom:30px; font-size:1.3rem; font-weight:800;">
                        <span>Total</span>
                        <span style="color:var(--sp-blue);">₹<?= number_format($totalPrice * 1.08, 2) ?></span>
                    </div>
                    <a href="checkout.php" class="sp-btn sp-btn--primary" style="display:block; width:100%; text-align:center; padding:15px; text-decoration:none;"><i class="fas fa-lock"></i> Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include 'partials/footer.php'; ?>
</body>
</html>
