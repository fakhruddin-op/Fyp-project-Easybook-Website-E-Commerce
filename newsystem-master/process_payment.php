<?php
session_start();
require 'dbconnect.php';

// Retrieve buyer ID and total price from POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyer_id = mysqli_real_escape_string($conn, $_POST['buyer_id']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);

    // Debug: Print payment details
    error_log("Processing payment for Buyer ID: $buyer_id, Total Price: $total_price");

    // Fetch cart items for the buyer
    $sql = "SELECT * FROM orderbook WHERE buyerid = '$buyer_id' AND is_purchased = 0";
    $result = mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {
        error_log("SQL Error: " . mysqli_error($conn));
        echo "Error processing payment. Please try again later.";
        exit;
    }

    // Simulate payment success (Replace this with your payment gateway integration)
    $payment_status = true; // Assume the payment is successful

    if ($payment_status) {
        // Update the cart items as purchased
        $update_sql = "UPDATE orderbook SET is_purchased = 1 WHERE buyerid = '$buyer_id' AND is_purchased = 0";
        if (mysqli_query($conn, $update_sql)) {
            // Debug: Payment success
            error_log("Payment successful for Buyer ID: $buyer_id. Cart items marked as purchased.");

            // Redirect to a success page
            header("Location: payment_success.php");
            exit;
        } else {
            error_log("Error updating purchase status: " . mysqli_error($conn));
            echo "Error processing payment. Please try again later.";
        }
    } else {
        // Debug: Payment failed
        error_log("Payment failed for Buyer ID: $buyer_id");
        echo "Payment failed. Please try again.";
    }
} else {
    // Debug: Invalid access
    error_log("Invalid access to process_payment.php");
    echo "Invalid request.";
}
?>
