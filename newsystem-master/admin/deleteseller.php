<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: login.php');
    exit();
}
require '../dbconnect.php';

$sellerId = $_GET['sellerid'];

$sql = "DELETE FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sellerId);
if ($stmt->execute()) {
    header("Location: manageseller.php?success=deleted");
} else {
    echo "Error: " . $stmt->error;
}
?>
