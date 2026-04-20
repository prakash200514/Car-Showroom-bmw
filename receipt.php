<?php
session_start();

if (!isset($_SESSION['last_order'])) {
    header("Location: bmw-spares.php");
    exit;
}

$order = $_SESSION['last_order'];
// Clean out the cart as order is complete
$_SESSION['cart'] = [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= $order['order_id'] ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #e0e0e0; margin: 0; padding: 40px; color: #111; }
        .receipt-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 50px; box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #1c6bba; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-box h1 { margin: 0; font-size: 2rem; font-weight: 900; letter-spacing: -1px; text-transform: uppercase; }
        .logo-box p { margin: 5px 0 0 0; color: #666; font-size: 0.9rem; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { margin: 0; font-size: 2.5rem; color: #1c6bba; font-weight: 900; text-transform: uppercase; }
        .invoice-title p { margin: 5px 0 0 0; font-weight: bold; }

        .billing-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .infobox h4 { margin: 0 0 10px 0; color: #1c6bba; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        .infobox p { margin: 0; line-height: 1.5; color: #444; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; background: #f4f4f4; padding: 15px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; color: #333; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        
        .totals { width: 50%; float: right; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;}
        .totals-row.grand { font-size: 1.5rem; font-weight: 900; color: #1c6bba; border-bottom: none; border-top: 2px solid #333; padding-top: 15px; }

        .clearfix::after { content: ""; clear: both; display: table; }

        .footer { clear: both; margin-top: 60px; text-align: center; color: #888; font-size: 0.9rem; border-top: 1px solid #eee; padding-top: 20px; }

        .actions { text-align: center; margin-bottom: 30px; }
        .btn { display: inline-block; background: #1c6bba; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold; cursor: pointer; border: none; font-size: 1rem;}
        .btn:hover { background: #155598; }
        .btn-outline { background: transparent; border: 2px solid #111; color: #111; margin-left:10px; }

        @media print {
            body { background: #fff; padding: 0; }
            .receipt-container { box-shadow: none; padding: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

    <div class="actions">
        <button class="btn" onclick="window.print()"><i class="fas fa-print"></i> Print / Download PDF</button>
        <a href="bmw-spares.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Store</a>
    </div>

    <div class="receipt-container">
        
        <div class="header">
            <div class="logo-box">
                <h1>BMW Showroom</h1>
                <p>Genuine Parts & Accessories</p>
                <p>Munich, Germany 80331</p>
            </div>
            <div class="invoice-title">
                <h2>RECEIPT</h2>
                <p>Order #: <?= htmlspecialchars($order['order_id']) ?></p>
                <p style="color:#666; font-size:0.9rem;">Date: <?= htmlspecialchars($order['date']) ?></p>
            </div>
        </div>

        <div class="billing-grid">
            <div class="infobox">
                <h4>Billed To</h4>
                <p><strong><?= htmlspecialchars($order['billing']['name']) ?></strong></p>
                <p><?= htmlspecialchars($order['billing']['email']) ?></p>
            </div>
            <div class="infobox">
                <h4>Shipped To</h4>
                <p><?= htmlspecialchars($order['billing']['address']) ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Part No.</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                    <td style="color:#666; font-family:monospace;"><?= htmlspecialchars($item['part_number'] ?: 'N/A') ?></td>
                    <td class="text-right"><?= $item['qty'] ?></td>
                    <td class="text-right">₹<?= number_format($item['price'], 2) ?></td>
                    <td class="text-right">₹<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="clearfix">
            <div class="totals">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <span>$<?= number_format($order['subtotal'], 2) ?></span>
                </div>
                <div class="totals-row">
                    <span>Tax (8%)</span>
                    <span>$<?= number_format($order['tax'], 2) ?></span>
                </div>
                <div class="totals-row">
                    <span>Shipping</span>
                    <span>$0.00</span>
                </div>
                <div class="totals-row grand">
                    <span>Total Paid</span>
                    <span>$<?= number_format($order['total'], 2) ?></span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for shopping genuine BMW accessories. If you have any questions concerning this invoice, contact support@bmwshowroom.com.</p>
        </div>

    </div>

</body>
</html>
