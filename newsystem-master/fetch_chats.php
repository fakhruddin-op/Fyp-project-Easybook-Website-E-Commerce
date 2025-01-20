<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['id'])) {
    echo '<div class="text-center p-3 text-danger">Please log in to view your chats.</div>';
    exit();
}

$currentUserId = $_SESSION['id'];
$sql = "SELECT DISTINCT 
            CASE 
                WHEN sender_id = $currentUserId THEN seller_id
                WHEN seller_id = $currentUserId THEN sender_id
            END AS user_id,
            user.username
        FROM messages
        JOIN user ON user.id = 
            CASE 
                WHEN sender_id = $currentUserId THEN seller_id
                WHEN seller_id = $currentUserId THEN sender_id
            END
        WHERE sender_id = $currentUserId OR seller_id = $currentUserId";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $userId = $row['user_id'];
        $username = htmlspecialchars($row['username']);
        echo "
            <a href='#' class='dropdown-item d-flex align-items-center' onclick='openChat($userId, \"$username\")'>
                <div class='font-weight-bold'>
                    <div class='text-truncate'>$username</div>
                </div>
            </a>
        ";
    }
} else {
    echo '<div class="text-center p-3">No chats available.</div>';
}
?>
