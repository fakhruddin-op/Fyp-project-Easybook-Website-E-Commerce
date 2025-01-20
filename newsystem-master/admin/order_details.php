<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: ../login.php');
    exit();
}

include "../dbconnect.php"; // Database connection

// Check if the database connection was successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

include "header.template.php";

// Query to fetch book details along with buyer and seller information from `orderbook`
$sql_books = "
    SELECT 
        ob.bookname, 
        ob.price, 
        s.username AS seller_name, 
        b.username AS buyer_name,
        ob.order_status
    FROM orderbook ob
    JOIN user s ON ob.ownerid = s.id
    LEFT JOIN user b ON ob.buyerid = b.id
    WHERE ob.is_purchased = 1";  // Only fetch books that have been purchased

$result_books = mysqli_query($conn, $sql_books);

// Check if the query executed successfully
if (!$result_books) {
    echo "Error fetching book details: " . mysqli_error($conn);
    exit();
}

// Debugging - Check if any rows are returned
if (mysqli_num_rows($result_books) == 0) {
    echo "<p class='text-danger'>No data found for purchased books. Ensure that there are records with is_purchased='1' in the 'orderbook' table and that related user records exist.</p>";
} else {
    echo "<p class='text-success'></p>";
}
?>

<body>
    <div class="container-fluid">
        <!-- Book Sales Details Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Book Sales Details</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Book Name</th>
                                <th>Seller Name</th>
                                <th>Buyer Name</th>
                                <th>Price (RM)</th>
                                <th>Stauts Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Display each book sale record
                            while ($row = mysqli_fetch_assoc($result_books)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['bookname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['buyer_name'] ?? 'N/A'); ?></td> <!-- Handle NULL values for buyer -->
                                    <td>RM <?php echo number_format((float)$row['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include "footer.template.php"; ?>
