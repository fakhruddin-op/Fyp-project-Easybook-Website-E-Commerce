<?php
session_start();
require 'dbconnect.php';

if (isset($_GET['idbook'])) {
    $idbook = $_GET['idbook'];
    $buyerid = $_SESSION['id'];

    // SQL query to cancel the booking by setting buyerid to NULL
    $sql = "UPDATE orderbook SET buyerid = NULL WHERE idbook = '$idbook' AND buyerid = '$buyerid'";
    $qr = mysqli_query($conn, $sql);

    if ($qr) {
        // Optionally redirect back to my booking page or main page
        header("Location: my_cart.php?message=Booking cancelled successfully.");
        exit();
    } else {
        echo "Error cancelling booking: " . mysqli_error($conn);
    }
} else {
    echo "No book ID specified.";
}
?>
