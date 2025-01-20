<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: ../login.php');
    exit();
}

include "../dbconnect.php";
include "header.template.php";

// Initialize variables with default values
$total_users = $total_sellers = $total_buyers = $total_books = $sold_books = $total_sales_amount = $not_sold_books = 0;

// Fetch total sellers and buyers
$result_sellers = mysqli_query($conn, "SELECT COUNT(*) AS total_sellers FROM user WHERE accesslevel = 'seller'");
$result_buyers = mysqli_query($conn, "SELECT COUNT(*) AS total_buyers FROM user WHERE accesslevel = 'buyer'");
if ($result_sellers && $result_buyers) {
    $total_sellers = mysqli_fetch_assoc($result_sellers)['total_sellers'];
    $total_buyers = mysqli_fetch_assoc($result_buyers)['total_buyers'];
    $total_users = $total_sellers + $total_buyers;
}

// Fetch total books and unsold books from `orderbook`
$result_total_books = mysqli_query($conn, "SELECT COUNT(*) AS total_books FROM orderbook");
if ($result_total_books) {
    $total_books = mysqli_fetch_assoc($result_total_books)['total_books'];
}

// Fetch sold books and total sales amount for purchased books
$result_sold_books = mysqli_query($conn, "SELECT COUNT(*) AS sold_books, SUM(price) AS total_sales_amount FROM orderbook WHERE is_purchased = 1");
if ($result_sold_books) {
    $sold_data = mysqli_fetch_assoc($result_sold_books);
    $sold_books = $sold_data['sold_books'];
    $total_sales_amount = $sold_data['total_sales_amount'];
    $not_sold_books = $total_books - $sold_books;
}

// Fetch purchased book details with buyer and seller info
$sql_books = "
    SELECT 
        ob.bookname, 
        ob.price, 
        s.username AS seller_name, 
        b.username AS buyer_name 
    FROM orderbook ob
    JOIN user s ON ob.ownerid = s.id
    LEFT JOIN user b ON ob.buyerid = b.id
    WHERE ob.is_purchased = 1";

$result_books = mysqli_query($conn, $sql_books);

// Fetch order status counts
$order_status_counts = [];
$result_order_status = mysqli_query($conn, "SELECT order_status, COUNT(*) AS count FROM orderbook GROUP BY order_status");
if ($result_order_status) {
    while ($row = mysqli_fetch_assoc($result_order_status)) {
        $order_status_counts[$row['order_status']] = $row['count'];
    }
}

?>

<body>
    <div class="container-fluid">
        
        <!-- Sales Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Books</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_books); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Books Sold</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($sold_books); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Books Not Sold</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($not_sold_books); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales Amount</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">RM <?php echo number_format((float)$total_sales_amount, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Metrics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_users); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Sellers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_sellers); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Buyers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_buyers); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart for Sellers vs Buyers -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">User Distribution: Sellers vs Buyers</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="userPieChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Bar Chart for Sales Metrics -->
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Sales Metrics</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="salesBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        

       

        <!-- Purchased Book Details Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Purchased Book Details</h6>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result_books)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['bookname']); ?></td>
                                    <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['buyer_name'] ?? 'N/A'); ?></td>
                                    <td>RM <?php echo number_format((float)$row['price'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Pie chart for Order Status
        const ctxOrderStatus = document.getElementById('orderStatusPieChart').getContext('2d');
        const orderStatusPieChart = new Chart(ctxOrderStatus, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($order_status_counts)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($order_status_counts)); ?>,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800'],
                    hoverBackgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    </script>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pie chart for User Distribution
            const ctxPie = document.getElementById('userPieChart').getContext('2d');
            const userPieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['Sellers', 'Buyers'],
                    datasets: [{
                        data: [<?php echo json_encode($total_sellers); ?>, <?php echo json_encode($total_buyers); ?>],
                        backgroundColor: ['#36A2EB', '#FFCE56'],
                        hoverBackgroundColor: ['#36A2EB', '#FFCE56']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });

            // Bar chart for Sales Metrics
            const ctxBar = document.getElementById('salesBarChart').getContext('2d');
            const salesBarChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Books Sold', 'Books Not Sold', 'Total Sales Amount'],
                    datasets: [{
                        label: 'Sales Metrics',
                        data: [<?php echo json_encode($sold_books); ?>, <?php echo json_encode($not_sold_books); ?>, <?php echo json_encode($total_sales_amount); ?>],
                        backgroundColor: ['#4CAF50', '#FF9800', '#2196F3'],
                        borderColor: ['#4CAF50', '#FF9800', '#2196F3'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Amount'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</body>

<?php include "footer.template.php"; ?>
