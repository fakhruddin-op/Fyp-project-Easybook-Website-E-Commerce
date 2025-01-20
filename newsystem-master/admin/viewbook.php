<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['accesslevel']) || $_SESSION['accesslevel'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Check if bookid is provided
if (!isset($_GET['bookid']) || empty($_GET['bookid'])) {
    echo "Invalid book ID.";
    exit();
}

$bookid = $_GET['bookid'];
require "../dbconnect.php";

// Fetch book and user details
$sql = "SELECT o.*, u.username 
        FROM orderbook AS o
        JOIN user AS u ON o.ownerid = u.id 
        WHERE o.idbook = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookid);
$stmt->execute();
$result = $stmt->get_result();

// Check if record is available
if ($result->num_rows === 0) {
    echo "Book not found.";
    exit();
}

$rec = $result->fetch_assoc();
include "header.template.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Book Details</title>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 15px 15px 0 0;
        }
        .card img {
            max-height: 350px;
            border-radius: 10px;
        }
        .btn-secondary {
            font-size: 1rem;
            font-weight: 500;
        }
        .list-group-item {
            font-size: 1rem;
            font-weight: 500;
            padding: 15px 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header text-center bg-primary text-white py-4">
                <h3 class="mb-0">📚 Book Information</h3>
            </div>
            <div class="card-body px-5 py-4">
                <?php
                // Construct the path to the image file
                $coverPath = "../seller/bookcover/" . htmlspecialchars($rec['bookcover']);
                ?>
                <div class="text-center mb-4">
                    <?php if (!empty($rec['bookcover']) && file_exists($coverPath)): ?>
                        <img src="<?= $coverPath ?>" class="img-fluid shadow-sm" alt="Book Cover">
                    <?php else: ?>
                        <p class="text-muted">No cover image available</p>
                    <?php endif; ?>
                </div>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Book Title:</strong>
                        <span class="text-secondary"><?= htmlspecialchars($rec['bookname']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Seller Name:</strong>
                        <span class="text-secondary"><?= htmlspecialchars($rec['username']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Price:</strong>
                        <span class="text-success, background-color">RM<?= htmlspecialchars(number_format($rec['price'], 2)) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Code Book:</strong>
                        <span class="text-secondary"><?= htmlspecialchars($rec['bookcodesubject']) ?></span>
                    </li>
                    <li class="list-group-item">
                        <strong>Description:</strong>
                        <p class="text-secondary mt-2 mb-0" style="line-height: 1.5;">
                            <?= nl2br(htmlspecialchars($rec['description'])) ?>
                        </p>
                    </li>
                </ul>

                <div class="text-center">
                    <a href="book_details.php" class="btn btn-secondary px-5 py-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<?php include "footer.template.php"; ?>
</body>
</html>
