<?php
session_start();
require 'dbconnect.php';

$idbook = $_GET['idbook'];

// Fetch user's saved addresses
$addressSql = "SELECT * FROM shipping_address WHERE user_id = ?";
$addressStmt = $conn->prepare($addressSql);
$addressStmt->bind_param("i", $_SESSION['id']);
$addressStmt->execute();
$addressResult = $addressStmt->get_result();
$addresses = $addressResult->fetch_all(MYSQLI_ASSOC);
$addressStmt->close();


// Fetch book details including seller name, book name, and price
$sql = "SELECT orderbook.idbook, orderbook.bookname, orderbook.price, user.username AS seller_name 
        FROM orderbook 
        JOIN user ON orderbook.ownerid = user.id 
        WHERE orderbook.idbook = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idbook);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

// Check if book details were found
if (!$book) {
    echo "<p>Book not found.</p>";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Payment - Easy Book</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color: #f2f2f2; font-family: Arial, sans-serif; }
    .payment-container { max-width: 500px; margin: 50px auto; padding: 25px; border-radius: 10px; box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.1); background-color: #fff; }
    .payment-container h2 { font-size: 28px; margin-bottom: 20px; color: #333; text-align: center; }
    .payment-info { font-size: 16px; color: #666; margin-bottom: 20px; }
    .form-group { text-align: left; margin-bottom: 20px; }
    .form-group label { font-weight: bold; color: #555; }
    .form-control { height: 45px; border-radius: 5px; border: 1px solid #ced4da; padding: 10px; font-size: 16px; }
    .btn-confirm { width: 100%; padding: 15px; font-size: 18px; font-weight: bold; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; }
    .btn-confirm:hover { background-color: #0056b3; }
    .payment-methods { display: flex; justify-content: space-around; margin-bottom: 15px; }
    .payment-methods label { display: flex; align-items: center; font-size: 14px; cursor: pointer; }
    .payment-methods input { margin-right: 8px; }
    .icon { width: 25px; height: 25px; margin-right: 10px; }
    .error { color: red; font-size: 13px; }
    .hidden { display: none; }
    .qr-code-img { display: block; margin: 0 auto; width: 150px; height: 150px; cursor: pointer; }

    /* Modal Styles */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); }
    .modal-content { margin: 10% auto; padding: 20px; width: 80%; max-width: 500px; background-color: white; border-radius: 8px; text-align: center; }
    .close { color: #aaa; font-size: 28px; font-weight: bold; position: absolute; top: 10px; right: 25px; cursor: pointer; }
    .close:hover { color: black; }
    .modal img { width: 100%; height: auto; max-width: 400px; }

    /* E-Wallet QR Code Styling */
.qr-code-img { 
    display: block; 
    margin: 0 auto; 
    width: 150px; 
    height: 150px; 
    cursor: pointer; 
    transition: transform 0.2s;
}
.qr-code-img:hover {
    transform: scale(1.05);
}

/* Modal Styling */
.modal { 
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0; 
    top: 0; 
    width: 100%; 
    height: 100%; 
    background-color: rgba(0, 0, 0, 0.7); 
}
.modal-content { 
    margin: 10% auto; 
    padding: 20px; 
    width: 80%; 
    max-width: 400px; 
    background-color: white; 
    border-radius: 8px; 
    text-align: center; 
    position: relative; 
}
.close { 
    color: #aaa; 
    font-size: 28px; 
    font-weight: bold; 
    position: absolute; 
    top: 10px; 
    right: 25px; 
    cursor: pointer; 
}
.close:hover { 
    color: black; 
}
.modal-qr-image { 
    width: 100%; 
    height: auto; 
    max-width: 350px; 
    border-radius: 5px; 
}

    .address-list {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
    }
    .address-card {
        position: relative;
        transition: box-shadow 0.3s, transform 0.2s;
    }
    .address-card:hover .card {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    .custom-control-label.address-label {
        width: 100%;
        margin: 0;
        cursor: pointer;
    }
    .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .card:hover {
        border-color: #007bff;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }
    .text-muted.small {
        font-size: 14px;
    }
</style>


  
  <script>
    function showPaymentFields() {
      const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
      document.getElementById("cardFields").style.display = paymentType === "card" ? "block" : "none";
      document.getElementById("fpxFields").style.display = paymentType === "fpx" ? "block" : "none";
      document.getElementById("eWalletFields").style.display = paymentType === "e_wallet" ? "block" : "none";
      document.getElementById("qrCodeFields").style.display = paymentType === "qr_code" ? "block" : "none";
    }

    function validateForm() {
      const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
      let valid = true;
      document.querySelectorAll('.error').forEach(el => el.innerHTML = '');

      if (paymentType === "fpx") {
        const bankSelect = document.getElementById("bankSelect").value;
        if (!bankSelect) {
          document.getElementById("bankSelectError").innerHTML = "Please select a bank.";
          valid = false;
        } else {
          // Set the action to the selected bank's FPX gateway URL
          const bankUrls = {
            maybank: "https://www.maybank2u.com.my",
            cimb: "https://www.cimbclicks.com.my",
            rhb: "https://logon.rhb.com.my",
            public_bank: "https://www.pbebank.com",
            hong_leong: "https://s.hongleongconnect.my",
            ambank: "https://www.ambank.com.my",
            bank_islam: "https://www.bankislam.biz",
            uob: "https://pib.uob.com.my",
            ocbc: "https://internet.ocbc.com.my",
            hsbc: "https://www.hsbc.com.my"
          };
          document.getElementById("paymentForm").action = bankUrls[bankSelect];
        }
      }

      if (paymentType === "card") {
        const cardNumber = document.getElementById("cardNumber").value;
        const expiryDate = document.getElementById("expiryDate").value;
        const cvv = document.getElementById("cvv").value;
        const cardName = document.getElementById("cardName").value;

        if (!/^\d{16}$/.test(cardNumber)) {
          document.getElementById("cardNumberError").innerHTML = "Please enter a valid 16-digit card number.";
          valid = false;
        }
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiryDate)) {
          document.getElementById("expiryDateError").innerHTML = "Enter expiry date in MM/YY format.";
          valid = false;
        }
        if (!/^\d{3}$/.test(cvv)) {
          document.getElementById("cvvError").innerHTML = "Please enter a valid 3-digit CVV.";
          valid = false;
        }
        if (!/^[A-Za-z\s]+$/.test(cardName)) {
          document.getElementById("cardNameError").innerHTML = "Name on card should contain only letters and spaces.";
          valid = false;
        }
      }
      return valid;
    }

    // Modal Functions
    function openModal() {
      document.getElementById("qrModal").style.display = "block";
    }

    function closeModal() {
      document.getElementById("qrModal").style.display = "none";
    }

    function openEwalletModal() {
    document.getElementById("ewalletModal").style.display = "block";
}

function closeEwalletModal() {
    document.getElementById("ewalletModal").style.display = "none";
}

  </script>
</head>
<body onload="showPaymentFields()">
<div class="container text-center">
    <div class="payment-container">
    <p class="payment-info"><strong>Book Name:</strong> <span><?= htmlspecialchars($book['bookname']) ?></span></p>
        <p class="payment-info"><strong>Price:</strong> <span>RM <?= htmlspecialchars($book['price']) ?></span></p>
        <p class="payment-info"><strong>Shipping Fee:</strong> <span>RM 4.90</span></p>
        <hr>
        <p class="payment-info total"><strong>Total Price:</strong> <span>RM <?= htmlspecialchars(number_format($book['price'] + 4.90, 2)) ?></span></p>

        <form id="paymentForm" action="confirm_payment.php" method="POST" onsubmit="return validateForm()">
            <input type="hidden" name="idbook" value="<?= htmlspecialchars($book['idbook']) ?>">
            <div class="form-group">
        <label for="addressSelection" class="font-weight-bold mb-3">Select Shipping Address:</label>
        <div class="address-list">
            <?php if (!empty($addresses)): ?>
                <?php foreach ($addresses as $address): ?>
                    <div class="custom-control custom-radio mb-3">
                        <input type="radio" id="address<?= $address['id'] ?>" name="selected_address" class="custom-control-input" value="<?= $address['id'] ?>" required>
                        <label class="custom-control-label address-label" for="address<?= $address['id'] ?>">
                            <div class="card border-light shadow-sm p-3">
                                <h6 class="mb-1 font-weight-bold"><?= htmlspecialchars($address['recipient_name']) ?></h6>
                                <p class="mb-1 text-muted small">
                                    <?= htmlspecialchars($address['address']) ?>, 
                                    <?= htmlspecialchars($address['city']) ?>, 
                                    <?= htmlspecialchars($address['state']) ?> - <?= htmlspecialchars($address['postal_code']) ?>
                                </p>
                                <p class="mb-0 text-muted small"><i class="fas fa-phone text-primary"></i> <?= htmlspecialchars($address['phone_number']) ?></p>
                            </div>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No saved addresses found. Please add one in your profile.</p>
                <a href="view_address.php" class="btn btn-primary btn-sm">Add Address</a>
            <?php endif; ?>
        </div>
    </div>
<br>
            <!-- Payment Method Selection -->
            <div class="payment-methods"> 
                <label><input type="radio" name="payment_type" value="card" onclick="showPaymentFields()" checked> <i class="fas fa-credit-card icon"></i>Card</label>
                <label><input type="radio" name="payment_type" value="fpx" onclick="showPaymentFields()"> <i class="fas fa-university icon"></i>FPX</label>
                <label><input type="radio" name="payment_type" value="e_wallet" onclick="showPaymentFields()"> <i class="fas fa-wallet icon"></i>E-Wallet</label>
                <label><input type="radio" name="payment_type" value="qr_code" onclick="showPaymentFields()"> <i class="fas fa-qrcode icon"></i>QR Code</label>
            </div>

            <!-- Card Payment Fields -->
            <div id="cardFields" class="hidden">
                <div class="form-group">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" class="form-control" id="cardNumber" name="card_number" maxlength="16" placeholder="1234 5678 9012 3456">
                    <small id="cardNumberError" class="error"></small>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="expiryDate">Expiry Date</label>
                        <input type="text" class="form-control" id="expiryDate" name="expiry_date" maxlength="5" placeholder="MM/YY">
                        <small id="expiryDateError" class="error"></small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="cvv">CVV</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" maxlength="3" placeholder="123">
                        <small id="cvvError" class="error"></small>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cardName">Name on Card</label>
                    <input type="text" class="form-control" id="cardName" name="card_name" placeholder="John Doe">
                    <small id="cardNameError" class="error"></small>
                </div>
            </div>

            <!-- FPX Payment Fields -->
            <div id="fpxFields" class="hidden">
                <p class="payment-info">Select your bank to proceed with FPX payment:</p>
                <div class="form-group">
                    <label for="bankSelect">Choose Bank</label>
                    <select id="bankSelect" name="bank" class="form-control" onchange="validateCardAndBank()">
                        <option value="">Select Bank</option>
                        <option value="maybank">Maybank</option>
                        <option value="cimb">CIMB Bank</option>
                        <option value="rhb">RHB Bank</option>
                        <option value="public_bank">Public Bank</option>
                        <option value="hong_leong">Hong Leong Bank</option>
                        <option value="ambank">AmBank</option>
                        <option value="bank_islam">Bank Islam</option>
                        <option value="uob">UOB Bank</option>
                        <option value="ocbc">OCBC Bank</option>
                        <option value="hsbc">HSBC Bank</option>
                    </select>
                </div>
            </div>
            

            <form id="paymentForm" action="upload_receipt.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="idbook" value="<?= htmlspecialchars($book['idbook']) ?>">
    <input type="hidden" name="payment_type" id="paymentTypeInput" value="card">

    <!-- QR E-Wallet Payment Fields -->
    <div id="eWalletFields" class="hidden">
        <p class="payment-info">Scan the QR code or click to enlarge. Upload your receipt to proceed with payment.</p>
        <img src="img/qr.ewallet.jpeg" alt="E-Wallet QR Code" class="qr-code-img" onclick="openEwalletModal()">
        <label for="receiptUploadEwallet" class="mt-2">Upload Receipt:</label>
        <input type="file" id="receiptUploadEwallet" name="receipt_ewallet" accept="image/*" onchange="validateQR('ewallet')">
        <small id="ewalletError" class="error"></small>
    </div>

    <!-- QR Bank Payment Fields -->
    <div id="qrCodeFields" class="hidden">
        <p class="payment-info">Click to enlarge and scan the QR code below. Upload your receipt to proceed with payment.</p>
        <img src="img/qr.admin.jpeg" alt="Bank QR Code" class="qr-code-img" onclick="openModal()">
        <label for="receiptUploadQRCode" class="mt-2">Upload Receipt:</label>
        <input type="file" id="receiptUploadQRCode" name="receipt_qrcode" accept="image/*" onchange="validateQR('qrcode')">
        <small id="qrcodeError" class="error"></small>
    </div>

    <!-- QR Bank Payment Fields -->
    <div id="qrCodeFields" class="hidden">
        <p class="payment-info">Click to enlarge and scan the QR code below. Upload your receipt to proceed with payment.</p>
        <img src="img/qr.admin.jpeg" alt="Bank QR Code" class="qr-code-img" onclick="openModal()">
        <br>
        <label for="receiptUploadQRCode">Upload Receipt:</label>
        <input type="file" id="receiptUploadQRCode" name="receipt_qrcode" accept="image/*" onchange="validateQR('qrcode')">
        <small id="qrcodeError" class="error"></small>
    </div>

    <!-- Finalize Payment Buttons -->
    <div class="form-group">
        <button id="finalizeCardBankButton" class="btn-confirm hidden" type="submit" onclick="finalizeCardOrBank()">Confirm Card/Bank Payment</button>
        <button id="finalizeQRButton" class="btn-confirm hidden" type="submit" disabled onclick="finalizeQRPayment()">Confrim QR Payment</button>
    </div>
</form>


<script>
    // Show fields based on selected payment type
    function showPaymentFields() {
        const selectedPaymentType = document.querySelector('input[name="payment_type"]:checked').value;
        const cardBankButton = document.getElementById('finalizeCardBankButton');
        const qrButton = document.getElementById('finalizeQRButton');

        document.getElementById('cardFields').classList.add('hidden');
        document.getElementById('fpxFields').classList.add('hidden');
        document.getElementById('eWalletFields').classList.add('hidden');
        document.getElementById('qrCodeFields').classList.add('hidden');
        cardBankButton.classList.add('hidden');
        qrButton.classList.add('hidden');

        if (selectedPaymentType === 'card') {
            document.getElementById('cardFields').classList.remove('hidden');
            cardBankButton.classList.remove('hidden');
        } else if (selectedPaymentType === 'fpx') {
            document.getElementById('fpxFields').classList.remove('hidden');
            cardBankButton.classList.remove('hidden');
        } else if (selectedPaymentType === 'e_wallet') {
            document.getElementById('eWalletFields').classList.remove('hidden');
            qrButton.classList.remove('hidden');
        } else if (selectedPaymentType === 'qr_code') {
            document.getElementById('qrCodeFields').classList.remove('hidden');
            qrButton.classList.remove('hidden');
        }
    }

    function validateQR(type) {
    const finalizeQRButton = document.getElementById('finalizeQRButton');
    const ewalletUploaded = document.getElementById('receiptUploadEwallet').files.length > 0;
    const qrcodeUploaded = document.getElementById('receiptUploadQRCode').files.length > 0;

    // Enable the button if the correct file is uploaded
    if ((type === 'ewallet' && ewalletUploaded) || (type === 'qrcode' && qrcodeUploaded)) {
        finalizeQRButton.disabled = false;
    } else {
        finalizeQRButton.disabled = true;
    }
}


    // Placeholder for finalization logic
    function finalizeCardOrBank() {
        alert('Card or FPX payment finalized.');
    }

    function finalizeQRPayment() {
        alert('QR payment finalized.');
    }
</script>

<style>
    .hidden {
        display: none;
    }

    .btn-confirm {
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-confirm:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
</style>



<!-- Modal for Enlarged QR Code -->
<div id="qrModal" class="modal" onclick="closeModal()">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <img src="img/qr.admin.jpeg" alt="QR Code">
  </div>
</div>
<!-- Modal for Enlarged E-Wallet QR Code -->
<div id="ewalletModal" class="modal" onclick="closeEwalletModal()">
  <div class="modal-content">
    <span class="close" onclick="closeEwalletModal()">&times;</span>
    <img src="img/qr.ewallet.jpeg" alt="E-Wallet QR Code" class="modal-qr-image">
  </div>
</div>

</body>
</html>
