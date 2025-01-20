<?php
session_start();
if ($_SESSION['accesslevel'] != 'seller') {
    header('location: ../login.php');
    exit();
}

include "header.template.php";
require '../dbconnect.php';

$userid = $_SESSION['id'];

// Fetch total sales summary
$sales_summary_sql = "SELECT SUM(price) AS total_sales, COUNT(*) AS total_books_sold 
                      FROM orderbook 
                      WHERE ownerid = '$userid' AND buyerid != 0";
$sales_summary_result = mysqli_query($conn, $sales_summary_sql);
$sales_summary = mysqli_fetch_assoc($sales_summary_result);

$total_sales = $sales_summary['total_sales'] ? $sales_summary['total_sales'] : 0;
$total_books_sold = $sales_summary['total_books_sold'] ? $sales_summary['total_books_sold'] : 0;

// Fetch total books listed by seller
$total_books_sql = "SELECT COUNT(*) AS total_books FROM orderbook WHERE ownerid = '$userid'";
$total_books_result = mysqli_query($conn, $total_books_sql);
$total_books = mysqli_fetch_assoc($total_books_result)['total_books'];

$books_not_sold = $total_books - $total_books_sold;
?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Sales Summary</h1>

    <div class="row mb-4">
        <!-- Total Books Listed by Seller -->
        <div class="col-lg-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Books Listed</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_books; ?></div>
                </div>
            </div>
        </div>

        <!-- Total Books Sold -->
        <div class="col-lg-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Books Sold</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_books_sold; ?></div>
                </div>
            </div>
        </div>

        <!-- Total Sales Amount -->
        <div class="col-lg-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Sales Amount</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">RM<?php echo number_format($total_sales, 2); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart for Books Sold vs Not Sold -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Books Sold vs Not Sold</h6>
                </div>
                <div class="card-body">
                    <canvas id="booksPieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bar Chart for Sales Summary -->
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sales Summary Bar Chart</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Pie Chart for Books Sold vs Not Sold
    const booksPieChartCanvas = document.getElementById('booksPieChart').getContext('2d');
    new Chart(booksPieChartCanvas, {
        type: 'pie',
        data: {
            labels: ['Books Sold', 'Books Not Sold'],
            datasets: [{
                data: [<?php echo json_encode($total_books_sold); ?>, <?php echo json_encode($books_not_sold); ?>],
                backgroundColor: ['#36A2EB', '#FFCE56'],
                hoverBackgroundColor: ['#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) label += ': ';
                            label += context.raw;
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Bar Chart for Sales Summary
    const salesBarChartCanvas = document.getElementById('salesBarChart').getContext('2d');
    new Chart(salesBarChartCanvas, {
        type: 'bar',
        data: {
            labels: ['Total Books Listed', 'Books Sold', 'Books Not Sold', 'Total Sales (RM)'],
            datasets: [{
                label: 'Summary',
                data: [
                    <?php echo json_encode($total_books); ?>, 
                    <?php echo json_encode($total_books_sold); ?>, 
                    <?php echo json_encode($books_not_sold); ?>, 
                    <?php echo json_encode($total_sales); ?>
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc'],
                borderColor: ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc'],
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
                        text: 'Value'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Category'
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

<?php include "footer.template.php"; ?>
