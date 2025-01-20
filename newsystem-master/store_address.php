<?php
session_start();
require 'dbconnect.php';

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Sanitize and fetch form inputs
$user_id = $_SESSION['id'];
$recipient_name = mysqli_real_escape_string($conn, $_POST['recipient_name']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$city = mysqli_real_escape_string($conn, $_POST['city']);
$state = mysqli_real_escape_string($conn, $_POST['state']);
$postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
$phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

// Insert the shipping address into the database
$sql = "INSERT INTO shipping_address (user_id, recipient_name, address, city, state, postal_code, phone_number)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $user_id, $recipient_name, $address, $city, $state, $postal_code, $phone_number);

if ($stmt->execute()) {
    echo "<p>Shipping address stored successfully!</p>";
    // Redirect to a success page or payment confirmation page
    header("Location: my_purchase.php");
    exit();
} else {
    echo "<p>Error: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
