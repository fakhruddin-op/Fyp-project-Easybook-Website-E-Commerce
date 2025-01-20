<?php
session_start();
include 'header.template.php';
require '../dbconnect.php';  // Adjusted path to dbconnect.php

$seller_id = $_SESSION['id']; // Assuming the seller is logged in and has an ID

// Redirect if not logged in as a seller
if (!$seller_id) {
    header("Location: login.php");
    exit();
}

// Fetch list of buyers who have chatted with this seller
$buyers_query = "SELECT DISTINCT user.id, user.username 
                 FROM messages 
                 JOIN user ON messages.buyer_id = user.id 
                 WHERE messages.seller_id = $seller_id";
$buyers_result = mysqli_query($conn, $buyers_query);

// Handle selecting a specific buyer to view the chat
$selected_buyer_id = isset($_GET['buyer_id']) ? (int)$_GET['buyer_id'] : null;
$chat_messages = [];
$buyer_name = '';

// Fetch the buyer's name if a specific buyer is selected
if ($selected_buyer_id) {
    $buyer_name_query = "SELECT username FROM user WHERE id = $selected_buyer_id";
    $buyer_name_result = mysqli_query($conn, $buyer_name_query);
    if ($buyer_name_result && mysqli_num_rows($buyer_name_result) > 0) {
        $buyer_data = mysqli_fetch_assoc($buyer_name_result);
        $buyer_name = htmlspecialchars($buyer_data['username']);
    }

    // Fetch chat messages between the seller and the selected buyer
    $chat_query = "SELECT * FROM messages 
                   WHERE buyer_id = $selected_buyer_id AND seller_id = $seller_id 
                   ORDER BY timestamp ASC";
    $chat_result = mysqli_query($conn, $chat_query);

    while ($row = mysqli_fetch_assoc($chat_result)) {
        $chat_messages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Chat</title>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .chat-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            height: 500px;
            overflow-y: auto;
            background-color: #f9f9f9;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 12px;
            font-size: 0.9rem;
            line-height: 1.4;
            max-width: 70%;
            position: relative;
            display: inline-block;
            clear: both;
        }

        .buyer-message {
            background-color: #e3f2fd;
            float: left;
            border-top-left-radius: 0;
            margin-right: auto;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .seller-message {
            background-color: #c8e6c9;
            float: right;
            border-top-right-radius: 0;
            margin-left: auto;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chat-box small {
            font-size: 0.8rem;
            color: #888;
            display: block;
        }

        .chat-input-container {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-top: 1px solid #ddd;
        }

        .chat-input {
            flex: 1;
            padding: 10px;
            font-size: 1rem;
            border-radius: 20px;
            border: 1px solid #ddd;
            outline: none;
            box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.05);
        }

        .send-button {
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .send-button:hover {
            background-color: #0056b3;
        }

        /* Styles for the buyer list */
        .buyer-list {
            list-style: none;
            padding: 0;
        }

        .buyer-item {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 15px;
            transition: box-shadow 0.2s, transform 0.1s;
            text-decoration: none;
            color: #333;
            display: block;
        }

        .buyer-item:hover {
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            transform: scale(1.02);
            text-decoration: none;
        }

        .buyer-item h6 {
            margin: 0;
            font-size: 1rem;
            font-weight: bold;
            color: #007bff;
        }

        .buyer-item small {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Seller Chat Interface</h2>
    
    <div class="row">
        <div class="col-md-4">
            <h5>Buyers</h5>
            <ul class="buyer-list">
                <?php while ($buyer = mysqli_fetch_assoc($buyers_result)): ?>
                    <li>
                        <a href="seller_chat.php?buyer_id=<?= $buyer['id'] ?>" class="buyer-item">
                            <h6><?= htmlspecialchars($buyer['username']) ?></h6>
                            <small>Click to chat</small>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        
        <div class="col-md-8">
            <?php if ($selected_buyer_id): ?>
                <h5>Chat with <?= $buyer_name ?></h5>
                <div class="chat-box" id="chat-box">
                    <?php foreach ($chat_messages as $message): ?>
                        <div class="message <?= $message['sender_id'] == $seller_id ? 'seller-message' : 'buyer-message' ?>">
                            <?= htmlspecialchars($message['message']) ?>
                            <small><?= date('H:i:s', strtotime($message['timestamp'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="chat-input-container">
                    <input type="text" name="message" id="message" class="chat-input" placeholder="Type a message..." required>
                    <button type="button" onclick="sendMessage()" class="send-button">Send</button>
                </div>
            <?php else: ?>
                <p class="text-muted">Select a buyer to start chatting.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script>
function sendMessage() {
    const message = document.getElementById('message').value;
    if (!message) return;

    $.post("send_message.php", {
        message: message,
        buyer_id: <?= $selected_buyer_id ?>,
        seller_id: <?= $seller_id ?>
    }, function(data) {
        $('#chat-box').append(data); // Append new message to chat
        document.getElementById('message').value = ''; // Clear input
        scrollToBottom();
    });
}

// Refresh chat every few seconds
setInterval(function() {
    $('#chat-box').load("fetch_messages.php?buyer_id=<?= $selected_buyer_id ?>&seller_id=<?= $seller_id ?>");
}, 3000);

function scrollToBottom() {
    const chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
}
scrollToBottom();
</script>

</body>
</html>

<?php
include 'footer.template.php';
?>
