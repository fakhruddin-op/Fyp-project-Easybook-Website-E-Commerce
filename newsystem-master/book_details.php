<?php
session_start();
require 'dbconnect.php';

$searchKeyword = '';


// Check if 'idbook' is set in the URL
if (!isset($_GET['idbook'])) {
    echo "No book ID provided.";
    exit();
}

$bookId = mysqli_real_escape_string($conn, $_GET['idbook']);

// Fetch book details based on 'idbook'
$sql = "SELECT orderbook.*, user.contact, user.username, user.id AS ownerid
        FROM orderbook 
        JOIN user ON orderbook.ownerid = user.id 
        WHERE orderbook.idbook = '$bookId'";
$result = mysqli_query($conn, $sql);



if (!$result || mysqli_num_rows($result) == 0) {
    echo "Book not found.";
    exit();
}

$book = mysqli_fetch_assoc($result);

// Fetch the seller's average rating and total reviews from seller_reviews table
$sellerId = $book['ownerid'];
$ratingQuery = "SELECT 
                    AVG(rating) AS avg_rating, 
                    COUNT(*) AS total_reviews 
                FROM seller_reviews 
                WHERE seller_id = '$sellerId'";
$ratingResult = mysqli_query($conn, $ratingQuery);
$ratingData = mysqli_fetch_assoc($ratingResult);

$averageRating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
$totalReviews = $ratingData['total_reviews'];

// Fetch the total books sold by the seller
$sellerid = $book['ownerid']; // Assuming `$book` contains the seller's data
$sql_books_sold = "SELECT COUNT(*) AS total_sold FROM orderbook WHERE ownerid = '$sellerid' AND is_purchased = 1";
$result_books_sold = mysqli_query($conn, $sql_books_sold);
$totalSold = 0;

if ($result_books_sold) {
    $row_books_sold = mysqli_fetch_assoc($result_books_sold);
    $totalSold = $row_books_sold['total_sold'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= htmlspecialchars($book['bookname']) ?> - Easy Book</title>
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
  color: #6c757d;
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


.seller-header {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .seller-icon {
    font-size: 3rem;
    color: #007bff;
    margin-right: 15px;
  }

  .seller-name {
    font-size: 1.5rem;
    font-weight: bold;
  }

  .seller-rating {
    margin-top: 5px;
    font-size: 0.9rem;
  }

  .rating-stars {
    color: #FFD700;
  }

  .card {
    border: none;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .book-title {
    font-size: 1.75rem;
    font-weight: bold;
    color: #333;
  }

  .book-price {
    font-size: 1.5rem;
    font-weight: bold;
    color: #E63946;
  }

  .btn-container .btn {
    text-transform: uppercase;
    font-weight: bold;
  }

  .btn-primary {
    background-color: #007bff;
    color: #fff;
  }

  .btn-primary:hover {
    background-color: #0056b3;
  }

  .btn-secondary {
    background-color: #6c757d;
    color: #fff;
  }

  .btn-secondary:hover {
    background-color: #5a6268;
  }

  .description-section {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .description-title {
    font-size: 1.4rem;
    font-weight: bold;
    color: #007bff;
  }

  .description-content {
    color: #555;
    line-height: 1.6;
  }
  </style>
</head>
<body>

<body id="page-top">


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

<div class="container my-5">
  <!-- Seller Header Section -->
  <div class="seller-header d-flex align-items-center p-4 bg-white rounded shadow-sm mb-4">
    <i class="fas fa-user-circle seller-icon text-primary display-4 mr-3"></i>
    <div>
      <a href="seller_profile.php?sellerid=<?= $book['ownerid'] ?>" class="seller-name h4 mb-1 text-dark text-decoration-none"><?= htmlspecialchars($book['username']) ?>'s Store</a>
      <div class="seller-rating mt-2">
        <span class="rating-stars text-warning">
          <?= str_repeat('<i class="fas fa-star"></i>', floor($averageRating)) ?>
          <?= $averageRating - floor($averageRating) >= 0.5 ? '<i class="fas fa-star-half-alt"></i>' : '' ?>
          <?= str_repeat('<i class="far fa-star"></i>', 5 - ceil($averageRating)) ?>
        </span>
        <small class="text-muted">(<?= $averageRating ?> | <?= $totalReviews ?> reviews)</small>
      </div>
      <div class="seller-sales mt-2">
        <small class="text-muted">Total Books Sold: <strong><?= $totalSold ?></strong></small>
      </div>
    </div>
  </div>

  <!-- Book Details Card -->
  <div class="card border-0 shadow-sm">
    <div class="row no-gutters">
      <!-- Book Image -->
      <div class="col-md-5 d-flex justify-content-center align-items-center p-4">
        <img src="seller/bookcover/<?= htmlspecialchars($book['bookcover']) ?>" alt="Book Cover" class="img-fluid rounded-lg shadow">
      </div>

      <!-- Book Information -->
      <div class="col-md-7">
        <div class="card-body p-4">
        <h2 class="book-title font-weight-bold mb-3" style="color: #000;"><?= htmlspecialchars($book['bookname']) ?></h2>
        <p class="book-price h4 font-weight-bold mb-4" style="color: #000;">RM <?= number_format($book['price'], 2) ?></>
          <p class="book-meta mb-2"><strong>Seller:</strong> <?= htmlspecialchars($book['username']) ?></p>
          <p class="book-meta mb-2"><strong>Book Code:</strong> <?= htmlspecialchars($book['bookcodesubject']) ?></p>
          
         

          <!-- Buttons -->
          <div class="btn-container mt-4">
            <a href="confirmbook.php?idbook=<?= $book['idbook'] ?>" class="btn btn-primary btn-sm px-4 mr-2"><i class="fas fa-shopping-cart"></i> Add to Cart</a>
            <a href="index.php" class="btn btn-secondary btn-sm px-4"><i class="fas fa-arrow-left"></i> Back to Listings</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Description Section -->
  <div class="description-section bg-light rounded p-4 mt-4">
    <h3 class="description-title text-secondary mb-3"><i class="fas fa-book"></i> About This Book</h3>
    <p class="description-content text-muted"><?= htmlspecialchars($book['description']) ?></p>
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
  
<br>

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
</body>
</html>
