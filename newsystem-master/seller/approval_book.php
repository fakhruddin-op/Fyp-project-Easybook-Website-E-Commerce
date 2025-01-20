<?php
session_start();
if ($_SESSION['accesslevel'] != 'seller') {
    header('location: ../login.php');
    exit();
}

include "../dbconnect.php";

$userid = $_SESSION['id'];

// Query to get approved books with purchase status
$sql = "SELECT price, ownerid, bookname, bookcodesubject, bookcover, quantity, description, is_purchased 
        FROM orderbook 
        WHERE ownerid='$userid' AND approval_status='approved'";
$result = mysqli_query($conn, $sql);

include "header.template.php";
?>

<div class="container mt-5">
    <h2 class="font-weight-bold text-primary text-center mb-4">Book Listings</h2>
    <div class="row">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-img-top" style="height: 250px; overflow: hidden;">
                            <img src="bookcover/<?php echo htmlspecialchars($row['bookcover']); ?>" alt="<?php echo htmlspecialchars($row['bookname']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold"><?php echo htmlspecialchars($row['bookname']); ?></h5>
                            <p class="card-text"><strong>Price:</strong> RM <?php echo number_format((float)$row['price'], 2); ?></p>
                            <p class="card-text"><strong>Book Code:</strong> <?php echo htmlspecialchars($row['bookcodesubject']); ?></p>
                            <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-text">
                                <span class="badge badge-<?php echo ($row['is_purchased'] == 1) ? 'danger' : 'success'; ?>">
                                    <?php echo ($row['is_purchased'] == 1) ? 'Purchased' : 'Available'; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<div class='col-12'><p class='text-center text-muted'>No approved books found.</p></div>";
        }
        ?>
    </div>
</div>

<?php
include "footer.template.php";
?>
