<?php
session_start();
include 'config/database.php';
include 'includes/functions.php';

if (empty($_SESSION['cart'])) {
    header("Location: bmw-spares.php");
    exit;
}

$cartItems = [];
$totalPrice = 0;

$ids = implode(',', array_keys($_SESSION['cart']));
$stmt = $pdo->query("SELECT * FROM spares WHERE id IN ($ids)");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $qty = $_SESSION['cart'][$row['id']];
    $row['qty'] = $qty;
    $row['subtotal'] = $row['price'] * $qty;
    $totalPrice += $row['subtotal'];
    $cartItems[] = $row;
}

$tax = $totalPrice * 0.08;
$grandTotal = $totalPrice + $tax;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate an order ID
    $order_id = 'BMW-' . date('Ymd') . '-' . rand(1000, 9999);
    
    // In a real app we'd insert to DB here. For now, pass to session for receipt.
    $_SESSION['last_order'] = [
        'order_id' => $order_id,
        'date' => date('Y-m-d H:i:s'),
        'items' => $cartItems,
        'subtotal' => $totalPrice,
        'tax' => $tax,
        'total' => $grandTotal,
        'billing' => [
            'name' => $_POST['fullname'],
            'email' => $_POST['email'],
            'address' => $_POST['address'] . ', ' . $_POST['city'] . ' ' . $_POST['zipcode']
        ]
    ];
    
    header("Location: receipt.php");
    exit;
}

include 'partials/header.php';
?>

<link rel="stylesheet" href="bmw-spares.css">
<style>
.checkout-input { width:100%; padding:14px; background:#1a1a1a; border:1px solid #333; color:#fff; border-radius:4px; margin-bottom:15px; }
.checkout-input:focus { border-color:var(--sp-blue); outline:none; }
</style>

<body class="spares-page">
    <div style="height:80px; background:#111;"></div>

    <div class="sp-container" style="padding: 60px 5%; min-height:60vh;">
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:40px;">
            <div>
                <h2 style="font-weight:900; font-size:2.5rem; margin-bottom:5px;">Checkout</h2>
                <p style="color:var(--sp-text-muted);">Secure Payment & Shipping</p>
            </div>
            <div><i class="fas fa-lock" style="color:var(--sp-blue); font-size:1.5rem;"></i></div>
        </div>

        <form method="POST" action="checkout.php" style="display:flex; gap:40px; flex-wrap:wrap; align-items:flex-start;">
            
            <!-- Shipping Form -->
            <div style="flex:2; min-width:300px;">
                <div class="sp-glass-card" style="padding:40px;">
                    <h3 style="margin-bottom:25px; border-bottom:1px solid #333; padding-bottom:10px;">Contact Information</h3>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                        <div>
                            <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.9rem;">Full Name *</label>
                            <input type="text" name="fullname" class="checkout-input" required placeholder="John Doe">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.9rem;">Email Address *</label>
                            <input type="email" name="email" class="checkout-input" required placeholder="john@example.com">
                        </div>
                    </div>

                    <h3 style="margin-top:30px; margin-bottom:25px; border-bottom:1px solid #333; padding-bottom:10px;">Shipping Address</h3>
                    
                    <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.9rem;">Street Address *</label>
                    <input type="text" name="address" class="checkout-input" required placeholder="123 BMW Avenue">
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                        <div>
                            <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.9rem;">City *</label>
                            <input type="text" name="city" class="checkout-input" required placeholder="Munich">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.9rem;">ZIP / Postal Code *</label>
                            <input type="text" name="zipcode" class="checkout-input" required placeholder="80331">
                        </div>
                    </div>

                    <h3 style="margin-top:30px; margin-bottom:25px; border-bottom:1px solid #333; padding-bottom:10px;">Payment Information</h3>
                    <div style="padding:20px; border:1px solid #333; border-radius:8px; margin-bottom:15px; position:relative; overflow:hidden;">
                        <i class="fab fa-cc-visa" style="font-size:2rem; position:absolute; top:20px; right:20px; color:rgba(255,255,255,0.2);"></i>
                        <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.9rem;">Card Number *</label>
                        <input type="text" class="checkout-input" required placeholder="XXXX XXXX XXXX XXXX" maxlength="19">
                        
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                            <div>
                                <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.9rem;">Expiry *</label>
                                <input type="text" class="checkout-input" required placeholder="MM/YY" maxlength="5">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.9rem;">CVC *</label>
                                <input type="password" class="checkout-input" required placeholder="***" maxlength="4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="sp-glass-card" style="flex:1; min-width:280px; padding:30px;">
                <h3 style="margin-bottom:20px; border-bottom:1px solid #333; padding-bottom:10px;">Order Summary</h3>
                
                <div style="margin-bottom:25px;">
                    <?php foreach ($cartItems as $item): ?>
                    <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.9rem;">
                        <span style="color:#ddd;"><?= $item['qty'] ?>x <?= htmlspecialchars($item['name']) ?></span>
                        <span style="font-weight:bold;">₹<?= number_format($item['subtotal'], 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="border-top:1px solid #333; margin:20px 0;"></div>

                <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--sp-text-muted);">
                    <span>Subtotal</span>
                    <span>$<?= number_format($totalPrice, 2) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--sp-text-muted);">
                    <span>Tax</span>
                    <span>$<?= number_format($tax, 2) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:15px; color:var(--sp-text-muted);">
                    <span>Shipping</span>
                    <span style="color:green;">Free</span>
                </div>
                
                <div style="border-top:1px solid #333; margin:20px 0;"></div>
                
                <div style="display:flex; justify-content:space-between; margin-bottom:30px; font-size:1.3rem; font-weight:800;">
                    <span>Total</span>
                    <span style="color:var(--sp-blue);">$<?= number_format($grandTotal, 2) ?></span>
                </div>
                
                <button type="submit" class="sp-btn sp-btn--primary" style="width:100%; text-align:center; padding:15px; font-size:1.1rem;"><i class="fas fa-check-circle"></i> Place Order</button>
                <div style="text-align:center; margin-top:20px; font-size:0.8rem; color:#666;">
                    <i class="fas fa-shield-alt"></i> 256-bit Secure Encryption
                </div>
            </div>
        </form>
    </div>

    <?php include 'partials/footer.php'; ?>
</body>
</html>
