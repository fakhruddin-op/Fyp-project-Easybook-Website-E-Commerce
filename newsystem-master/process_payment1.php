<?php
session_start();
require 'dbconnect.php';

// Handle data from the cart page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['selected_books']) && count($_POST['selected_books']) > 0) {
        $selectedBooks = $_POST['selected_books'];
        $quantities = $_POST['quantities'];

        // Prepare for fetching book details
        $bookIds = implode(',', array_map('intval', $selectedBooks));
        $sql = "SELECT * FROM orderbook WHERE idbook IN ($bookIds)";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("Error: " . mysqli_error($conn));
        }

        $books = [];
        $totalPrice = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $bookId = $row['idbook'];
            $row['quantity'] = $quantities[$bookId] ?? 1;
            $books[] = $row;
            $totalPrice += $row['price'] * $row['quantity'];
        }
    } else {
        die("No books selected. <a href='my_cart.php'>Go back</a>");
    }
} else {
    die("Invalid request. <a href='my_cart.php'>Go back</a>");
}

// Fetch saved addresses for the user
$userId = $_SESSION['id'];
$addressQuery = "SELECT * FROM shipping_address WHERE user_id = '$userId'";
$addressResult = mysqli_query($conn, $addressQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Process</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .payment-section {
      margin-bottom: 20px;
    }
    .payment-card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .btn-primary-custom {
      background-color: #023047;
      color: white;
      border: none;
    }
    .btn-primary-custom:hover {
      background-color: #005f73;
    }
    .qr-code {
      max-width: 200px;
      margin: auto;
      display: block;
    }
    .qr-instructions {
      font-size: 0.9rem;
      color: #6c757d;
      text-align: center;
      margin-top: 10px;
    }
    .form-check-label {
      font-weight: bold;
    }
    @media (max-width: 768px) {
      .order-summary {
        margin-top: 20px;
      }
    }
    
  </style>
</head>
<body>
<div class="container my-5">
  <h2 class="text-center text-primary mb-4">Payment Process</h2>

  <form action="submit_payment.php" method="POST">
    <!-- Hidden Fields for Selected Books -->
    <?php foreach ($books as $book): ?>
      <input type="hidden" name="selected_books[]" value="<?= $book['idbook'] ?>">
      <input type="hidden" name="quantities[<?= $book['idbook'] ?>]" value="<?= $book['quantity'] ?>">
    <?php endforeach; ?>
    <input type="hidden" name="total_price" value="<?= $totalPrice ?>">

    <div class="row">
      <!-- Shipping Address Section -->
      <div class="col-lg-8 payment-section">
        <div class="card payment-card p-4">
          <h5>Select Shipping Address</h5>
          <?php if (mysqli_num_rows($addressResult) > 0): ?>
            <?php while ($address = mysqli_fetch_assoc($addressResult)): ?>
              <div class="form-check mt-2">
                <input class="form-check-input" type="radio" name="address_id" id="address<?= $address['id'] ?>" value="<?= $address['id'] ?>" required>
                <label class="form-check-label" for="address<?= $address['id'] ?>">
                  <?= htmlspecialchars($address['recipient_name']) ?>, <?= htmlspecialchars($address['address']) ?>, <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state']) ?>, <?= htmlspecialchars($address['postal_code']) ?>, <?= htmlspecialchars($address['phone_number']) ?>
                </label>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p class="text-muted">No saved addresses. Add a new address below.</p>
          <?php endif; ?>
          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="address_id" id="newAddress" value="new">
            <label class="form-check-label" for="newAddress">Add New Address</label>
          </div>
          <div id="newAddressFields" class="mt-3 d-none">
            <input type="text" name="new_recipient_name" class="form-control mb-2" placeholder="Recipient Name">
            <input type="text" name="new_address" class="form-control mb-2" placeholder="Address">
            <input type="text" name="new_city" class="form-control mb-2" placeholder="City">
            <input type="text" name="new_state" class="form-control mb-2" placeholder="State">
            <input type="text" name="new_postal_code" class="form-control mb-2" placeholder="Postal Code">
            <input type="text" name="new_phone_number" class="form-control mb-2" placeholder="Phone Number">
          </div>
        </div>
      </div>

      <!-- Payment Method Section -->
      <div class="col-lg-8 payment-section">
        <div class="card payment-card p-4">
          <h5>Select Payment Method</h5>
          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="payment_method" id="cardPayment" value="card" required>
            <label class="form-check-label" for="cardPayment">Credit/Debit Card</label>
          </div>
          <div id="cardDetails" class="mt-3 d-none">
            <input type="text" name="card_number" class="form-control mb-2" placeholder="Card Number">
            <input type="text" name="card_name" class="form-control mb-2" placeholder="Cardholder Name">
            <input type="text" name="card_expiry" class="form-control mb-2" placeholder="Expiry Date (MM/YY)">
            <input type="text" name="card_cvv" class="form-control mb-2" placeholder="CVV">
          </div>

          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="payment_method" id="fpxPayment" value="fpx">
            <label class="form-check-label" for="fpxPayment">FPX (Online Banking)</label>
          </div>
          <div id="fpxDetails" class="mt-3 d-none">
            <select name="fpx_bank" class="form-control">
              <option value="">Select Bank</option>
              <option value="Maybank">Maybank</option>
              <option value="CIMB">CIMB</option>
              <option value="Public Bank">Public Bank</option>
              <option value="RHB">RHB</option>
            </select>
          </div>

          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="payment_method" id="qrPayment" value="qr">
            <label class="form-check-label" for="qrPayment">QR Code</label>
          </div>
          <div id="qrDetails" class="mt-3 d-none text-center">
            <img src="img/qr.ewallet.jpeg" alt="QR Code" class="qr-code">
            <p class="qr-instructions">Scan this QR code using your bank app to complete the payment.</p>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="col-lg-4">
        <div class="card payment-card p-4">
          <h5>Order Summary</h5>
          <ul class="list-group mb-3">
            <?php foreach ($books as $book): ?>
              <li class="list-group-item d-flex justify-content-between">
                <?= htmlspecialchars($book['bookname']) ?> x<?= $book['quantity'] ?>
                <span>RM <?= number_format($book['price'] * $book['quantity'], 2) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="d-flex justify-content-between">
            <span>Subtotal:</span>
            <span>RM <?= number_format($totalPrice, 2) ?></span>
          </div>
          <div class="d-flex justify-content-between">
            <span>Tax (6%):</span>
            <span>RM <?= number_format($totalPrice * 0.06, 2) ?></span>
          </div>
          <div class="d-flex justify-content-between fw-bold">
            <span>Total:</span>
            <span>RM <?= number_format($totalPrice * 1.06, 2) ?></span>
          </div>
          <button type="submit" class="btn btn-primary-custom btn-block mt-3">Confirm Payment</button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
  // Toggle payment method details
  document.querySelectorAll('input[name="payment_method"]').forEach(input => {
    input.addEventListener('change', function () {
      document.getElementById('cardDetails').classList.add('d-none');
      document.getElementById('fpxDetails').classList.add('d-none');
      document.getElementById('qrDetails').classList.add('d-none');

      if (this.value === 'card') {
        document.getElementById('cardDetails').classList.remove('d-none');
      } else if (this.value === 'fpx') {
        document.getElementById('fpxDetails').classList.remove('d-none');
      } else if (this.value === 'qr') {
        document.getElementById('qrDetails').classList.remove('d-none');
      }
    });
  });

  // Toggle new address fields
  document.querySelectorAll('input[name="address_id"]').forEach(input => {
    input.addEventListener('change', function () {
      const newAddressFields = document.getElementById('newAddressFields');
      newAddressFields.classList.toggle('d-none', this.value !== 'new');
    });
  });
</script>
</body>
</html>
