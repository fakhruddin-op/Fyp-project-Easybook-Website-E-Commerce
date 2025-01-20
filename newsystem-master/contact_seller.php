<?php
session_start();
require 'dbconnect.php';

$buyer_id = $_SESSION['id'];

// Check if seller_id exists in the URL
if (!isset($_GET['seller_id'])) {
    die("Seller ID is required to start a chat.");
}

$seller_id = $_GET['seller_id']; // Seller ID passed via URL

// Fetch the seller's name
$seller_query = "SELECT username FROM user WHERE id = $seller_id";
$seller_result = mysqli_query($conn, $seller_query);
if ($seller_result && mysqli_num_rows($seller_result) > 0) {
    $seller = mysqli_fetch_assoc($seller_result);
    $seller_name = htmlspecialchars($seller['username']);
} else {
    die("Seller not found.");
}

// Fetch chat messages between buyer and seller
$sql = "SELECT * FROM messages 
        WHERE (buyer_id = $buyer_id AND seller_id = $seller_id) 
        ORDER BY timestamp ASC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with <?= $seller_name ?></title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .chat-box {
            border: 1px solid #ddd;
            padding: 15px;
            height: 500px;
            overflow-y: auto;
            background-color: #f9f9f9;
            margin-bottom: 15px;
        }
        .message {
            margin: 5px 0;
            padding: 10px;
            border-radius: 10px;
        }
        .buyer-message {
            background-color: #d4edda; /* Light Green */
            color: #155724; /* Darker green text */
            text-align: right;
        }
        .seller-message {
            background-color: #cce5ff; /* Light Blue */
            color: #004085; /* Darker blue text */
        }
        .form-group input {
            padding: 10px;
            font-size: 14px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .form-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .form-buttons button {
            flex: 0 0 auto; /* Prevent buttons from stretching */
            padding: 8px 15px; /* Smaller button size */
            font-size: 14px; /* Smaller font size */
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .form-buttons .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .form-buttons .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .form-buttons button:hover {
            opacity: 0.85; /* Slight hover effect */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Chat with <?= $seller_name ?></h2>
    <div class="chat-box" id="chat-box">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="message <?= $row['sender_id'] == $buyer_id ? 'buyer-message' : 'seller-message' ?>">
                <?= htmlspecialchars($row['message']) ?>
                <br><small><em><?= date('H:i:s', strtotime($row['timestamp'])) ?></em></small>
            </div>
        <?php endwhile; ?>
    </div>

    <form id="chat-form" method="post">
        <div class="form-group">
            <input type="text" name="message" id="message" class="form-control" placeholder="Type a message..." required>
        </div>
        <div class="form-buttons">
            <button type="button" onclick="window.history.back();" class="btn btn-secondary">Back</button>
            <button type="button" onclick="sendMessage()" class="btn btn-primary">Send</button>
        </div>
    </form>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script>
function sendMessage() {
    const message = document.getElementById('message').value;
    if (!message) return;

    $.post("send_message.php", {
        message: message,
        buyer_id: <?= $buyer_id ?>,
        seller_id: <?= $seller_id ?>
    }, function(data) {
        $('#chat-box').append(data); // Append new message to chat
        document.getElementById('message').value = ''; // Clear input
        scrollToBottom();
    });
}

// Refresh chat every few seconds
setInterval(function() {
    $('#chat-box').load("fetch_messages.php?buyer_id=<?= $buyer_id ?>&seller_id=<?= $seller_id ?>");
}, 3000);

function scrollToBottom() {
    const chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
}
scrollToBottom();
</script>

</body>
</html>
