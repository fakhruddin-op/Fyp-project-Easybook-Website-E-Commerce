<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: ../login.php');
    exit();
}

include "../dbconnect.php";
include "header.template.php";

// Fetch total unpaid sales for each seller from `orderbook`
$sellers_sales = mysqli_query($conn, "
    SELECT u.id AS seller_id, u.username AS seller_name, 
           COUNT(ob.idbook) AS books_sold, 
           SUM(ob.price) AS total_sales,
           (SUM(ob.price) * 0.05) AS platform_fee
    FROM user u
    JOIN orderbook ob ON u.id = ob.ownerid
    WHERE ob.is_purchased = 1 AND ob.is_paid = 0 AND u.accesslevel = 'seller'
    GROUP BY u.id, u.username
");

// Calculate total commission from all unpaid sales
$total_commission_query = mysqli_query($conn, "
    SELECT SUM(commission) AS total_commission FROM payments
");
$total_commission_result = mysqli_fetch_assoc($total_commission_query);
$total_commission = $total_commission_result['total_commission'] ? $total_commission_result['total_commission'] : 0;

// Fetch payment history
$payment_history = mysqli_query($conn, "
    SELECT p.id AS payment_id, u.username AS seller_name, 
           p.total_amount AS amount_paid, p.payment_date
    FROM payments p
    JOIN user u ON p.seller_id = u.id
    ORDER BY p.payment_date DESC
");
?>

<body>
    <div class="container-fluid">
        <h2 class="mt-4">Seller Payments</h2>

        <!-- Total Commission Earned Card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow h-100 py-2" style="border-left: 4px solid #dc3545;">
                    <div class="card-body">
                        <div class="text-uppercase font-weight-bold text-danger mb-1">
                            Total Services Platform Earned
                        </div>
                        <div class="h4 font-weight-bold text-gray-800">
                            RM <?php echo number_format($total_commission, 2); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seller Payment Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Sellers with Unpaid Sales</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Seller Name</th>
                                <th>Books Sold (Unpaid)</th>
                                <th>Total Sales (Unpaid) (RM)</th>
                                <th>Service Platform Fee (5%) (RM)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($sellers_sales)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['books_sold']); ?></td>
                                    <td>RM <?php echo number_format((float)$row['total_sales'], 2); ?></td>
                                    <td>RM <?php echo number_format((float)$row['platform_fee'], 2); ?></td>
                                    <td>
                                        <form action="process_payment.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="seller_id" value="<?php echo htmlspecialchars($row['seller_id']); ?>">
                                            <input type="hidden" name="total_sales" value="<?php echo htmlspecialchars($row['total_sales']); ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Pay</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment History Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Payment History</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Payment ID</th>
                                <th>Seller Name</th>
                                <th>Amount Paid (RM)</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($payment_history)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['seller_name']); ?></td>
                                    <td>RM <?php echo number_format((float)$row['amount_paid'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
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
