<?php
session_start();
require '../dbconnect.php';

$seller_id = $_SESSION['id'];
$response = [];

// Query to get unread messages and the buyer’s name
$sql = "SELECT messages.id AS message_id, messages.buyer_id, messages.message, user.username 
        FROM messages 
        JOIN user ON messages.buyer_id = user.id 
        WHERE messages.seller_id = $seller_id AND messages.is_read = 0 
        ORDER BY messages.timestamp DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = [
            'message_id' => $row['message_id'],
            'buyer_id' => $row['buyer_id'],
            'message' => $row['message'],
            'username' => $row['username']
        ];
    }
}

// Mark messages as read
$update_query = "UPDATE messages SET is_read = 1 WHERE seller_id = $seller_id AND is_read = 0";
mysqli_query($conn, $update_query);

echo json_encode($response);
?>
