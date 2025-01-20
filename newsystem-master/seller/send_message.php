<?php
session_start();
require '../dbconnect.php';  // Adjust path to your dbconnect.php

// Get data from the AJAX request
$message = mysqli_real_escape_string($conn, $_POST['message']);
$buyer_id = (int) $_POST['buyer_id'];
$seller_id = (int) $_POST['seller_id'];
$sender_id = $_SESSION['id']; // ID of the current user (seller in this case)

// Insert message into the database
$query = "INSERT INTO messages (buyer_id, seller_id, sender_id, message) 
          VALUES ('$buyer_id', '$seller_id', '$sender_id', '$message')";
mysqli_query($conn, $query);

// Confirm the message was sent
if (mysqli_affected_rows($conn) > 0) {
    echo "<div class='message seller-message'>".htmlspecialchars($message)."<br><small>".date('H:i:s')."</small></div>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
