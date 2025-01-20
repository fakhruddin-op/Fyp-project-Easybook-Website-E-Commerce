<?php
session_start();
require 'dbconnect.php';

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from the form
    $userId = $_SESSION['id'];
    $addressId = $_POST['address_id'];
    $paymentMethod = $_POST['payment_method'];
    $totalPrice = floatval($_POST['total_price']);
    $selectedBooks = $_POST['selected_books'];
    $quantities = $_POST['quantities'];

    // Validate address
    if ($addressId === 'new') {
        $recipientName = mysqli_real_escape_string($conn, $_POST['new_recipient_name']);
        $address = mysqli_real_escape_string($conn, $_POST['new_address']);
        $city = mysqli_real_escape_string($conn, $_POST['new_city']);
        $state = mysqli_real_escape_string($conn, $_POST['new_state']);
        $postalCode = mysqli_real_escape_string($conn, $_POST['new_postal_code']);
        $phoneNumber = mysqli_real_escape_string($conn, $_POST['new_phone_number']);

        // Save the new address
        $insertAddressQuery = "INSERT INTO shipping_address (user_id, recipient_name, address, city, state, postal_code, phone_number)
                               VALUES ('$userId', '$recipientName', '$address', '$city', '$state', '$postalCode', '$phoneNumber')";
        if (mysqli_query($conn, $insertAddressQuery)) {
            $addressId = mysqli_insert_id($conn);
        } else {
            die("Error saving new address: " . mysqli_error($conn));
        }
    }

    // Process payment based on the method
    if ($paymentMethod === 'card') {
        $cardNumber = $_POST['card_number'];
        $cardName = $_POST['card_name'];
        $cardExpiry = $_POST['card_expiry'];
        $cardCVV = $_POST['card_cvv'];

        // Simulate card payment validation
        if (empty($cardNumber) || empty($cardName) || empty($cardExpiry) || empty($cardCVV)) {
            die("Card payment details are incomplete. <a href='process_payment1.php'>Go back</a>");
        }

        // Add payment record (example only; integrate a real payment gateway here)
        $paymentStatus = 'Success'; // Simulated payment status
    } elseif ($paymentMethod === 'fpx') {
        $selectedBank = $_POST['fpx_bank'];

        if (empty($selectedBank)) {
            die("Please select a bank for FPX payment. <a href='process_payment1.php'>Go back</a>");
        }

        // Redirect to bank login page (mock)
        header("Location: bank_login_mock.php?bank=$selectedBank&amount=$totalPrice");
        exit;
    } elseif ($paymentMethod === 'qr') {
        // QR payment is assumed to be completed by scanning the QR code
        $paymentStatus = 'Pending'; // Simulated payment status for QR
    } else {
        die("Invalid payment method selected. <a href='process_payment1.php'>Go back</a>");
    }

    // Save the order details
    $orderQuery = "INSERT INTO orders (user_id, address_id, total_price, payment_method, payment_status, created_at)
                   VALUES ('$userId', '$addressId', '$totalPrice', '$paymentMethod', '$paymentStatus', NOW())";

    if (mysqli_query($conn, $orderQuery)) {
        $orderId = mysqli_insert_id($conn);

        // Save the books in the order
        foreach ($selectedBooks as $bookId) {
            $quantity = intval($quantities[$bookId]);
            $orderItemQuery = "INSERT INTO order_items (order_id, book_id, quantity)
                               VALUES ('$orderId', '$bookId', '$quantity')";
            mysqli_query($conn, $orderItemQuery);

            // Update the book as purchased
            $updateBookQuery = "UPDATE orderbook SET is_purchased = 1 WHERE idbook = '$bookId'";
            mysqli_query($conn, $updateBookQuery);
        }

        // Redirect to the order confirmation page
        header("Location: order_confirmation.php?order_id=$orderId");
        exit;
    } else {
        die("Error creating order: " . mysqli_error($conn));
    }
} else {
    die("Invalid request method. <a href='index.php'>Go back</a>");
}
?>
