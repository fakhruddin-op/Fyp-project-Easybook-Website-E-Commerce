<?php
require 'dbconnect.php';

$buyer_id = $_GET['buyer_id'];
$seller_id = $_GET['seller_id'];

$sql = "SELECT * FROM messages 
        WHERE buyer_id = '$buyer_id' AND seller_id = '$seller_id' 
        ORDER BY timestamp ASC";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $class = ($row['sender_id'] == $buyer_id) ? "buyer-message" : "seller-message";
    echo "<div class='message $class'>" . htmlspecialchars($row['message']);
    echo "<br><small><em>" . date('H:i:s', strtotime($row['timestamp'])) . "</em></small></div>";
}
