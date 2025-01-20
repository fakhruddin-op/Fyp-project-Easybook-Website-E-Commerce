<?php
session_start();
require '../dbconnect.php';

$userid = $_GET['userid'];

// SQL query to get counts of sold and unsold books
$sql = "
SELECT 
    (SELECT COUNT(*) FROM orderbook WHERE ownerid = '$userid' AND buyerid != 0) AS sold,
    (SELECT COUNT(*) FROM orderbook WHERE ownerid = '$userid' AND (buyerid = 0 OR buyerid IS NULL)) AS unsold
";

$result = mysqli_query($conn, $sql);

if ($result) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode($data);
} else {
    echo json_encode(['sold' => 0, 'unsold' => 0]); // Default values in case of an error
}
?>
