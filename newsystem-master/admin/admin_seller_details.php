<?php
session_start();
include("header.template.php");
require '../dbconnect.php';

// Check if the user is an admin
if (!isset($_SESSION['id']) || $_SESSION['accesslevel'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if seller_id is passed in the URL
if (!isset($_GET['seller_id'])) {
    echo "No seller selected.";
    exit();
}

$seller_id = (int)$_GET['seller_id'];

// Fetch seller details
$sellerQuery = "SELECT username, contact FROM user WHERE id = $seller_id";
$sellerResult = mysqli_query($conn, $sellerQuery);
$seller = mysqli_fetch_assoc($sellerResult);

// Fetch book details along with buyer names
$booksQuery = "SELECT 
                  ob.idbook, ob.bookname, ob.bookcodesubject, ob.price, ob.description, 
                  u.username AS buyer_name
               FROM orderbook ob
               JOIN user u ON ob.buyerid = u.id
               WHERE ob.ownerid = $seller_id AND ob.is_purchased = 1";

$booksResult = mysqli_query($conn, $booksQuery);
?>


<div class="container mt-5">
  <h2 class="seller-title text-center mb-4">
    <i class="fas fa-user icon"></i> Seller: <?= htmlspecialchars($seller['username']) ?>
  </h2>
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white rounded-top">
      <h4 class="mb-0"></i> Sales Summary</h4>
    </div>
    <div class="card-body p-4 bg-light">
      <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered text-center" id="dataTable">
          <thead class="bg-dark text-white">
            <tr>
              <th>#</th>
              <th>Book Title</th>
              <th>Code Subject</th>
              <th>Price (RM)</th>
              <th>Description</th>
              <th>Buyer Name</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($booksResult) > 0): ?>
              <?php $counter = 1; while ($book = mysqli_fetch_assoc($booksResult)): ?>
                <tr>
                  <td><?= $counter++ ?></td>
                  <td><?= htmlspecialchars($book['bookname']) ?></td>
                  <td><?= htmlspecialchars($book['bookcodesubject']) ?></td>
                  <td>RM <?= number_format($book['price'], 2) ?></td>
                  <td><?= htmlspecialchars($book['description']) ?></td>
                  <td><?= htmlspecialchars($book['buyer_name']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6">No books found for this seller.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="text-center mt-4">
        <a href="seller_sales_details.php" class="btn btn-secondary btn-lg"></i> Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>


<?php include("footer.template.php"); ?>
