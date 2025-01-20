<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: ../login.php');
    exit();
}

include "../dbconnect.php";
include "header.template.php";

// Fetch total sellers and buyers
$result_sellers = mysqli_query($conn, "SELECT COUNT(*) AS total_sellers FROM user WHERE accesslevel = 'seller'");
$result_buyers = mysqli_query($conn, "SELECT COUNT(*) AS total_buyers FROM user WHERE accesslevel = 'buyer'");

if (!$result_sellers || !$result_buyers) {
    echo "Error fetching user data.";
    exit();
}

$total_sellers = mysqli_fetch_assoc($result_sellers)['total_sellers'];
$total_buyers = mysqli_fetch_assoc($result_buyers)['total_buyers'];
$total_users = $total_sellers + $total_buyers;
?>

<body>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 text-gray-800">Manage Users</h1>
        </div>

        <!-- User Metrics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Total Users
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_users); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Sellers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_sellers); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-store fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Buyers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_buyers); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pie Chart for Sellers vs Buyers -->
        <div class="row mb-4">
            <div class="col-lg-6 offset-lg-3">
                <div class="card shadow">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">User Distribution: Sellers vs Buyers</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="userPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('userPieChart').getContext('2d');
            const userPieChart = new Chart(ctx, {
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
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw;
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

<?php include "footer.template.php"; ?>
