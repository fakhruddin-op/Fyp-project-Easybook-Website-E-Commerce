<?php
session_start();
if ($_SESSION['accesslevel'] != 'seller') {
    header('location: ../login.php');
    exit();
}

include "header.template.php";
require '../dbconnect.php';

$userid = $_SESSION['id'];

// Fetch payment history for the seller
$payment_history_sql = "SELECT total_amount, payment_date FROM payments WHERE seller_id = '$userid' ORDER BY payment_date DESC";
$payment_history_result = mysqli_query($conn, $payment_history_sql);

// Calculate total payments received
$total_payments_sql = "SELECT SUM(total_amount) AS total_received FROM payments WHERE seller_id = '$userid'";
$total_payments_result = mysqli_query($conn, $total_payments_sql);
$total_payments_row = mysqli_fetch_assoc($total_payments_result);
$total_received = $total_payments_row['total_received'] ? $total_payments_row['total_received'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <!-- Add your preferred CSS styling here or link to your CSS files -->
</head>
<body>
     <!-- Note about the 5% commission -->
     <div class="alert alert-info mb-4" role="alert">
        <strong>Note:</strong> A 5% platform service is deducted from the total sales amount for each book sold. The displayed payment amounts reflect this deduction.
    </div>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800 font-weight-bold">Payment History</h1>

    <!-- Total Payments Received Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow h-100 py-2" style="border-left: 4px solid #36b9cc;">
                <div class="card-body">
                    <div class="text-uppercase font-weight-bold mb-1" style="color: #36b9cc;">Total Payments Received</div>
                    <div class="h4 font-weight-bold">RM <?php echo number_format($total_received, 2); ?></div>
                </div>
            </div>
        </div>
    </div>
     

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Received Payments</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Payment Amount (RM)</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($payment_history_result) > 0) : ?>
                            <?php while ($row = mysqli_fetch_assoc($payment_history_result)) : ?>
                                <tr>
                                    <td>RM <?php echo number_format((float)$row['total_amount'], 2); ?></td>
                                    <td><?php echo date("d-m-Y H:i:s", strtotime($row['payment_date'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="2" class="text-center">No payments received yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "footer.template.php"; ?>
</body>
</html>
