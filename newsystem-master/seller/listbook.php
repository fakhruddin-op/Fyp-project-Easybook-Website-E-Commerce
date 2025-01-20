<?php
session_start();
if ($_SESSION['accesslevel'] != 'seller') {
    header('location: ../login.php');
}

include "../dbconnect.php";

$userid = $_SESSION['id'];

$sql = "SELECT price, ownerid, bookname, bookcodesubject, bookcover, description FROM orderbook WHERE ownerid='$userid'";
$result = mysqli_query($conn, $sql);

include "header.template.php";
?>

<div class="container mt-5">
    <h2 class="font-weight-bold text-primary">Book Listings</h2>
    <div class="row">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="bookcover/<?php echo $row['bookcover']; ?>" class="card-img-top" alt="<?php echo $row['bookname']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['bookname']; ?></h5>
                            <p class="card-text">Price: <?php echo $row['price']; ?></p>
                            <p class="card-text">Book Code Subject: <?php echo $row['bookcodesubject']; ?></p>
                            <p class="card-text">Description: <?php echo $row['description']; ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No books found.</p>";
        }
        ?>
    </div>
</div>

<?php
include "footer.template.php";
?>
