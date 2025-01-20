<?php
$orderId = $_GET['order_id'];
// Render card payment form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Card Payment</title>
</head>
<body>
    <h1>Pay with Card</h1>
    <form action="process_card_payment.php" method="POST">
        <input type="hidden" name="order_id" value="<?= $orderId ?>">
        <input type="text" name="card_number" placeholder="Card Number" required>
        <input type="text" name="card_name" placeholder="Cardholder Name" required>
        <input type="text" name="card_expiry" placeholder="Expiry Date (MM/YY)" required>
        <input type="text" name="card_cvv" placeholder="CVV" required>
        <button type="submit">Submit Payment</button>
    </form>
</body>
</html>
