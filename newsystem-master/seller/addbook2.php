<?php
session_start();

// Ensure the user is a seller
if ($_SESSION['accesslevel'] != 'seller') {
    header('Location: ../login.php');
    exit();
}

// Include database connection
include "../dbconnect.php";

if (isset($_POST['btn_savebook'])) {
    // Get form data
    $userid = $_SESSION['id'];
    $bookname = mysqli_real_escape_string($conn, $_POST['bookname']);
    $bookcodesubject = mysqli_real_escape_string($conn, $_POST['bookcodesubject']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $condition = intval($_POST['condition']);

    // Backend Price Calculation based on Book Condition
    if ($condition >= 1 && $condition <= 3) {
        $price = 5.00; // Low-quality book price
    } elseif ($condition >= 4 && $condition <= 7) {
        $price = 15.00; // Medium-quality book price
    } elseif ($condition >= 8 && $condition <= 10) {
        $price = 25.00; // High-quality book price
    } else {
        echo "Invalid condition rating.";
        exit();
    }

    // Handle file upload
    $target_dir = "bookcover/";
    $newfilename = date('U') . basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $newfilename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate the uploaded file
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Restrict file types to JPG, JPEG, PNG, and GIF
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Upload the file if all conditions are met
    if ($uploadOk == 1 && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Insert book details into the database
        $sql = "INSERT INTO orderbook (price, ownerid, bookname, bookcodesubject, bookcover, description, condition, approval_status)
                VALUES ('$price', '$userid', '$bookname', '$bookcodesubject', '$newfilename', '$description', '$condition', 'pending')";
        
        if (mysqli_query($conn, $sql)) {
            header('Location: listing.php?success=saved');
            exit();
        } else {
            echo "Error saving record: " . mysqli_error($conn);
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

include "header.template.php";
?>

<div class="card o-hidden border-0 shadow-lg my-1">
    <div class="card-header py-3">
        <h5 class="m-0 font-weight-bold text-primary">Add New Book</h5>
    </div>
    <div class="card-body p-0">
        <div class="p-4">
            <!-- Book insertion form -->
            <form class="user" method="post" action="addbook.php" enctype="multipart/form-data">
                
                <!-- Book Name and Code Subject inputs -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="bookname">Book Title</label>
                        <input name="bookname" type="text" class="form-control" id="bookname" placeholder="Book Title" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="bookcodesubject">Book Code Subject</label>
                        <input name="bookcodesubject" type="text" class="form-control" id="bookcodesubject" placeholder="Book Code Subject" required>
                    </div>
                </div>

                <!-- Condition Rating -->
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="condition">Book Condition <span class="text-muted">(1 - Poor, 10 - Excellent)</span></label>
                        <input name="condition" type="number" class="form-control" id="condition" min="1" max="10" placeholder="Rate 1-10" required>
                    </div>
                </div>

                <!-- Price input (Auto-filled based on condition) -->
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="price">Price <span class="text-muted">(Auto-calculated based on condition)</span></label>
                        <input name="price" type="text" class="form-control" id="price" placeholder="Price will auto-adjust" readonly required>
                    </div>
                </div>

                <!-- Description input -->
                <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" id="description" placeholder="Description" required></textarea>
                    </div>
                </div>

                <!-- Cover Page Upload -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fileToUpload">Cover Page Book</label>
                        <input name="fileToUpload" type="file" class="form-control-file" required>
                    </div>
                </div>

                <!-- Submit button -->
                <hr>
                <div align="right">
                    <button type="submit" name="btn_savebook" class="btn btn-success">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include "footer.template.php";
?>

<script>
// Automatically adjust price based on condition
document.getElementById('condition').addEventListener('input', function () {
    const condition = parseInt(this.value, 10);
    const priceInput = document.getElementById('price');

    if (!isNaN(condition)) {
        // Define price ranges based on condition
        if (condition >= 1 && condition <= 3) {
            priceInput.value = "5.00"; // Low-quality book price
        } else if (condition >= 4 && condition <= 7) {
            priceInput.value = "15.00"; // Medium-quality book price
        } else if (condition >= 8 && condition <= 10) {
            priceInput.value = "25.00"; // High-quality book price
        } else {
            priceInput.value = ""; // Clear price if condition is out of range
        }
    } else {
        priceInput.value = ""; // Clear price if no valid condition
    }
});
</script>
