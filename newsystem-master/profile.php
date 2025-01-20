<?php
session_start();
require 'dbconnect.php';
$searchKeyword = '';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$userId = $_SESSION['id'];


// Fetch the current user's details from the database
$sql = "SELECT * FROM user WHERE id = '$userId'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    echo "Error: User not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Easy Book</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
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

     /* General Card Styling */
.profile-card {
    max-width: 600px;
    margin: auto;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    background-color: #fff;
}

.profile-card h2 {
    font-size: 1.8rem;
    font-weight: bold;
    color: #333;
}

.profile-card p {
    font-size: 1rem;
    color: #555;
}

/* Avatar Styling */
.profile-avatar img {
    border: 3px solid #007bff;
    transition: transform 0.2s ease-in-out;
}

.profile-avatar img:hover {
    transform: scale(1.1);
}

/* Buttons */
.profile-card .btn-primary {
    background-color: #007bff;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 1rem;
    transition: background-color 0.2s ease-in-out;
}

.profile-card .btn-primary:hover {
    background-color: #0056b3;
}

.profile-card .btn-outline-secondary {
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 1rem;
    color: #555;
    border-color: #ddd;
    transition: color 0.2s ease-in-out, border-color 0.2s ease-in-out;
}

.profile-card .btn-outline-secondary:hover {
    color: #007bff;
    border-color: #007bff;
}

/* Icons */
.profile-card i {
    margin-right: 10px;
    color: #007bff;
}

/* Mobile Responsiveness */
@media (max-width: 576px) {
    .profile-card {
        padding: 15px;
    }

    .profile-avatar img {
        width: 60px;
        height: 60px;
    }
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
</head>
<body>

<body id="page-top">
<!-- Header Section -->
<header class="header-section">
<div class="jumbotron text-center p-5" style="background-image: url('img/buku.jpg'); background-size: cover; background-position: center; color: white;">
    <div class="container">
      <h1 class="display-4 mb-2">Easy Book</h1>
      <p class="lead">Find and Sell Your Books Easily</p>
      <p class="lead">Your profile</p>
    </div>
</div>

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
    <div class="profile-card bg-white shadow-sm rounded p-4">
        <!-- Profile Header -->
        <div class="d-flex align-items-center mb-4">
            <!-- Profile Icon -->
            <div class="profile-avatar me-3">
                <img src="img/profile.jpg" alt="Profile Avatar" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
            </div>
            <div>
                <h2 class="mb-1"><?= htmlspecialchars($user['username']) ?></h2>
                <p class="text-muted mb-0">Welcome back!</p>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="profile-info">
            <h4 class="text-primary mb-3">Account Details</h4>
            <p class="mb-2"><i class="fas fa-user text-primary"></i> <strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p class="mb-2"><i class="fas fa-envelope text-primary"></i> <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p class="mb-3"><i class="fas fa-phone text-primary"></i> <strong>Contact Number:</strong> <?= htmlspecialchars($user['contact']) ?></p>

            <!-- Action Buttons -->
<div class="mt-3">
    <a href="edit_profile.php" class="btn btn-primary me-2"><i class="fas fa-edit"></i> Edit Profile</a>
    <a href="my_purchase.php" class="btn btn-outline-secondary me-2"><i class="fas fa-shopping-cart"></i> Order History</a>
    <a href="view_address.php" class="btn btn-outline-primary"><i class="fas fa-map-marker-alt"></i> View My Address</a>
</div>

        </div>
    </div>
</div>


<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content chat-modal">
      <!-- Header -->
      <div class="modal-header chat-modal-header">
        <button type="button" class="btn btn-link text-white p-0" data-dismiss="modal">
          <i class="fas fa-arrow-left"></i> Back
        </button>
        <h5 class="modal-title mx-auto" id="chatModalLabel">
          <i class="fas fa-user-circle"></i> Chat with <span id="chatUserName">Seller</span>
        </h5>
      </div>
      <!-- Body -->
      <div class="modal-body chat-modal-body">
        <div id="chatContent" class="chat-content">
          <div class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer -->
      <div class="modal-footer chat-modal-footer">
        <form id="chatForm" class="w-100 d-flex">
          <input type="hidden" name="receiver_id" id="receiverId">
          <input type="text" name="message" id="chatMessage" class="form-control chat-input" placeholder="Type your message..." required>
          <button type="submit" class="btn btn-primary ml-2"><i class="fas fa-paper-plane"></i></button>
        </form>
      </div>
    </div>
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
function openChat(userId, userName) {
    // Set the modal title with the user's name
    document.getElementById('chatUserName').innerText = userName;
    document.getElementById('receiverId').value = userId;

    // Open the modal
    $('#chatModal').modal('show');

    // Display loading spinner in chat content
    const chatContent = document.getElementById('chatContent');
    chatContent.innerHTML = `
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
    `;

    // Fetch chat messages via AJAX
    fetch(`chat.php?user_id=${userId}`)
        .then(response => response.text())
        .then(data => {
            chatContent.innerHTML = data;
            chatContent.scrollTop = chatContent.scrollHeight; // Scroll to bottom
        })
        .catch(error => {
            chatContent.innerHTML = `
              <div class="alert alert-danger" role="alert">
                Failed to load chat. Please try again later.
              </div>
            `;
            console.error('Error fetching chat:', error);
        });
}

// Handle message sending
document.getElementById('chatForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const receiverId = document.getElementById('receiverId').value;
    const message = document.getElementById('chatMessage').value;

    fetch('send_message.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `receiver_id=${receiverId}&message=${encodeURIComponent(message)}`
    })
    .then(response => response.text())
    .then(data => {
        // Reload chat content
        openChat(receiverId, document.getElementById('chatUserName').innerText);
        document.getElementById('chatMessage').value = ''; // Clear input
    })
    .catch(error => {
        console.error('Error sending message:', error);
    });
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
