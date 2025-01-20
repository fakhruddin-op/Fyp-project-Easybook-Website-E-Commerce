<?php
session_start();
require 'dbconnect.php';

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id']; // Logged-in user ID



// Fetch user details (name and phone number)
$userSql = "SELECT username, contact FROM user WHERE id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
$userStmt->close();

// Handle new address submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $recipient_name = mysqli_real_escape_string($conn, $_POST['recipient_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

    $sql = "INSERT INTO shipping_address (user_id, recipient_name, address, city, state, postal_code, phone_number) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $userId, $recipient_name, $address, $city, $state, $postal_code, $phone_number);

    if ($stmt->execute()) {
        $success_message = "Address added successfully!";
    } else {
        $error_message = "Failed to add address. Please try again.";
    }
    $stmt->close();
}

// Handle address editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_address'])) {
    $edit_id = intval($_POST['edit_id']);
    $recipient_name = mysqli_real_escape_string($conn, $_POST['recipient_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

    $updateSql = "UPDATE shipping_address 
                  SET recipient_name = ?, address = ?, city = ?, state = ?, postal_code = ?, phone_number = ? 
                  WHERE id = ? AND user_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssssssii", $recipient_name, $address, $city, $state, $postal_code, $phone_number, $edit_id, $userId);

    if ($updateStmt->execute()) {
        $success_message = "Address updated successfully!";
    } else {
        $error_message = "Failed to update address. Please try again.";
    }

    $updateStmt->close();
}

// Handle address deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $deleteSql = "DELETE FROM shipping_address WHERE id = ? AND user_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("ii", $delete_id, $userId);

    if ($deleteStmt->execute()) {
        $success_message = "Address deleted successfully!";
    } else {
        $error_message = "Failed to delete address.";
    }

    $deleteStmt->close();
}

// Fetch all addresses for the user
$sql = "SELECT * FROM shipping_address WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$addresses = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Address - Easy Book</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px;
        }
        .address-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .address-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            border-radius: 5px;
            padding: 10px;
        }
        .btn-submit {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .btn-action {
            margin-right: 10px;
        }
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-back:hover {
            background-color:rgb(9, 115, 228);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="address-container">
            <!-- Back to Profile Button -->
            <a href="profile.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Profile</a>
            <h2 class="text-primary mb-4 text-center">My Addresses</h2>

            <!-- Display success or error messages -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

           
            <!-- Add Address Form -->
            <h4 class="mb-3 text-primary">Add a New Address</h4>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="recipient_name">Recipient Name</label>
                    <input type="text" id="recipient_name" name="recipient_name" class="form-control" 
                           value="<?= htmlspecialchars($user['username']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="Enter full address" required>
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control" placeholder="Enter city" required>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" class="form-control" placeholder="Enter state" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="postal_code">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="Enter postal code" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" class="form-control" 
                           value="<?= htmlspecialchars($user['contact']) ?>" readonly>
                </div>
                <button type="submit" name="add_address" class="btn-submit">Add Address</button>
            </form>

            <!-- Edit Address Form -->
            <div class="address-container mt-4" id="editAddressForm" style="display: none;">
                <h4 class="mb-3 text-primary">Edit Address</h4>
                <form action="" method="POST">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="form-group">
                        <label for="recipient_name">Recipient Name</label>
                        <input type="text" id="edit_recipient_name" name="recipient_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="edit_address" name="address" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="edit_city" name="city" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="state">State</label>
                            <input type="text" id="edit_state" name="state" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="edit_postal_code" name="postal_code" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" id="edit_phone_number" name="phone_number" class="form-control" required>
                    </div>
                    <button type="submit" name="edit_address" class="btn-submit">Save Changes</button> 
                </form>
                
            </div>
            <br>
             <!-- Display Existing Addresses -->
             <?php if (!empty($addresses)): ?>
                <?php foreach ($addresses as $address): ?>
                    <div class="address-card">
                        <h5><i class="fas fa-user text-primary"></i> <?= htmlspecialchars($address['recipient_name']) ?></h5>
                        <p><i class="fas fa-map-marker-alt text-primary"></i> <?= htmlspecialchars($address['address']) ?>, <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state']) ?> - <?= htmlspecialchars($address['postal_code']) ?></p>
                        <p><i class="fas fa-phone text-primary"></i> <?= htmlspecialchars($address['phone_number']) ?></p>
                        <a href="?delete_id=<?= $address['id'] ?>" class="btn btn-danger btn-sm btn-action" onclick="return confirm('Are you sure you want to delete this address?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                        <button type="button" class="btn btn-warning btn-sm btn-action" onclick="openEditForm(<?= htmlspecialchars(json_encode($address)) ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted text-center">No saved addresses found.</p>
            <?php endif; ?>

        </div>
        
    </div>
    

    <script>
        function openEditForm(address) {
            document.getElementById('editAddressForm').style.display = 'block';
            document.getElementById('edit_id').value = address.id;
            document.getElementById('edit_recipient_name').value = address.recipient_name;
            document.getElementById('edit_address').value = address.address;
            document.getElementById('edit_city').value = address.city;
            document.getElementById('edit_state').value = address.state;
            document.getElementById('edit_postal_code').value = address.postal_code;
            document.getElementById('edit_phone_number').value = address.phone_number;
        }
    </script>
</body>
</html>
