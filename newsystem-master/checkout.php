<?php
session_start();
require 'dbconnect.php';

$buyerid = $_SESSION['id'];
$idbook = $_GET['idbook'];

// Mark the book as sold by setting `buyerid` to the current user ID
$sql = "UPDATE orderbook 
        SET buyerid = '$buyerid'
        WHERE idbook = '$idbook'";

if (mysqli_query($conn, $sql)) {
    // Redirect the user to the payment page after marking as sold
    header("Location: payment.php?idbook=$idbook");
    exit();
} else {
    echo "Error updating record: " . mysqli_error($conn);
}


?>
