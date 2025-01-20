<?php
require '../dbconnect.php';

$buyer_id = (int)$_GET['buyer_id'];
$seller_id = (int)$_GET['seller_id'];

$query = "SELECT * FROM messages 
          WHERE buyer_id = '$buyer_id' AND seller_id = '$seller_id' 
          ORDER BY timestamp ASC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $class = ($row['sender_id'] == $seller_id) ? "seller-message" : "buyer-message";
    echo "<div class='message $class'>" . htmlspecialchars($row['message']);
    echo "<br><small>" . date('H:i:s', strtotime($row['timestamp'])) . "</small></div>";
}
?>
