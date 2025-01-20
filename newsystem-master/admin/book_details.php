<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: ../login.php');
    exit();
}

include "header.template.php";
require '../dbconnect.php';

// Fetch total books
$total_books_sql = "SELECT COUNT(*) AS total_books FROM orderbook";
$total_books_result = mysqli_query($conn, $total_books_sql);
$total_books = mysqli_fetch_assoc($total_books_result)['total_books'];

// Fetch total sellers
$total_sellers_sql = "SELECT COUNT(DISTINCT ownerid) AS total_sellers FROM orderbook";
$total_sellers_result = mysqli_query($conn, $total_sellers_sql);
$total_sellers = mysqli_fetch_assoc($total_sellers_result)['total_sellers'];

// Fetch total sellers included in the list
$sellers_in_list_sql = "SELECT COUNT(DISTINCT username) AS sellers_in_list FROM user u
                        JOIN orderbook o ON u.id = o.ownerid";
$sellers_in_list_result = mysqli_query($conn, $sellers_in_list_sql);
$sellers_in_list = mysqli_fetch_assoc($sellers_in_list_result)['sellers_in_list'];

// Fetch book listings with seller details
$sql_books = "SELECT o.*, u.username, u.contact FROM orderbook AS o
              JOIN user AS u ON o.ownerid = u.id";
$rs_books = mysqli_query($conn, $sql_books);
?>

<body>
    <div class="container-fluid">
        
        <!-- Summary Cards -->
        <div class="row mb-5">
            <!-- Total Books Card -->
            <div class="col-md-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Books</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_books); ?></div>
                    </div>
                </div>
            </div>

            <!-- Total Sellers Card -->
            <div class="col-md-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Sellers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($total_sellers); ?></div>
                    </div>
                </div>
            </div>

            <!-- Sellers in List Card -->
            <div class="col-md-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sellers in List</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($sellers_in_list); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Books Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">All Books</h6>
                <?php
                if (isset($_GET['success']) && $_GET['success'] == 'deleted') {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Successfully deleted the book.
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span></button></div>';
                }
                ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Seller Name</th>
                                <th>Title</th>
                                <th>Price (RM)</th>
                                <th>Code Book</th>
                                <th>Contact</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rec = mysqli_fetch_array($rs_books)) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($rec['username']); ?></td>
                                    <td><?= htmlspecialchars($rec['bookname']); ?></td>
                                    <td>RM <?= number_format((float)$rec['price'], 2); ?></td>
                                    <td><?= htmlspecialchars($rec['bookcodesubject']); ?></td>
                                    <td><?= htmlspecialchars($rec['contact']); ?></td>
                                    <td><?= htmlspecialchars($rec['description']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-primary" href="viewbook.php?bookid=<?= $rec['idbook']; ?>" title="View"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>

                                <!-- Delete Book Modal -->
                                <div class="modal fade" id="deleteBookModal<?= $rec['idbook']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteBookModalLabel<?= $rec['idbook']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteBookModalLabel<?= $rec['idbook']; ?>">Confirm Delete</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this book?</p>
                                                <p><strong>Seller Name:</strong> <?= htmlspecialchars($rec['username']); ?></p>
                                                <p><strong>Title:</strong> <?= htmlspecialchars($rec['bookname']); ?></p>
                                                <p><strong>Description:</strong> <?= htmlspecialchars($rec['description']); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <a href="deletebook.php?bookid=<?= $rec['idbook']; ?>" class="btn btn-danger">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include("footer.template.php"); ?>
