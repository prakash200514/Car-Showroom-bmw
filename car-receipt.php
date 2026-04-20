<?php
session_start();

if (!isset($_SESSION['last_car_booking'])) {
    header("Location: cars.php");
    exit;
}

$booking = $_SESSION['last_car_booking'];
// Clear the booking session
unset($_SESSION['last_car_booking']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Receipt - <?= htmlspecialchars($booking['booking_id']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background: #e0e0e0; margin: 0; padding: 40px; color: #111; }
        .receipt-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 50px; box-shadow: 0 15px 30px rgba(0,0,0,0.2); border-top: 10px solid #28a745; }
        
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-box h1 { margin: 0; font-size: 2rem; font-weight: 900; letter-spacing: -1px; text-transform: uppercase; }
        .logo-box p { margin: 5px 0 0 0; color: #666; font-size: 0.9rem; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { margin: 0; font-size: 2rem; color: #28a745; font-weight: 900; text-transform: uppercase; }
        .invoice-title p { margin: 5px 0 0 0; font-weight: bold; }

        .success-banner { background: rgba(40, 167, 69, 0.1); padding: 15px; border-radius: 4px; text-align: center; color: #28a745; font-weight: bold; margin-bottom: 30px; border: 1px solid rgba(40, 167, 69, 0.3); }

        .billing-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .infobox h4 { margin: 0 0 10px 0; color: #666; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }
        .infobox p { margin: 0; line-height: 1.5; color: #222; font-size: 1.1rem; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; background: #f9f9f9; padding: 15px; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; color: #333; }
        td { padding: 15px; border-bottom: 1px solid #eee; font-size: 1.1rem; }
        .text-right { text-align: right; }
        
        .totals { width: 50%; float: right; padding-top: 20px; }
        .totals-row { display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;}
        .totals-row.grand { font-size: 1.5rem; font-weight: 900; color: #28a745; border-bottom: none; border-top: 2px solid #333; padding-top: 15px; }

        .clearfix::after { content: ""; clear: both; display: table; }

        .footer { clear: both; margin-top: 60px; text-align: center; color: #888; font-size: 0.9rem; border-top: 1px solid #eee; padding-top: 20px; }

        .actions { text-align: center; margin-bottom: 30px; }
        .btn { display: inline-block; background: #222; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold; cursor: pointer; border: none; font-size: 1rem; transition: background 0.3s;}
        .btn:hover { background: #000; }
        .btn-outline { background: transparent; border: 2px solid #222; color: #222; margin-left:10px; }
        .btn-outline:hover { background: #eee; }

        @media print {
            body { background: #fff; padding: 0; }
            .receipt-container { box-shadow: none; padding: 0; border: none; }
            .actions { display: none; }
        }
    </style>
</head>
<body>

    <div class="actions">
        <button class="btn" onclick="window.print()"><i class="fas fa-print"></i> Print Receipt</button>
        <a href="customer/dashboard.php" class="btn btn-outline"><i class="fas fa-home"></i> Go to Dashboard</a>
    </div>

    <div class="receipt-container">
        
        <div class="header">
            <div class="logo-box">
                <h1>BMW Showroom</h1>
                <p>Automotive Excellence</p>
                <p>Munich, Germany 80331</p>
            </div>
            <div class="invoice-title">
                <h2>BOOKING RECEIPT</h2>
                <p>Booking #: <?= htmlspecialchars($booking['booking_id']) ?></p>
                <p style="color:#666; font-size:0.9rem;">Date: <?= htmlspecialchars($booking['date']) ?></p>
            </div>
        </div>

        <div class="success-banner">
            <i class="fas fa-check-circle" style="font-size: 1.2rem; margin-right: 5px;"></i> Payment Successful! Your booking is confirmed.
        </div>

        <div class="billing-grid">
            <div class="infobox">
                <h4>Customer Details</h4>
                <p><strong><?= htmlspecialchars($booking['customer_name']) ?></strong></p>
            </div>
            <div class="infobox" style="text-align: right;">
                <h4>Booking Status</h4>
                <p><span style="background: #28a745; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 0.9rem; font-weight: bold;"><?= htmlspecialchars($booking['status']) ?></span></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Car Details</th>
                    <th class="text-right">Total Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($booking['car_name']) ?></strong><br>
                        <span style="color:#666; font-size: 0.9rem;"><?= htmlspecialchars($booking['car_brand']) ?> Collection</span>
                    </td>
                    <td class="text-right">₹<?= number_format($booking['car_price'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="clearfix">
            <div class="totals">
                <div class="totals-row">
                    <span style="color: #666;">Car Price</span>
                    <span>₹<?= number_format($booking['car_price'], 2) ?></span>
                </div>
                <div class="totals-row">
                    <span style="color: #666;">Handling Fees</span>
                    <span>₹0.00</span>
                </div>
                <div class="totals-row grand">
                    <span>Deposit Paid</span>
                    <span>₹<?= number_format($booking['booking_amount'], 2) ?></span>
                </div>
                <div style="text-align: right; margin-top: 10px; color: #666; font-size: 0.9rem;">
                    Balance due at delivery: <strong>₹<?= number_format($booking['car_price'] - $booking['booking_amount'], 2) ?></strong>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing BMW Showroom. A representative will contact you shortly to finalize delivery details. For inquiries, contact sales@bmwshowroom.com.</p>
        </div>

    </div>

</body>
</html>
