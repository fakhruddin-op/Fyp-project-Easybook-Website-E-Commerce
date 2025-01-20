<?php
session_start();
require 'dbconnect.php';

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    echo "Invalid access. Please log in.";
    exit();
}

$buyerId = $_SESSION['id'];
$paymentType = $_POST['payment_type'] ?? null;
$idbook = $_POST['idbook'] ?? null;

// Validate inputs
if (!$idbook || !$paymentType) {
    echo "Invalid payment details. Please try again.";
    exit();
}

try {
    $conn->begin_transaction();

    // Validate book ID
    $bookSql = "SELECT * FROM orderbook WHERE idbook = ? AND buyerid = ?";
    $stmt = $conn->prepare($bookSql);
    $stmt->bind_param("ii", $idbook, $buyerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if (!$book) {
        throw new Exception("Invalid book ID or unauthorized access.");
    }

    // Handle receipt upload
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

        // Save receipt details in the database
        $stmt = $conn->prepare("INSERT INTO payment_receipts (idbook, buyerid, payment_type, receipt_path, uploaded_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiss", $idbook, $buyerId, $paymentType, $uploadFile);
        if (!$stmt->execute()) {
            throw new Exception("Error saving receipt information: " . $stmt->error);
        }
    } else {
        throw new Exception("No receipt file uploaded.");
    }

    // Update order status
    $updateOrderSql = "UPDATE orderbook SET payment_status = 'paid', is_purchased = 1 WHERE idbook = ? AND buyerid = ?";
    $stmt = $conn->prepare($updateOrderSql);
    $stmt->bind_param("ii", $idbook, $buyerId);
    if (!$stmt->execute()) {
        throw new Exception("Error updating order status: " . $stmt->error);
    }

    $conn->commit();
    echo "Payment successful. Receipt uploaded.";
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
