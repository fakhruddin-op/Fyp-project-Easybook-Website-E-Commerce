<?php
session_start();
require '../dbconnect.php';

$userid = $_GET['userid'];
$timeframe = $_GET['timeframe'];

// SQL query to get sales data based on timeframe
if ($timeframe == 'week') {
    $sql = "SELECT DATE(orderdate) AS date, SUM(price) AS total_sales
            FROM orderbook
            WHERE ownerid = '$userid' AND buyerid != 0
            GROUP BY DATE(orderdate)
            ORDER BY DATE(orderdate) DESC LIMIT 7";
} elseif ($timeframe == 'month') {
    $sql = "SELECT DATE(orderdate) AS date, SUM(price) AS total_sales
            FROM orderbook
            WHERE ownerid = '$userid' AND buyerid != 0
            GROUP BY DATE(orderdate)
            ORDER BY DATE(orderdate) DESC LIMIT 30";
} else { // year
    $sql = "SELECT DATE_FORMAT(orderdate, '%Y-%m') AS date, SUM(price) AS total_sales
            FROM orderbook
            WHERE ownerid = '$userid' AND buyerid != 0
            GROUP BY DATE_FORMAT(orderdate, '%Y-%m')
            ORDER BY DATE(orderdate) DESC LIMIT 12";
}

$result = mysqli_query($conn, $sql);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
echo json_encode($data);
?>
