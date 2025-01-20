<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['id'])) {
    echo "Please log in to send messages.";
    exit();
}

$buyerId = $_SESSION['id']; // Buyer sending the message
$sellerId = intval($_POST['receiver_id']); // Seller ID from the form
$message = mysqli_real_escape_string($conn, $_POST['message']);
$senderId = $buyerId; // Sender is the buyer in this case

// Insert the message into the messages table
$sql = "INSERT INTO messages (buyer_id, seller_id, sender_id, message, timestamp, is_read) 
        VALUES ($buyerId, $sellerId, $senderId, '$message', NOW(), 0)";

if (mysqli_query($conn, $sql)) {
    echo "Message sent successfully.";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
