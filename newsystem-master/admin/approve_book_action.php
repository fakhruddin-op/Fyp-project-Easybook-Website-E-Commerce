<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

include "../dbconnect.php";

// Get the action and book ID
$action = $_GET['action'];
$idbook = intval($_GET['idbook']);

if ($action === 'approve') {
    $sql = "UPDATE orderbook SET approval_status = 'approved' WHERE idbook = $idbook";
} elseif ($action === 'reject') {
    $sql = "UPDATE orderbook SET approval_status = 'rejected' WHERE idbook = $idbook";
}

// Execute the query
if (mysqli_query($conn, $sql)) {
    header('Location: approval_book.php?success=' . $action);
} else {
    echo "Error updating record: " . mysqli_error($conn);
}
?>
