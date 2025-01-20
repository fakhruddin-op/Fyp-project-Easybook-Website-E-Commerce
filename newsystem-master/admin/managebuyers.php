<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: login.php');
    exit();
}
require '../dbconnect.php';

// Fetch buyer data from the database
$sql = "SELECT * FROM user WHERE accesslevel = 'buyer'";
$rs = mysqli_query($conn, $sql);
if (mysqli_error($conn)) {
    echo 'Error: ' . mysqli_error($conn);
    exit();
}

// Count total buyers
$total_buyers_query = "SELECT COUNT(*) AS total_buyers FROM user WHERE accesslevel = 'buyer'";
$total_buyers_result = mysqli_query($conn, $total_buyers_query);
$total_buyers = mysqli_fetch_assoc($total_buyers_result)['total_buyers'];

include("header.template.php");
?>

<body>
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 text-gray-800">Manage Buyers</h1>
        </div>

        <!-- Total Buyers Card -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Buyers
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_buyers ?></div>
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
                <?= ($_GET['success'] == 'deleted') ? 'Successfully deleted the buyer.' : 'Successfully updated the buyer.'; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Buyers Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Buyers Information</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rec = mysqli_fetch_array($rs)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rec['username']) ?></td>
                                    <td><?= htmlspecialchars($rec['email']) ?></td>
                                    <td><?= htmlspecialchars($rec['contact']) ?></td>
                                    <td>
                                        <a href="editbuyer.php?buyerid=<?= $rec['id'] ?>" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteBuyerModal<?= $rec['id'] ?>" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Delete Buyer Modal -->
                                <div class="modal fade" id="deleteBuyerModal<?= $rec['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="deleteBuyerModalLabel<?= $rec['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteBuyerModalLabel<?= $rec['id'] ?>">Confirm Delete</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this buyer?</p>
                                                <p><strong>Username:</strong> <?= htmlspecialchars($rec['username']) ?></p>
                                                <p><strong>Email:</strong> <?= htmlspecialchars($rec['email']) ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <a href="deletebuyer.php?buyerid=<?= $rec['id'] ?>" class="btn btn-danger">Delete</a>
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

    <!-- JavaScript Files -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
</body>
<?php include("footer.template.php"); ?>
