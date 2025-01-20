<?php
session_start();
require 'dbconnect.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    die("Error: You need to log in to send messages.");
}

// Validate POST data
if (!isset($_POST['seller_id']) || empty($_POST['seller_id'])) {
    die("Error: Seller ID is missing.");
}
if (!isset($_POST['message']) || empty(trim($_POST['message']))) {
    die("Error: Message cannot be empty.");
}

// Sanitize inputs
$buyerId = $_SESSION['id']; // The logged-in user's ID
$sellerId = intval($_POST['seller_id']); // The recipient's ID
$message = mysqli_real_escape_string($conn, trim($_POST['message']));

// Check if the seller exists in the database
$checkSeller = "SELECT id FROM user WHERE id = $sellerId";
$result = mysqli_query($conn, $checkSeller);

if (mysqli_num_rows($result) == 0) {
    die("Error: Invalid Seller ID.");
}

// Insert the message into the database
$query = "INSERT INTO messages (buyer_id, seller_id, sender_id, message, timestamp, is_read)
          VALUES ($buyerId, $sellerId, $buyerId, '$message', NOW(), 0)";

if (mysqli_query($conn, $query)) {
    echo "Message sent successfully.";
} else {
    error_log("MySQL Error: " . mysqli_error($conn)); // Log the error for debugging
    die("Error: Unable to send message. Please try again.");
}
?>
