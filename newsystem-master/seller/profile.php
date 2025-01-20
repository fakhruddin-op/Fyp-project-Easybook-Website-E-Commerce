<?php
session_start();
require '../dbconnect.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

// Fetch user data from the database
$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
include "header.template.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        /* Profile Page Styling */
        .profile-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
        }
        .profile-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .profile-header {
            background-color: #4e73df;
            color: #fff;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        .profile-header h2 {
            font-size: 1.8em;
            font-weight: 700;
            margin: 0;
        }
        .profile-icon {
            font-size: 60px;
            color: #fff;
            margin-bottom: 10px;
        }
        .profile-body {
            padding: 20px;
        }
        .profile-body .form-group {
            margin-bottom: 15px;
        }
        .profile-body label {
            font-weight: bold;
            color: #4e73df;
        }
        .profile-body .form-control-plaintext {
            color: #333;
        }
        .profile-actions {
            padding: 20px;
            text-align: center;
            border-top: 1px solid #f1f1f1;
        }
        .btn-edit-profile {
            background-color: #4e73df;
            border: none;
            color: #fff;
            font-size: 1.1em;
            font-weight: 500;
            padding: 10px 30px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-edit-profile:hover {
            background-color: #3751c7;
        }
    </style>
</head>
<body>
<div class="container profile-container">
    <div class="card profile-card">
        <div class="profile-header">
            <i class="fas fa-user-circle profile-icon"></i>
            <h2>User Profile</h2>
        </div>
        <div class="profile-body">
            <form>
                <div class="form-group">
                    <label>Username</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($user['username']) ?></p>
                </div>
                <div class="form-group">
                    <label>Contact</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($user['contact']) ?></p>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <p class="form-control-plaintext"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </form>
        </div>
        <div class="profile-actions">
            <a href="editprofile.php" class="btn btn-edit-profile"><i class="fas fa-edit"></i> Edit Profile</a>
        </div>
    </div>
</div>
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<?php
include "footer.template.php";
?>
</body>
</html>
