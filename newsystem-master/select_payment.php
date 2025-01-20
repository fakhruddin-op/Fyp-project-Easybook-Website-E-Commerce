<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my_cart.php");
    exit;
}

$buyer_id = $_POST['buyer_id'];
$total_price = $_POST['total_price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Select Payment Method</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .payment-container { margin-top: 70px; text-align: center; }
        .payment-method { margin: 20px; }
        .btn-payment { padding: 1rem 2rem; font-size: 1rem; border: none; border-radius: 5px; cursor: pointer; transition: all 0.3s ease; }
        .btn-payment:hover { transform: scale(1.05); }
        .btn-card { background-color: #007bff; color: white; }
        .btn-card:hover { background-color: #0056b3; }
        .btn-fpx { background-color: #28a745; color: white; }
        .btn-fpx:hover { background-color: #218838; }
        .btn-qr { background-color: #ffc107; color: black; }
        .btn-qr:hover { background-color: #e0a800; }
    </style>
</head>
<body>
<div class="container payment-container">
    <h2>Select Payment Method</h2>
    <p>Total Amount: RM <?= number_format($total_price, 2) ?></p>
    <div class="payment-method">
        <form action="payment_details.php" method="POST">
            <input type="hidden" name="buyer_id" value="<?= $buyer_id ?>">
            <input type="hidden" name="total_price" value="<?= $total_price ?>">
            <input type="hidden" name="payment_method" value="card">
            <button type="submit" class="btn-payment btn-card">Pay with Card</button>
        </form>
    </div>
    <div class="payment-method">
        <form action="payment_details.php" method="POST">
            <input type="hidden" name="buyer_id" value="<?= $buyer_id ?>">
            <input type="hidden" name="total_price" value="<?= $total_price ?>">
            <input type="hidden" name="payment_method" value="fpx">
            <button type="submit" class="btn-payment btn-fpx">Pay with FPX</button>
        </form>
    </div>
    <div class="payment-method">
        <form action="payment_details.php" method="POST">
            <input type="hidden" name="buyer_id" value="<?= $buyer_id ?>">
            <input type="hidden" name="total_price" value="<?= $total_price ?>">
            <input type="hidden" name="payment_method" value="qr">
            <button type="submit" class="btn-payment btn-qr">Pay with QR Code</button>
        </form>
    </div>
</div>
</body>
</html>
