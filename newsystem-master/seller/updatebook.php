<?php
session_start();
if ($_SESSION['accesslevel'] != 'seller') {
    header('location: ../login.php');
    exit();
}

include "../dbconnect.php";

if (isset($_POST['btn_updatebook'])) {
    $bookid = $_POST['idbook'];
    $price = $_POST['price'];
    $bookname = $_POST['bookname'];
    $bookcodesubject = $_POST['bookcodesubject'];
    $description = $_POST['description'];

    $sql = "UPDATE orderbook 
            SET price = '$price', bookname = '$bookname', bookcodesubject = '$bookcodesubject', description = '$description'
            WHERE idbook = '$bookid'";

    if (mysqli_query($conn, $sql)) {
        header('Location: listing.php?success=saved');
        exit();
    } else {
        echo "Failed to update book information. Error: " . mysqli_error($conn);
        exit();
    }
}

if (isset($_GET['idbook'])) {
    $idbook = $_GET['idbook'];
    $qr = mysqli_query($conn, "SELECT * FROM orderbook WHERE idbook = $idbook");
    if ($qr) {
        $rec = mysqli_fetch_array($qr);
        $price = $rec['price'];
        $bookname = $rec['bookname'];
        $bookcodesubject = $rec['bookcodesubject'];
        $description = $rec['description'];
    } else {
        echo "Failed to get book information from DB. Error: " . mysqli_error($conn);
        exit();
    }
} else {
    header('Location: listing.php');
    exit();
}

include "header.template.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0 font-weight-bold">Update Book Information</h5>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="updatebook.php">
                        <!-- Hidden book ID field -->
                        <input type="hidden" name="idbook" value="<?= $idbook ?>">
                        
                        <!-- Price field -->
                        <div class="form-group">
                            <label for="price" class="font-weight-bold">Price</label>
                            <input name="price" type="number" step="0.01" class="form-control" id="price" placeholder="Enter price" value="<?= $price ?>" required>
                        </div>

                        <!-- Book Title field -->
                        <div class="form-group">
                            <label for="bookname" class="font-weight-bold">Book Title</label>
                            <input name="bookname" type="text" class="form-control" id="bookname" placeholder="Enter book title" value="<?= $bookname ?>" required>
                        </div>

                        <!-- Book Code Subject field -->
                        <div class="form-group">
                            <label for="bookcodesubject" class="font-weight-bold">Book Code Subject</label>
                            <input name="bookcodesubject" type="text" class="form-control" id="bookcodesubject" placeholder="Enter book code subject" value="<?= $bookcodesubject ?>" required>
                        </div>

                        <!-- Description field -->
                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Description</label>
                            <textarea name="description" class="form-control" id="description" rows="4" placeholder="Enter description" required><?= $description ?></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" name="btn_updatebook" class="btn btn-success mr-2">Save Update</button>
                            <a href="listing.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "footer.template.php";
?>
