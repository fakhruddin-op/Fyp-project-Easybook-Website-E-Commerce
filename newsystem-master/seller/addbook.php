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
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $bookname = mysqli_real_escape_string($conn, $_POST['bookname']);
    $bookcodesubject = mysqli_real_escape_string($conn, $_POST['bookcodesubject']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = (int)$_POST['quantity'];

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

    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1 && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO orderbook (price, ownerid, bookname, bookcodesubject, bookcover, description, quantity, approval_status)
                VALUES ('$price', '$userid', '$bookname', '$bookcodesubject', '$newfilename', '$description', '$quantity', 'pending')";
        
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.min.css" rel="stylesheet">

<!-- Clean and Modern Book Insertion Form -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white text-center py-4">
                    <h3 class="mb-0 text-dark fw-bold">
                        <i class="bi bi-book-fill text-primary me-2"></i> Add New Book
                    </h3>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="addbook.php" enctype="multipart/form-data" class="modern-form">
                        
                        <!-- Price and Quantity Fields -->
                        <div class="mb-4">
                            <label for="price" class="form-label fw-semibold">Price (RM)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-currency-dollar"></i></span>
                                <input name="price" type="text" class="form-control" id="price" placeholder="Enter Price (e.g., 12.00)" required>
                            </div>
                            <small id="price-suggestion" class="form-text mt-2 text-muted"></small> <!-- Price suggestion text -->
                        </div>

                    
                        <!-- Book Title and Subject Code -->
                        <div class="mb-4">
                            <label for="bookname" class="form-label fw-semibold">Book Title</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-journal-text"></i></span>
                                <input name="bookname" type="text" class="form-control" id="bookname" placeholder="Enter Book Title" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="bookcodesubject" class="form-label fw-semibold">Book Code Subject</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-code-slash"></i></span>
                                <input name="bookcodesubject" type="text" class="form-control" id="bookcodesubject" placeholder="Enter Subject Code (e.g., MPU3242)" required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control rounded-3" id="description" placeholder="Write a brief description (e.g., condition, notes)" rows="4" required></textarea>
                        </div>

                        <!-- Book Cover Upload -->
                        <div class="mb-4">
                            <label for="fileToUpload" class="form-label fw-semibold">Book Cover (JPG, PNG, GIF)</label>
                            <input name="fileToUpload" type="file" class="form-control" id="fileToUpload" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" name="btn_savebook" class="btn btn-primary btn-lg rounded-pill">
                                <i class="bi bi-save2-fill"></i> Save Book
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<?php
include "footer.template.php";
?>

<!-- JavaScript for Price Suggestion -->
<script>
document.getElementById('price').addEventListener('input', function () {
    const price = parseFloat(this.value);
    const suggestion = document.getElementById('price-suggestion');

    if (!isNaN(price)) {
        if (price < 5) {
            suggestion.textContent = "Suggestion: This price seems too low. Consider adjusting if the book is in readable condition.";
        } else if (price >= 5 && price <= 10) {
            suggestion.textContent = "Suggestion: This is a suitable range for fair-condition books.";
        } else if (price > 10 && price <= 25) {
            suggestion.textContent = "Suggestion: This is a reasonable price range for good-condition books.";
        } else if (price > 25) {
            suggestion.textContent = "Suggestion: This price seems high. Make sure the book has extra value (like being rare or in pristine condition).";
        }
    } else {
        suggestion.textContent = "";
    }
});
</script>
