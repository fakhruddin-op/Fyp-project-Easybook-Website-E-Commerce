<?php
session_start();
if ($_SESSION['accesslevel'] != 'seller') {
    header('location: ../login.php');
    exit();
}

include "header.template.php";
require '../dbconnect.php';

$userid = $_SESSION['id'];

// Fetch the books owned by the current seller, including the approval status
$sql = "SELECT o.*, u.username AS seller_name
        FROM orderbook AS o
        JOIN user AS u ON o.ownerid = u.id
        WHERE o.ownerid = '$userid'";
$rs = mysqli_query($conn, $sql);
if (mysqli_error($conn)) {
    echo 'Error: ' . mysqli_error($conn);
    exit();
}
?>

<div class="container-fluid mt-4">
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">My Books</h1>

    <!-- Success Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-<?= $_GET['success'] == 'saved' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?= $_GET['success'] == 'saved' ? 'Successfully updated book information.' : 'Successfully deleted the book.'; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Book List Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">My Book Collection</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
    <tr>
        <th>Title</th>
        <th>Price (RM)</th>
        <th>Book Code</th>
        <th>Description</th>
        <th>Status</th>
        <th>Sold Status</th> <!-- New Column -->
        <th class="text-center">Actions</th>
    </tr>
</thead>
<tbody>
    <?php while ($rec = mysqli_fetch_array($rs)): ?>
        <tr>
            <td><?= htmlspecialchars($rec['bookname']); ?></td>
            <td>RM <?= number_format($rec['price'], 2); ?></td>
            <td><?= htmlspecialchars($rec['bookcodesubject']); ?></td>
            <td><?= htmlspecialchars($rec['description']); ?></td>
            <td>
                <?php 
                // Display book approval status with label styling
                $status = $rec['approval_status'];
                if ($status === 'approved') {
                    echo '<span class="badge badge-success">Approved</span>';
                } elseif ($status === 'pending') {
                    echo '<span class="badge badge-warning">Pending</span>';
                } elseif ($status === 'rejected') {
                    echo '<span class="badge badge-danger">Rejected</span>';
                } else {
                    echo '<span class="badge badge-secondary">Unknown</span>';
                }
                ?>
            </td>
            <td>
                <?php 
                // Display sold status
                if ($rec['payment_status'] === 'paid') {
                    echo '<span class="badge badge-success">Sold</span>';
                } else {
                    echo '<span class="badge badge-danger">Not Sold</span>';
                }
                ?>
            </td>
            <td class="text-center">
                <?php if ($rec['payment_status'] !== 'paid'): ?>
                    <!-- Edit Button -->
                    <a href="updatebook.php?idbook=<?= $rec['idbook']; ?>" class="btn btn-sm btn-info" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <!-- Delete Button -->
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?= $rec['idbook']; ?>" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                <?php endif; ?>
            </td>
        </tr>

        <!-- Delete Confirmation Modal -->
        <?php if ($rec['payment_status'] !== 'paid'): ?>
        <div class="modal fade" id="deleteModal<?= $rec['idbook']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?= $rec['idbook']; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel<?= $rec['idbook']; ?>">Confirm Deletion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this book?</p>
                        <p><strong>Title:</strong> <?= htmlspecialchars($rec['bookname']); ?></p>
                        <p><strong>Seller:</strong> <?= htmlspecialchars($rec['seller_name']); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <a href="deletebook4user.php?bookid=<?= $rec['idbook']; ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endwhile; ?>
</tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<?php include("footer.template.php"); ?>
