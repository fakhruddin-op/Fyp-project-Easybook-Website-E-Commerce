<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: login.php');
    exit();
}
require '../dbconnect.php';

if (isset($_POST['update'])) {
    $sellerId = $_POST['sellerid'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    $sql = "UPDATE user SET username = ?, email = ?, contact = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $contact, $sellerId);
    if ($stmt->execute()) {
        header("Location: manageseller.php?success=updated");
    } else {
        echo "Error: " . $stmt->error;
    }
    exit();
}

$sellerId = $_GET['sellerid'];
$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sellerId);
$stmt->execute();
$result = $stmt->get_result();
$seller = $result->fetch_assoc();
include("header.template.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Seller</title>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-lg">
            <div class="card-header text-center bg-primary text-white">
                <h4 class="mb-0">Edit Seller</h4>
            </div>
            <div class="card-body">
                <form action="editseller.php" method="post">
                    <input type="hidden" name="sellerid" value="<?= $seller['id'] ?>">
                    
                    <div class="form-group">
                        <label for="username">Seller Name:</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($seller['username']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($seller['email']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" class="form-control" id="contact" name="contact" 
                               value="<?= htmlspecialchars($seller['contact']) ?>" required>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5" name="update">Update</button>
                        <button type="button" class="btn btn-secondary btn-lg px-5" 
                                onclick="window.location.href='manageseller.php'">Back</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<?php include("footer.template.php"); ?>
</body>
</html>
