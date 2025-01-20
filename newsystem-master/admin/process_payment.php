<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable MySQL error reporting for debugging

// Check if the user is an admin
if (!isset($_SESSION['accesslevel']) || $_SESSION['accesslevel'] != 'admin') {
    header('location: ../login.php');
    exit();
}

include "../dbconnect.php";

// Check if database connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seller_id = $_POST['seller_id'];       // Seller's ID from the form
    $total_sales = $_POST['total_sales'];   // Total amount to be paid to the seller from the form

    // Debug: Check if POST data is received correctly
    if (empty($seller_id) || empty($total_sales)) {
        die("Error: seller_id or total_sales is missing.");
    }

    // Calculate 5% commission
    $commission = $total_sales * 0.05;  // Calculate 5% of the total sales
    $final_payment = $total_sales - $commission; // Calculate payment after deducting commission

    // Debug: Display the calculated amounts
    echo "Processing payment for seller_id: $seller_id<br>";
    echo "Total Sales: RM " . number_format($total_sales, 2) . "<br>";
    echo "Commission (5%): RM " . number_format($commission, 2) . "<br>";
    echo "Final Payment: RM " . number_format($final_payment, 2) . "<br>";

    // Ensure there are unpaid sales
    if ($final_payment > 0) {
        // Insert a payment record into the `payments` table
        $stmt = $conn->prepare("INSERT INTO payments (seller_id, total_amount, commission) VALUES (?, ?, ?)");
        $stmt->bind_param("idd", $seller_id, $final_payment, $commission);
        $stmt->execute();
        $stmt->close();
        echo "Payment record inserted successfully.<br>";

        // Mark orders as paid in the `orderbook` table for this seller
        $update_stmt = $conn->prepare("UPDATE orderbook SET is_paid = 1 WHERE ownerid = ? AND is_purchased = 1 AND is_paid = 0");
        $update_stmt->bind_param("i", $seller_id);
        $update_stmt->execute();
        $update_stmt->close();
        echo "Orders marked as paid successfully.<br>";

        // Set a success message and redirect to the admin dashboard
        $_SESSION['message'] = "Payment of RM " . number_format($final_payment, 2) . " (after 5% commission) has been made to seller ID: $seller_id.";
    } else {
        // If there are no unpaid sales, set an error message
        $_SESSION['message'] = "No unpaid sales for this seller.";
    }
} else {
    die("Error: Invalid request method.");
}

// Redirect back to the admin dashboard
header('Location: admindashboard.php');
exit();
