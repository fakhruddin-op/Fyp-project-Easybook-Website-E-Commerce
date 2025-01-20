<?php
session_start();
require 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: select_payment.php");
    exit;
}

$buyer_id = $_POST['buyer_id'];
$total_price = $_POST['total_price'];
$payment_method = $_POST['payment_method'];

// Fetch saved addresses for the buyer
$sql = "SELECT * FROM shipping_address WHERE user_id = '$buyer_id'";
$result = mysqli_query($conn, $sql);
if (!$result) {
    error_log("Error fetching addresses: " . mysqli_error($conn));
    die("Error fetching addresses.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Enter Payment Details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f6fa;
            font-family: 'Roboto', sans-serif;
        }

        .payment-details-container {
            margin-top: 50px;
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .payment-details-container h2 {
            text-align: center;
            color: #212529;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background-color: #f8f9fa;
            transition: all 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #007bff;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
            outline: none;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            font-size: 1rem;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .qr-container img {
            width: 150px;
            height: 150px;
            display: block;
            margin: 0 auto;
            cursor: pointer;
        }

        .qr-container p {
            text-align: center;
            font-weight: bold;
            color: #495057;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            margin: 15% auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content img {
            width: 100%;
            max-width: 300px;
            margin: auto;
        }

        .modal-close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-close:hover {
            color: black;
        }

        @media (max-width: 768px) {
            .payment-details-container {
                padding: 20px;
            }

            .btn-submit {
                font-size: 0.9rem;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
<div class="container payment-details-container">
    <h2>Payment & Delivery</h2>
    <form action="process_payment.php" method="POST">
        <input type="hidden" name="buyer_id" value="<?= $buyer_id ?>">
        <input type="hidden" name="total_price" value="<?= $total_price ?>">
        <input type="hidden" name="payment_method" value="<?= $payment_method ?>">

        <div class="form-group">
            <label for="address">Select Delivery Address</label>
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="address-box">
                        <input type="radio" id="address_<?= $row['id'] ?>" name="address_id" value="<?= $row['id'] ?>" required>
                        <label for="address_<?= $row['id'] ?>">
                            <strong><?= htmlspecialchars($row['recipient_name']) ?></strong><br>
                            <?= htmlspecialchars($row['address']) ?>, <?= htmlspecialchars($row['city']) ?>,<br>
                            <?= htmlspecialchars($row['state']) ?>, <?= htmlspecialchars($row['postal_code']) ?><br>
                            <span style="color: #495057;">Phone:</span> <?= htmlspecialchars($row['phone_number']) ?>
                        </label>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>No saved addresses. Please <a href="add_address.php" style="color: #007bff;">add an address</a>.</p>
            <?php } ?>
        </div>

        <?php if ($payment_method === 'fpx') { ?>
            <div class="form-group">
                <label for="bank">Bank</label>
                <select id="bank" name="bank" class="form-control" required>
                    <option value="">Select your bank</option>
                    <option value="maybank">Maybank</option>
                    <option value="cimb">CIMB Bank</option>
                    <option value="public">Public Bank</option>
                    <option value="rhb">RHB Bank</option>
                    <option value="hongleong">Hong Leong Bank</option>
                    <option value="ambank">AmBank</option>
                    <option value="affin">Affin Bank</option>
                    <option value="bsn">Bank Simpanan Nasional</option>
                </select>
            </div>
        <?php } elseif ($payment_method === 'qr') { ?>
            <div class="form-group qr-container">
                <p>Click the QR code to enlarge:</p>
                <img src="img/qr.ewallet.jpeg" alt="QR Code" id="qr-code">
            </div>
        <?php } ?>

        <button type="submit" class="btn-submit">Submit Payment</button>
    </form>
</div>

<div id="qrModal" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <img src="img/qr.ewallet.jpeg" alt="QR Code">
    </div>
</div>

<script>
    const qrCode = document.getElementById('qr-code');
    const qrModal = document.getElementById('qrModal');
    const modalClose = document.querySelector('.modal-close');

    qrCode.addEventListener('click', () => {
        qrModal.style.display = 'block';
    });

    modalClose.addEventListener('click', () => {
        qrModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === qrModal) {
            qrModal.style.display = 'none';
        }
    });
</script>
</body>
</html>
