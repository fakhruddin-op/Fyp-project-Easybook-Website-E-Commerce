<?php
session_start();
require 'dbconnect.php';

// Check if 'query' is set in the URL parameters
if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $searchKeyword = mysqli_real_escape_string($conn, trim($_GET['query']));

    $sql = "SELECT orderbook.*, user.contact, user.username 
            FROM orderbook 
            JOIN user ON orderbook.ownerid = user.id 
            WHERE (orderbook.bookname LIKE '%$searchKeyword%' 
                   OR orderbook.description LIKE '%$searchKeyword%' 
                   OR user.username LIKE '%$searchKeyword%')
            AND orderbook.approval_status = 'approved'";

    $qr = mysqli_query($conn, $sql);

    if (mysqli_error($conn)) {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // If the query is not set or empty, redirect back to the homepage or show a message
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Results - Easy Book</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Search Results for "<?= htmlspecialchars($searchKeyword) ?>"</h3>
    <div class="row">
        <?php 
        if (mysqli_num_rows($qr) > 0) {
            while ($rec = mysqli_fetch_array($qr)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="seller/bookcover/<?= $rec['bookcover'] ?>" class="card-img-top" alt="Book Cover">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($rec['bookname']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($rec['description']) ?></p>
                        <p>Seller: <?= htmlspecialchars($rec['username']) ?></p>
                        <p>Price: $<?= htmlspecialchars($rec['price']) ?></p>
                        <a href="#" class="btn btn-primary">View Book</a>
                    </div>
                </div>
            </div>
            <?php }
        } else {
            echo "<p>No results found for your search.</p>";
        }
        ?>
    </div>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
