<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

include "../dbconnect.php";

$bookId = $_GET['id'];
$status = $_GET['status'];

// Ensure status is either 'approved' or 'rejected'
if (in_array($status, ['approved', 'rejected'])) {
    $sql = "UPDATE orderbook SET approval_status='$status' WHERE idbook='$bookId'";
    if (mysqli_query($conn, $sql)) {
        header("Location: approval_page.php?success=$status");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid action.";
}
?>
