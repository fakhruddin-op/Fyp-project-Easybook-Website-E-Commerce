<?php
session_start();
if ($_SESSION['accesslevel'] != 'seller') {
    header('location: ../login.php');
    exit();
}

include "header.template.php";
require '../dbconnect.php';

$userid = $_SESSION['id']; // Logged-in seller ID

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = mysqli_real_escape_string($conn, $_POST['order_status']);

    $update_sql = "UPDATE orderbook SET order_status = '$order_status' WHERE idbook = $order_id AND ownerid = $userid";
    if (mysqli_query($conn, $update_sql)) {
        echo "<div class='alert alert-success'>Order status updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating order status: " . mysqli_error($conn) . "</div>";
    }
}

// Fetch all orders with buyer info and selected address
$sql = "
    SELECT 
        o.idbook, 
        o.bookname, 
        o.price, 
        o.bookcodesubject, 
        o.order_status, 
        u.username AS buyer_name, 
        u.contact AS buyer_contact,
        sa.address AS buyer_address, 
        sa.city AS buyer_city, 
        sa.state AS buyer_state, 
        sa.postal_code AS buyer_postal_code
    FROM orderbook o
    JOIN user u ON o.buyerid = u.id
    LEFT JOIN shipping_address sa ON o.selected_address_id = sa.id
    WHERE o.ownerid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$rs = $stmt->get_result();

if (!$rs) {
    echo "Error fetching orders: " . $conn->error;
    exit();
}

// Fetch the count of unique buyers
$count_sql = "SELECT COUNT(DISTINCT buyerid) AS total_buyers FROM orderbook WHERE ownerid = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $userid);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_buyers = $count_result->fetch_assoc()['total_buyers'];
$count_stmt->close();
?>
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">My Book Sales</h1>
    <p class="mb-4">Track the buyers who have purchased your books, update order statuses, and view their shipping addresses.</p>

    <!-- Total Buyers Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Unique Buyers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_buyers; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Books and Buyers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Book Title</th>
                            <th>Price (RM)</th>
                            <th>Code Book</th>
                            <th>Buyer Name</th>
                            <th>Buyer Contact</th>
                            <th>Buyer Address</th>
                            <th>Order Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rec = $rs->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($rec['bookname']); ?></td>
                                <td>RM <?= number_format($rec['price'], 2); ?></td>
                                <td><?= htmlspecialchars($rec['bookcodesubject']); ?></td>
                                <td><?= htmlspecialchars($rec['buyer_name']); ?></td>
                                <td><?= htmlspecialchars($rec['buyer_contact']); ?></td>
                                <td>
                                    <?php if (!empty($rec['buyer_address'])): ?>
                                        <?= htmlspecialchars($rec['buyer_address']); ?><br>
                                        <?= htmlspecialchars($rec['buyer_city']); ?>, <?= htmlspecialchars($rec['buyer_state']); ?><br>
                                        <?= htmlspecialchars($rec['buyer_postal_code']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">No address selected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="order_id" value="<?= $rec['idbook']; ?>">
                                        <select name="order_status" class="form-control">
                                            <option value="Order Confirmed" <?= $rec['order_status'] === 'Order Confirmed' ? 'selected' : ''; ?>>Order Confirmed</option>
                                            <option value="Waiting for Courier" <?= $rec['order_status'] === 'Waiting for Courier' ? 'selected' : ''; ?>>Waiting for Courier</option>
                                            <option value="Out for Delivery" <?= $rec['order_status'] === 'Courier Pick up' ? 'selected' : ''; ?>>Courier Pick up</option>
                                            <option value="Out for Delivery" <?= $rec['order_status'] === 'Out for Delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                            <option value="Delivered" <?= $rec['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        </select>
                                </td>
                                <td>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "footer.template.php"; ?>
