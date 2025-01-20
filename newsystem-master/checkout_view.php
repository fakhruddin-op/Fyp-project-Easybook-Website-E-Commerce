<?php
session_start();
require 'dbconnect.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['idbook'])) {
    $idbook = $_GET['idbook'];

    // Fetch the details of the booked item
    $sql = "SELECT orderbook.*, user.username, user.contact FROM orderbook JOIN user ON orderbook.ownerid = user.id WHERE orderbook.idbook = '$idbook'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $rec = mysqli_fetch_assoc($result);
    } else {
        echo "Error fetching record: " . mysqli_error($conn);
        exit();
    }
} else {
    echo "No booking ID specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout Successful</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

<div class="jumbotron">
    <div class="container text-center">
        <h1 class="h1 mb-1 text-gray-800">Checkout Successful</h1>
        <p>Your booking for <strong><?= $rec['bookname'] ?></strong> has been successfully completed!</p>
        <p>Seller: <?= $rec['username'] ?></p>
        <p>Contact: <?= $rec['contact'] ?></p>
        <a class="btn btn-primary" href="mybooking.php">View My Bookings</a>
    </div>
</div>

<?php include "footer.template.php"; ?>

</body>
</html>
