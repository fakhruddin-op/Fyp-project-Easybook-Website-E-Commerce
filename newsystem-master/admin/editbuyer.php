<?php
session_start();
if ($_SESSION['accesslevel'] != 'admin') {
    header('location: login.php');
    exit();
}
require '../dbconnect.php';

if (isset($_POST['update'])) {
    $buyerId = $_POST['buyerid'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    $sql = "UPDATE user SET username = ?, email = ?, contact = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $contact, $buyerId);
    if ($stmt->execute()) {
        header("Location: managebuyers.php?success=updated");
    } else {
        echo "Error: " . $stmt->error;
    }
    exit();
}

$buyerId = $_GET['buyerid'];
$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $buyerId);
$stmt->execute();
$result = $stmt->get_result();
$buyer = $result->fetch_assoc();
include("header.template.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Buyer</title>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="col-lg-6 mx-auto">
        <div class="card shadow-lg">
            <div class="card-header text-center bg-primary text-white">
                <h4 class="mb-0">Edit Buyer Information</h4>
            </div>
            <div class="card-body">
                <form action="editbuyer.php" method="post">
                    <input type="hidden" name="buyerid" value="<?= htmlspecialchars($buyer['id']) ?>">
                    
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($buyer['username']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($buyer['email']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" class="form-control" id="contact" name="contact" 
                               value="<?= htmlspecialchars($buyer['contact']) ?>" required>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg px-5" name="update">Update</button>
                        <button type="button" class="btn btn-secondary btn-lg px-5" 
                                onclick="window.location.href='managebuyers.php'">Back</button>
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
