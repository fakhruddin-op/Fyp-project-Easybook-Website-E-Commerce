<?php
session_start();
require '../dbconnect.php';

$userid = $_GET['userid'];

// Query to get book distribution by category (e.g., genre, type)
$sql = "SELECT category, COUNT(*) AS count 
        FROM orderbook 
        WHERE ownerid = '$userid' AND buyerid != 0 
        GROUP BY category";

$result = mysqli_query($conn, $sql);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>
