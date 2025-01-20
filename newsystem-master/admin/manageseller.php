<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: login.php');
    exit();
}
require '../dbconnect.php';

// Fetch seller data from the database
$sql = "SELECT * FROM user WHERE accesslevel = 'seller'";
$rs = mysqli_query($conn, $sql);
if (mysqli_error($conn)) {
    echo 'Error: ' . mysqli_error($conn);
    exit();
}

// Count total sellers
$total_sellers_query = "SELECT COUNT(*) AS total_sellers FROM user WHERE accesslevel = 'seller'";
$total_sellers_result = mysqli_query($conn, $total_sellers_query);
$total_sellers = mysqli_fetch_assoc($total_sellers_result)['total_sellers'];

include("header.template.php");
?>

<body>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Manage Sellers</h1>
        </div>

        <!-- Total Sellers Card -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Sellers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_sellers ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= ($_GET['success'] == 'deleted') ? 'Successfully deleted the seller.' : 'Successfully updated the seller.'; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Seller Information Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Seller Information</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Seller Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rec = mysqli_fetch_array($rs)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rec['username']); ?></td>
                                    <td><?= htmlspecialchars($rec['email']); ?></td>
                                    <td><?= htmlspecialchars($rec['contact']); ?></td>
                                    <td>
                                        <a href="editseller.php?sellerid=<?= $rec['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteSellerModal<?= $rec['id']; ?>" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Delete Seller Modal -->
                                <div class="modal fade" id="deleteSellerModal<?= $rec['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteSellerModalLabel<?= $rec['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteSellerModalLabel<?= $rec['id']; ?>">Confirm Delete</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this seller?</p>
                                                <p><strong>Seller Name:</strong> <?= htmlspecialchars($rec['username']); ?></p>
                                                <p><strong>Email:</strong> <?= htmlspecialchars($rec['email']); ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <a href="deleteseller.php?sellerid=<?= $rec['id']; ?>" class="btn btn-danger">Delete</a>
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

    <!-- Bootstrap and JavaScript libraries -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
</body>
<?php include("footer.template.php"); ?>
