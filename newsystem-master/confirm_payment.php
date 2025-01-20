<?php
session_start();
require 'dbconnect.php';

// Ensure user is logged in and required fields are set
if (!isset($_SESSION['id']) || !isset($_POST['idbook']) || !isset($_POST['selected_address'])) {
    echo "Invalid access. Please try again from the payment page.";
    exit();
}

$buyerid = $_SESSION['id'];
$idbook = $_POST['idbook'];
$selectedAddressId = intval($_POST['selected_address']);
$paymentType = $_POST['payment_type'] ?? null;

try {
    $conn->begin_transaction();

    // 1. Fetch book details and validate address
    $sqlBook = "SELECT orderbook.bookname, orderbook.price, user.username AS seller_name 
                FROM orderbook 
                JOIN user ON orderbook.ownerid = user.id 
                WHERE orderbook.idbook = ?";
    $stmtBook = $conn->prepare($sqlBook);
    $stmtBook->bind_param("i", $idbook);

    if (!$stmtBook->execute()) {
        throw new Exception("Error fetching book details: " . $stmtBook->error);
    }

    $resultBook = $stmtBook->get_result();
    $book = $resultBook->fetch_assoc();

    if (!$book) {
        throw new Exception("Invalid book selected.");
    }

    $bookTitle = $book['bookname'];
    $bookPrice = $book['price'];
    $sellerName = $book['seller_name'];
    $shippingFee = 4.90; // Fixed shipping fee
    $totalPrice = $bookPrice + $shippingFee;

    $sqlAddress = "SELECT * FROM shipping_address WHERE id = ? AND user_id = ?";
    $stmtAddress = $conn->prepare($sqlAddress);
    $stmtAddress->bind_param("ii", $selectedAddressId, $buyerid);

    if (!$stmtAddress->execute()) {
        throw new Exception("Error fetching the selected address: " . $stmtAddress->error);
    }

    $resultAddress = $stmtAddress->get_result();
    $address = $resultAddress->fetch_assoc();

    if (!$address) {
        throw new Exception("Invalid address selected.");
    }

    $shippingAddress = $address['address'] . ', ' . $address['city'] . ', ' . $address['state'] . ' - ' . $address['postal_code'];
    $recipientName = $address['recipient_name'];
    $phoneNumber = $address['phone_number'];

    // 2. Handle receipt upload if applicable
    $receiptField = ($paymentType === 'e_wallet') ? 'receipt_ewallet' : 'receipt_qrcode';
    $uploadDir = 'uploads/receipts/';
    $uploadFile = isset($_FILES[$receiptField]) ? $uploadDir . uniqid('receipt_', true) . '.' . pathinfo($_FILES[$receiptField]['name'], PATHINFO_EXTENSION) : null;

    if ($uploadFile) {
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $allowedTypes = ['image/jpeg', 'image/png'];
        $fileType = mime_content_type($_FILES[$receiptField]['tmp_name']);
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Invalid file type. Only JPG and PNG are allowed.");
        }
        if ($_FILES[$receiptField]['size'] > $maxSize) {
            throw new Exception("File size exceeds the 2MB limit.");
        }

        if (!move_uploaded_file($_FILES[$receiptField]['tmp_name'], $uploadFile)) {
            throw new Exception("Error uploading receipt. Please try again.");
        }

        $stmtReceipt = $conn->prepare("INSERT INTO payment_receipts (idbook, buyerid, payment_type, receipt_path, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
        $stmtReceipt->bind_param("iiss", $idbook, $buyerid, $paymentType, $uploadFile);
        if (!$stmtReceipt->execute()) {
            throw new Exception("Error saving receipt information: " . $stmtReceipt->error);
        }
    }

    // 3. Update order status and save selected address ID
    $stmtUpdate = $conn->prepare("UPDATE orderbook 
                                  SET payment_status = 'paid', 
                                      is_purchased = 1, 
                                      selected_address_id = ? 
                                  WHERE idbook = ? AND buyerid = ?");
    $stmtUpdate->bind_param("iii", $selectedAddressId, $idbook, $buyerid);
    if (!$stmtUpdate->execute()) {
        throw new Exception("Error updating order status: " . $stmtUpdate->error);
    }

    $conn->commit();
    $message = "Payment successful! Your purchase has been confirmed.";
} catch (Exception $e) {
    $conn->rollback();
    $message = "Transaction failed: " . $e->getMessage();
    error_log("Payment Error: " . $e->getMessage(), 3, "/var/log/easybook_errors.log");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Payment Confirmation - Easy Book</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
        padding-top: 70px;
    }
    .container {
        max-width: 700px;
        margin: auto;
        padding: 30px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .btn-primary {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        color: white;
        text-decoration: none;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
    .section-box {
        margin-top: 20px;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #eaeaea;
    }
    .section-box h5 {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 1rem;
    }
    .summary-item strong {
        font-weight: bold;
    }
    .divider {
        border-top: 1px solid #eaeaea;
        margin: 20px 0;
    }
  </style>
</head>
<body>
<div class="container">
    <h2 class="text-center text-primary mb-4">Payment Confirmation</h2>

    <!-- Payment Status -->
    <div class="alert alert-success text-center" role="alert">
        <?= htmlspecialchars($message) ?>
    </div>

    <!-- Order Details -->
    <div class="section-box">
        <h5>Order Details</h5>
        <div class="summary-item">
            <span>Book Title:</span>
            <strong><?= htmlspecialchars($bookTitle) ?></strong>
        </div>
        <div class="summary-item">
            <span>Seller Name:</span>
            <strong><?= htmlspecialchars($sellerName) ?></strong>
        </div>
        <div class="summary-item">
            <span>Book Price:</span>
            <strong>RM <?= number_format($bookPrice, 2) ?></strong>
        </div>
        <div class="summary-item">
            <span>Shipping Fee:</span>
            <strong>RM <?= number_format($shippingFee, 2) ?></strong>
        </div>
        <div class="divider"></div>
        <div class="summary-item">
            <span>Total Price:</span>
            <strong class="text-dark">RM <?= number_format($totalPrice, 2) ?></strong>

        </div>
    </div>

    <!-- Shipping Address -->
    <div class="section-box">
        <h5>Shipping Address</h5>
        <p><strong>Recipient:</strong> <?= htmlspecialchars($recipientName) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($shippingAddress) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($phoneNumber) ?></p>
    </div>

    <!-- Redirect Button -->
    <div class="text-center">
        <a href="my_purchase.php" class="btn btn-primary">Go to My Purchases</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

