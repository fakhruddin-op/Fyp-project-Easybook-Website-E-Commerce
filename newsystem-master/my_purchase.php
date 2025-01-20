<?php
session_start();
$buyerid = $_SESSION['id']; // Current user's ID
require 'dbconnect.php';

// Initialize search variable
$searchKeyword = '';
if (isset($_GET['keyword'])) {
    $searchKeyword = mysqli_real_escape_string($conn, $_GET['keyword']);
}

// Query to fetch purchased books
$sql = "SELECT orderbook.*, user.contact, user.username 
        FROM orderbook 
        JOIN user ON orderbook.ownerid = user.id 
        WHERE orderbook.buyerid = '$buyerid' AND orderbook.is_purchased = 1";

if (!empty($searchKeyword)) {
    $sql .= " AND (orderbook.bookname LIKE '%$searchKeyword%' 
                  OR orderbook.description LIKE '%$searchKeyword%' 
                  OR user.username LIKE '%$searchKeyword%' 
                  OR orderbook.bookcodesubject LIKE '%$searchKeyword%' 
                  OR orderbook.price LIKE '%$searchKeyword%')";
}

$qr = mysqli_query($conn, $sql);

if (mysqli_error($conn)) {
    echo "Error: " . mysqli_error($conn);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Easy Book - My Purchases</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
       /* Custom navbar styling */
     .navbar-custom { background-color: #023047; padding: 0.8rem; }
    .navbar-brand { font-weight: bold; color: #FFD166; }
    .navbar-nav .nav-link { color: white; font-size: 1rem; }
    .search-bar { max-width: 500px; width: 100%; }

    /* Header and Navbar Styling */
.header-section .jumbotron {
  background-color: #f8f9fa;
  margin-bottom: 0;
}

.header-section .jumbotron h1 {
  font-size: 2.5rem;
  font-weight: 700;
}

.header-section .jumbotron p {
  font-size: 1.2rem;
  color: #ebedf0;
}

.navbar {
  padding: 0.8rem 1rem;
  border-bottom: 1px solid #eaeaea;
}

.navbar-brand .logo {
  width: 40px;
  height: auto;
  margin-right: 8px;
}

.navbar-brand span {
  font-size: 1.25rem;
  color: #333;
}

.navbar-nav .nav-item .nav-link {
  color: #333;
  font-weight: 500;
  padding: 0.5rem 1rem;
  transition: color 0.3s ease;
}

.navbar-nav .nav-item .nav-link:hover {
  color: #007bff;
}

.search-bar input[type="search"] {
  width: 250px;
  max-width: 100%;
  padding: 0.4rem 1rem;
  margin-right: 0.5rem;
  border: 1px solid #ddd;
  transition: border-color 0.3s ease;
}

.search-bar input[type="search"]:focus {
  border-color: #007bff;
  outline: none;
}

.search-bar button[type="submit"] {
  padding: 0.4rem 1rem;
}

.dropdown-menu .dropdown-item {
  color: #333;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
}

.dropdown-menu .dropdown-item:hover {
  background-color: #f8f9fa;
  color: #007bff;
}

.dropdown-divider {
  margin: 0.4rem 0;
}

/* Footer Styling */
.footer-section {
  background-color: #f8f9fa;
  color: #6c757d;
}

.footer-section .footer-logo {
  width: 30px;
  height: auto;
  margin-right: 8px;
}

.footer-section h5 {
  color: #333;
  font-weight: 600;
  font-size: 1.1rem;
}

.footer-section a {
  color: #6c757d;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-section a:hover {
  color: #007bff;
}

.footer-section .list-unstyled {
  padding-left: 0;
  list-style: none;
}

.footer-section .list-unstyled li {
  margin-bottom: 0.5rem;
}

.footer-section .text-muted {
  font-size: 0.9rem;
}

.footer-section .social-icons a {
  font-size: 1.2rem;
  color: #6c757d;
  margin: 0 8px;
  transition: color 0.3s ease;
}

.footer-section .social-icons a:hover {
  color: #007bff;
}

.footer-section hr {
  border-top: 1px solid #eaeaea;
  margin: 1.5rem 0;
}
/* Add top padding to the main content to avoid overlap with fixed navbar */
body {
  padding-top: 70px; /* Adjust height based on your navbar's height */
}

@media (max-width: 992px) {
  body {
    padding-top: 90px; /* Add more space for smaller screens if necessary */
  }
}
        .navbar-custom { background-color: #023047; padding: 1rem; }
        .navbar-brand { font-weight: bold; color: #FFD166; }
        .navbar-nav .nav-link { color: white; font-size: 1.1rem; }
        .search-bar { max-width: 500px; width: 100%; }

        /* Add top padding to the main content to avoid overlap with fixed navbar */
        body { padding-top: 70px; }
        @media (max-width: 992px) { body { padding-top: 90px; } }

        /* Card Design */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.card-img-top {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.badge-primary {
    background-color: #007bff;
    color: #fff;
    border-radius: 6px;
}

.badge-success {
    background-color:rgb(64, 237, 87);
    color: #fff;
}

.badge-warning {
    background-color:rgba(51, 140, 242, 0.8);
    color: #fff;
}

/* Buttons */
.btn-outline-info {
    border-color: #17a2b8;
    color: #17a2b8;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-outline-info:hover {
    background-color: #17a2b8;
    color: #fff;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
}

/* Text and Truncation */
.text-truncate {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

/* Alerts */
.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
    border-radius: 8px;
}

/* Spacing and Layout */
.mt-auto {
    margin-top: auto;
}

.position-absolute {
    top: 0;
    left: 0;
    z-index: 10;
}


        /* Shopee-style Chat Modal */
.chat-modal {
    height: 80vh; /* Modal height */
    display: flex;
    flex-direction: column;
    border-radius: 8px;
    overflow: hidden;
}

.chat-modal-header {
    background-color: #291ddb; /* Shopee's primary red color */
    color: white;
    padding: 1rem;
    font-size: 1.2rem;
}

.chat-modal-body {
    background-color: #fafafa;
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.chat-modal-footer {
    background-color: white;
    padding: 0.75rem 1rem;
    border-top: 1px solid #ddd;
}

/* Chat Content */
.chat-content {
    display: flex;
    flex-direction: column;
}

.chat-bubble {
    max-width: 70%;
    padding: 10px 15px;
    margin-bottom: 10px;
    border-radius: 15px;
    font-size: 0.9rem;
    word-wrap: break-word;
    position: relative;
}

.chat-bubble.sent {
    background-color: #dcf8c6; /* Light green for sent messages */
    align-self: flex-end;
}

.chat-bubble.received {
    background-color: #ffffff;
    align-self: flex-start;
    border: 1px solid #ddd;
}

/* Timestamp */
.chat-timestamp {
    font-size: 0.75rem;
    color: #555;
    margin-top: 5px;
    text-align: right;
}


    </style>
<!-- Header Section -->

 <!-- Navbar Section -->
 <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <!-- Brand Logo and Name -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/easybook.png" alt="Easy Book Logo" class="logo mr-2">
      <span class="font-weight-bold">Easy Book</span>
    </a>

    <!-- Mobile Menu Toggle Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible Navbar Content -->
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav mr-auto">
      <li class="nav-item">
          <a class="nav-link" href="about_us.php">About Us</a>
        </li>
        
      </ul>

      <!-- Search Bar (Centers in large screens, stacks in small screens) -->
      <form class="form-inline my-2 my-lg-0 mx-lg-auto search-bar" method="GET" action="">
        <input class="form-control rounded-pill mr-2" type="search" name="keyword" placeholder="Search books" value="<?= htmlspecialchars($searchKeyword) ?>" aria-label="Search">
        <button class="btn btn-primary rounded-pill" type="submit">Search</button>
      </form>


      <!-- Right-aligned User Account Links -->
      <ul class="navbar-nav">
      <li class="nav-item">
      <li class="nav-item">
      <?php if (isset($_SESSION['id'])): ?>
        <a class="nav-link" href="my_cart.php">
         <i class="fas fa-shopping-cart"></i> 
       </a>
     <?php endif; ?>
      </li>
      <li class="nav-item">
        <?php if (isset($_SESSION['id'])): ?>
        <a class="nav-link" href="my_purchase.php">
         <i class="fas fa-file-invoice-dollar"></i>
        </a>
       <?php endif; ?>
      </li>
      <li class="nav-item dropdown d-flex align-items-center">
      <?php if (isset($_SESSION['id'])): ?>
    <!-- Chat Icon -->
    <li class="nav-item dropdown">
    <a 
    class="nav-link dropdown-toggle" 
    href="#" 
    id="messagesDropdown" 
    role="button" 
    data-toggle="dropdown" 
    aria-haspopup="true" 
    aria-expanded="false" 
    title="Messages">
    <i class="fas fa-comments"></i>
  </a>
  <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="messagesDropdown" id="messagesList">
    <!-- Chat list will be dynamically loaded -->
    
  </div>
</li>
    <li class="nav-item dropdown">
  


    <!-- User Dropdown -->
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
     
    <?= htmlspecialchars($_SESSION['username']) ?>

    </a>
    <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="navbarDropdown">
      <a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> My Profile</a>
      <a class="dropdown-item" href="edit_profile.php"><i class="fas fa-edit"></i> Edit Profile</a>
      <a class="dropdown-item" href="my_cart.php"><i class="fas fa-shopping-cart"></i> My Cart</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </div>
  <?php else: ?>
    <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
  <?php endif; ?>
</li>
      </ul>
    </div>
  </div>
</nav>

</header>

<div class="container mt-5">
    <h2 class="font-weight-bold text-primary mb-4 text-center">My Purchases</h2>
    <div class="row">
        <?php if (mysqli_num_rows($qr) > 0): ?>
            <?php while ($rec = mysqli_fetch_array($qr)): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 rounded-lg h-100">
                        <!-- Book Cover Image -->
                        <div class="position-relative">
                            <img src="seller/bookcover/<?= htmlspecialchars($rec['bookcover']) ?>" 
                                 alt="Book Cover" 
                                 class="card-img-top img-fluid rounded-top" 
                                 style="height: 230px; object-fit: cover;">
                            <!-- Price Badge -->
                            <span class="badge badge-primary position-absolute top-0 start-0 m-2 px-3 py-2" 
                                  style="background-color: #007bff; font-size: 0.85rem; font-weight: 600;">
                                RM <?= number_format($rec['price'], 2) ?>
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body text-center d-flex flex-column p-4">
                            <!-- Book Title -->
                            <h5 class="card-title mb-2 text-truncate" style="font-weight: bold; color: #333;">
                                <?= htmlspecialchars($rec['bookname']) ?>
                            </h5>

                            <!-- Seller Information -->
                            <p class="text-muted mb-2" style="font-size: 0.9rem;">
                                <i class="fas fa-user"></i> <strong>Seller:</strong> <?= htmlspecialchars($rec['username']) ?>
                            </p>

                            <!-- Purchase Status -->
                            <span class="badge 
                                <?= ($rec['order_status'] === 'Completed') ? 'badge-success' : 'badge-warning' ?>" 
                                style="font-size: 0.85rem; padding: 6px 12px; margin-bottom: 1rem;">
                                <?= htmlspecialchars($rec['order_status']) ?>
                            </span>

                            <!-- Action Buttons -->
                            <div class="mt-auto">
                                <a href="book_details2.php?idbook=<?= $rec['idbook'] ?>" 
                                   class="btn btn-outline-info btn-sm px-3 mr-2">
                                    <i class="fas fa-info-circle"></i> Details
                                </a>
                                <button type="button" 
                                        class="btn btn-primary btn-sm px-3" 
                                        onclick="event.preventDefault(); openChat(<?= $rec['ownerid'] ?>, '<?= htmlspecialchars($rec['username']) ?>')">
                                    <i class="fas fa-comment"></i> Chat
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <!-- No Purchases Message -->
            <div class="col-12">
                <div class="alert alert-warning text-center" role="alert">
                    You haven't purchased anything yet. Start shopping now!
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content chat-modal">
            <!-- Header -->
            <div class="modal-header chat-modal-header d-flex align-items-center">
                <button type="button" class="btn btn-link text-white p-0" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h5 class="modal-title mx-auto" id="chatModalLabel">
                    <i class="fas fa-user-circle"></i> Chat with <span id="chatUserName">Seller</span>
                </h5>
            </div>
            <!-- Chat Body -->
            <div class="modal-body chat-modal-body">
                <div id="chatContent" class="chat-content">
                    <!-- Messages dynamically loaded -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <div class="modal-footer chat-modal-footer">
                <form id="chatForm" class="w-100 d-flex align-items-center">
                    <input type="hidden" id="chatSellerId">
                    <input type="text" id="chatMessage" class="form-control chat-input" placeholder="Type your message..." required>
                    <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="cta-section container-fluid text-center py-5" style="background-color: #f8f9fa;">
  <div class="cta-content p-4" style="border: 1px solid #ddd; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);">
    <h2 class="cta-title fw-bold mb-3" style="color: #1b19b0;">Sell Your Books Now!</h2>
    <p class="cta-text mb-4" style="font-size: 1.1rem; color: #6c757d;">Join <strong>Easy Book</strong> and reach thousands of buyers in just a few clicks!</p>
    <a class="btn cta-button btn-lg fw-bold px-4 py-2" href="seller/register/" style="background-color: #1b19b0; color: #fff; border-radius: 25px; text-transform: uppercase; transition: all 0.3s;">
      Start Selling
    </a>
  </div>
</div>
<script>
  document.getElementById('messagesDropdown').addEventListener('click', function () {
    const messagesList = document.getElementById('messagesList');

    // Display loading spinner
    messagesList.innerHTML = `
      <div class="text-center p-3">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
    `;

    // Fetch chat list from the server
    fetch('fetch_chats.php')
        .then(response => response.text())
        .then(data => {
            messagesList.innerHTML = data;
        })
        .catch(error => {
            messagesList.innerHTML = `
              <div class="text-center p-3 text-danger">
                Failed to load chats. Please try again later.
              </div>
            `;
            console.error('Error fetching chat list:', error);
        });
});
function openChat(sellerId, sellerName) {
    // Set modal title
    document.getElementById('chatUserName').innerText = sellerName || 'Unknown';
    document.getElementById('chatSellerId').value = sellerId;

    // Open modal
    $('#chatModal').modal('show');

    // Fetch messages
    const chatContent = document.getElementById('chatContent');
    chatContent.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `;
    fetch(`chat.php?user_id=${sellerId}`)
        .then(response => response.text())
        .then(data => {
            chatContent.innerHTML = data;
            chatContent.scrollTop = chatContent.scrollHeight;
        })
        .catch(error => {
            console.error('Error fetching chat:', error);
            chatContent.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Failed to load chat messages.
                </div>
            `;
        });
}

// Handle message sending
document.getElementById('chatForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const sellerId = document.getElementById('chatSellerId').value;
    const message = document.getElementById('chatMessage').value.trim();

    if (!message) return;

    fetch('send_messages_1.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `seller_id=${sellerId}&message=${encodeURIComponent(message)}`
    })
        .then(response => response.text())
        .then(() => {
            const chatContent = document.getElementById('chatContent');
            chatContent.innerHTML += `<div class="chat-bubble sent">${message}</div>`;
            document.getElementById('chatMessage').value = '';
            chatContent.scrollTop = chatContent.scrollHeight;
        })
        .catch(error => console.error('Error sending message:', error));
});

</script>
<!-- Footer Section -->
<footer class="footer-section bg-light text-center text-lg-start mt-5">
  <div class="container py-4">
    <div class="row">
      
      <!-- Logo and Brief Description -->
      <div class="col-lg-4 col-md-6 mb-4">
        <a href="index.php" class="d-flex align-items-center justify-content-center mb-3">
          <img src="img/easybook.png" alt="Easy Book Logo" class="footer-logo mr-2"> <!-- Add your logo here -->
          <span class="font-weight-bold">Easy Book</span>
        </a>
        <p class="small text-muted">Your reliable platform to find and sell books easily.</p>
      </div>

      <!-- Navigation Links -->
      <div class="col-lg-4 col-md-6 mb-4">
        <h5 class="mb-3">Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="about.php" class="text-muted">About Us</a></li>
          <li><a href="contact.php" class="text-muted">Contact</a></li>
          <li><a href="terms.php" class="text-muted">Terms of Service</a></li>
          <li><a href="privacy.php" class="text-muted">Privacy Policy</a></li>
        </ul>
      </div>

      <!-- Social Media and Contact Info -->
      <div class="col-lg-4 col-md-12 mb-4">
        <h5 class="mb-3">Connect with Us</h5>
        <div class="d-flex justify-content-center">
          <a href="https://facebook.com" class="text-muted mx-2"><i class="fab fa-facebook-f"></i></a>
          <a href="https://twitter.com" class="text-muted mx-2"><i class="fab fa-twitter"></i></a>
          <a href="https://instagram.com" class="text-muted mx-2"><i class="fab fa-instagram"></i></a>
          <a href="https://linkedin.com" class="text-muted mx-2"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <p class="small text-muted mt-2">Email: <a href="mailto:support@easybook.com" class="text-muted">support@easybook.com</a></p>
      </div>

    </div>

    <hr class="my-4">
    
    <!-- Copyright -->
    <div class="text-center small text-muted">
      © <?= date("Y"); ?> Easy Book. All rights reserved.
    </div>
  </div>
</footer>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="js/sb-admin-2.min.js"></script>
</body>
</html>