<?php
session_start();
include("header.template.php");
require '../dbconnect.php';

// Check if the user is an admin
if (!isset($_SESSION['id']) || $_SESSION['accesslevel'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch total sales for each seller
$sql = "SELECT 
            user.id AS seller_id,
            user.username AS seller_name,
            user.contact,
            COUNT(orderbook.idbook) AS total_books_sold,
            SUM(orderbook.price) AS total_sales
        FROM orderbook
        JOIN user ON orderbook.ownerid = user.id
        WHERE orderbook.is_purchased = 1
        GROUP BY user.id, user.username, user.contact
        ORDER BY total_sales DESC";

$result = mysqli_query($conn, $sql);
?>



<div class="container mt-5">
  <h2 class="dashboard-title text-center"><i class="fas fa-chart-line icon"></i> Total Sales for Each Seller</h2>
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0"></i> Sales Summary</h4>
    </div>
    <div class="card-body p-4">
      <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered text-center" id="dataTable">
        <thead class="thead-dark">
            <tr>
              <th>#</th>
              <th>Seller Name</th>
              <th>Contact</th>
              <th>Total Books Sold</th>
              <th>Total Sales (RM)</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
              <?php $counter = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                  <td><?= $counter++ ?></td>
                  <td><?= htmlspecialchars($row['seller_name']) ?></td>
                  <td><?= htmlspecialchars($row['contact']) ?></td>
                  <td><?= $row['total_books_sold'] ?></td>
                  <td>RM <?= number_format($row['total_sales'], 2) ?></td>
                  <td>
                    <a href="admin_seller_details.php?seller_id=<?= $row['seller_id'] ?>" class="btn btn-info btn-sm">
                      <i class="fas fa-info-circle"></i> View Details
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No sales data available.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('#dataTable').DataTable({
      "pageLength": 10,
      "order": [[4, "desc"]],
      "language": {
        "search": "Search:",
        "paginate": {
          "previous": "Previous",
          "next": "Next"
        }
      }
    });
  });
</script>

</body>
</html>
<?php include("footer.template.php"); ?>
