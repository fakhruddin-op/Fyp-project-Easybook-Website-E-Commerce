<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: ../login.php');
    exit();
}

include "../dbconnect.php";

// Fetch all pending books with seller details
$sql = "
    SELECT 
        orderbook.*, 
        user.username AS seller_name 
    FROM 
        orderbook 
    JOIN 
        user 
    ON 
        orderbook.ownerid = user.id 
    WHERE 
        orderbook.approval_status = 'pending'
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

include "header.template.php";
?>

<div class="container mt-5">
    <h2 class="font-weight-bold text-primary text-center mb-4">Pending Book Approvals</h2>
    <div class="row">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-img-top" style="height: 200px; overflow: hidden;">
                            <img src="../seller/bookcover/<?php echo htmlspecialchars($row['bookcover']); ?>" alt="<?php echo htmlspecialchars($row['bookname']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold"><?php echo htmlspecialchars($row['bookname']); ?></h5>
                            <p class="card-text"><strong>Seller Name:</strong> <?php echo htmlspecialchars($row['seller_name']); ?></p>
                            <p class="card-text"><strong>Price:</strong> RM <?php echo number_format((float)$row['price'], 2); ?></p>
                            <p class="card-text"><strong>Book Code:</strong> <?php echo htmlspecialchars($row['bookcodesubject']); ?></p>
                            <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                            
                        </div>
                        <div class="card-footer text-center">
                            <a href="approvebook.php?id=<?php echo $row['idbook']; ?>&status=approved" class="btn btn-success btn-sm">Approve</a>
                            <a href="approvebook.php?id=<?php echo $row['idbook']; ?>&status=rejected" class="btn btn-danger btn-sm">Reject</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12'><p class='text-center text-muted'>No books pending approval.</p></div>";
        }
        ?>
    </div>
</div>

<?php
include "footer.template.php";
?>
