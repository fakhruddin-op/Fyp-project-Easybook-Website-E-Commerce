<?php
session_start();
require 'dbconnect.php';

$buyerid = $_SESSION['id'];
$idbook = isset($_GET['idbook']) ? $_GET['idbook'] : null;

if ($idbook) {
    // Simulate successful payment and mark the book as purchased
    $sql = "UPDATE orderbook SET is_purchased = 1 WHERE idbook = '$idbook' AND buyerid = '$buyerid'";
    if (mysqli_query($conn, $sql)) {
        // Redirect to my_purchases.php after successful payment
        header("location: my_purchases.php?status=success");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid book ID.";
}
?>
