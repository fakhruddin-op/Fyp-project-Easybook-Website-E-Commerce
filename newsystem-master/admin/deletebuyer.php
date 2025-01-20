<?php
session_start();

// Check if the user is an admin
if ($_SESSION['accesslevel'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Database connection
require '../dbconnect.php';

// Check if a buyer ID was provided
if (isset($_GET['buyerid'])) {
    $buyerId = intval($_GET['buyerid']);

    // SQL query to delete the buyer with the provided ID
    $sql = "DELETE FROM user WHERE id = $buyerId AND accesslevel = 'buyer'";
    $result = mysqli_query($conn, $sql);

    // Redirect with success message if deletion was successful
    if ($result) {
        header('Location: managebuyers.php?success=deleted');
    } else {
        echo "Error deleting buyer: " . mysqli_error($conn);
    }
} else {
    // Redirect to the manage buyers page if no ID is specified
    header('Location: managebuyers.php');
    exit();
}
?>
