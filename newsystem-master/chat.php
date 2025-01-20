<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['id'])) {
    echo "Please log in to view the chat.";
    exit();
}

$buyerId = $_SESSION['id']; // Buyer's ID from the session
$sellerId = intval($_GET['user_id']); // Seller's ID from the GET request

// Fetch messages between the buyer and seller
$sql = "SELECT * FROM messages 
        WHERE (buyer_id = $buyerId AND seller_id = $sellerId) 
           OR (buyer_id = $sellerId AND seller_id = $buyerId) 
        ORDER BY timestamp ASC";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $class = $row['sender_id'] == $buyerId ? 'sent' : 'received';
        echo "<div class='chat-bubble $class'>" . htmlspecialchars($row['message']) . "
              <div class='chat-timestamp'>" . date('H:i', strtotime($row['timestamp'])) . "</div>
              </div>";
    }
} else {
    echo "<p>No messages yet. Start the conversation!</p>";
}
?>
