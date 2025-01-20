<?php
session_start();
if ($_SESSION['accesslevel'] != 'seller') {
    header('location: ../login.php');
    exit();
}

include "header.template.php";
require '../dbconnect.php';

$userid = $_SESSION['id'];

// Fetch total sales summary based on `buyerid`
$sales_summary_sql = "SELECT SUM(price) AS total_sales, COUNT(*) AS total_books_sold 
                      FROM orderbook 
                      WHERE ownerid = '$userid' AND payment_status = 'paid'";
$sales_summary_result = mysqli_query($conn, $sales_summary_sql);
$sales_summary = mysqli_fetch_assoc($sales_summary_result);

$total_sales = $sales_summary['total_sales'] ? $sales_summary['total_sales'] : 0;
$total_books_sold = $sales_summary['total_books_sold'] ? $sales_summary['total_books_sold'] : 0;

// Fetch total books listed by seller
$total_books_sql = "SELECT COUNT(*) AS total_books FROM orderbook WHERE ownerid = '$userid'";
$total_books_result = mysqli_query($conn, $total_books_sql);
$total_books = mysqli_fetch_assoc($total_books_result)['total_books'];

$books_not_sold = $total_books - $total_books_sold;

// Fetch total payment received by the seller
$payment_received_sql = "SELECT SUM(total_amount) AS total_payment_received 
                         FROM payments 
                         WHERE seller_id = '$userid'";
$payment_received_result = mysqli_query($conn, $payment_received_sql);
$payment_received = mysqli_fetch_assoc($payment_received_result);

$total_payment_received = $payment_received['total_payment_received'] ? $payment_received['total_payment_received'] : 0;

// Fetch book listing for the seller
$sql = "SELECT o.*, u.*, 
                CASE 
                   WHEN o.payment_status = 'paid' THEN 'Sold' 
                   WHEN o.payment_status IS NULL THEN 'Not Sold' 
                   ELSE 'Not Sold' 
               END AS sale_status
        FROM orderbook as o 		
        JOIN user as u
        ON o.ownerid = u.id 
        WHERE o.ownerid = '$userid'";
$rs = mysqli_query($conn, $sql);
if (mysqli_error($conn)) {
    echo 'Error: ' . mysqli_error($conn);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Easy Book Dashboard</title>
    <style>
        .custom-card {
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }
        
        .custom-card .title {
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .custom-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        /* Card Colors */
        .card-total-books {
            border-left: 4px solid #36b9cc;
        }
        .card-total-books .title {
            color: #36b9cc;
        }

        .card-books-sold {
            border-left: 4px solid #1cc88a;
        }
        .card-books-sold .title {
            color: #1cc88a;
        }

        .card-books-not-sold {
            border-left: 4px solid #f6c23e;
        }
        .card-books-not-sold .title {
            color: #f6c23e;
        }

        .card-total-sales {
            border-left: 4px solid #4e73df;
        }
        .card-total-sales .title {
            color: #4e73df;
        }

        /* New Card for Total Payment Received */
        .card-total-payment {
            border-left: 4px solid #ff851b;
        }
        .card-total-payment .title {
            color: #ff851b;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800 font-weight-bold">Dashboard - Sales Summary</h1>

    <div class="row mb-4">
        <!-- Total Books Listed by Seller -->
        <div class="col-md-3">
            <div class="custom-card card-total-books">
                <div class="title">Total Books Listed</div>
                <div class="value"><?php echo $total_books; ?></div>
            </div>
        </div>

        <!-- Total Books Sold -->
        <div class="col-md-3">
            <div class="custom-card card-books-sold">
                <div class="title">Total Books Sold</div>
                <div class="value"><?php echo $total_books_sold; ?></div>
            </div>
        </div>

        <!-- Books Not Sold -->
        <div class="col-md-3">
            <div class="custom-card card-books-not-sold">
                <div class="title">Books Not Sold</div>
                <div class="value"><?php echo $books_not_sold; ?></div>
            </div>
        </div>

        <!-- Total Sales Amount -->
        <div class="col-md-3">
            <div class="custom-card card-total-sales">
                <div class="title">Total Sales Amount</div>
                <div class="value">RM <?php echo number_format($total_sales, 2); ?></div>
            </div>
            
        </div>
        

        <!-- Total Payment Received -->
        <div class="col-md-3">
            <div class="custom-card card-total-payment">
                <div class="title">Total Payment Received</div>
                <div class="value">RM <?php echo number_format($total_payment_received, 2); ?></div>
            </div>
        </div>
    </div>

    <!-- Pie and Bar Charts Row -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Books Sold vs Not Sold</h6>
                </div>
                <div class="card-body">
                    <canvas id="booksPieChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Sales Summary</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Listing Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">My Books</h6>
        </div>
        <div class="card-body">
            <?php
            if (isset($_GET['success'])) {
                if ($_GET['success'] == "saved") {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Successfully updated book information <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                } elseif ($_GET['success'] == "deleted") {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">Successfully deleted the book <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                }
            }
            ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Title</th>
                            <th>Price (RM)</th>
                            <th>Code Book</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Sale Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php 
    while ($rec = mysqli_fetch_array($rs)) {
    ?>
    <tr>
        <td><?php echo htmlspecialchars($rec['bookname']); ?></td>
        <td>RM <?php echo htmlspecialchars($rec['price']); ?></td>
        <td><?php echo htmlspecialchars($rec['bookcodesubject']); ?></td>
        <td><?php echo htmlspecialchars($rec['description']); ?></td>
        <td><?php echo htmlspecialchars($rec['approval_status']); ?></td>
        <td><?php echo htmlspecialchars($rec['sale_status']); ?></td>
        <td>
            <?php if ($rec['sale_status'] !== 'Sold') { ?>
                <a href="updatebook.php?idbook=<?php echo $rec['idbook']; ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal<?php echo $rec['idbook']; ?>">
                    <i class="fas fa-trash"></i> Delete
                </a>
            <?php } ?>
        </td>
    </tr>

    <!-- Delete Confirmation Modal -->
    <?php if ($rec['sale_status'] !== 'Sold') { ?>
    <div class="modal fade" id="deleteModal<?php echo $rec['idbook']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this book?</p>
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($rec['bookname']); ?></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger" href="deletebook4user.php?bookid=<?php echo $rec['idbook']; ?>">Yes, Delete</a>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php } ?>
</tbody>

                </table>
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
                y: { beginAtZero: true, title: { display: true, text: 'Value' }},
                x: { title: { display: true, text: 'Category' }}
            },
            plugins: { legend: { display: false }}
        }
    });
});
</script>

<?php include "footer.template.php"; ?>
