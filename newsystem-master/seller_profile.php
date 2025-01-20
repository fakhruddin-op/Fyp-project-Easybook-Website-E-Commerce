<?php
session_start();
require 'dbconnect.php';

$searchKeyword = '';


// Check if 'sellerid' is set in the URL
if (!isset($_GET['sellerid'])) {
    echo "No seller ID provided.";
    exit();
}

$sellerId = mysqli_real_escape_string($conn, $_GET['sellerid']);

// Fetch seller details
$sqlSeller = "SELECT * FROM user WHERE id = '$sellerId'";
$resultSeller = mysqli_query($conn, $sqlSeller);

if (!$resultSeller || mysqli_num_rows($resultSeller) == 0) {
    echo "Seller not found.";
    exit();
}

$seller = mysqli_fetch_assoc($resultSeller);

// Redirect to login page if user is not logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Handle review form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating']) && isset($_POST['comment'])) {
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $userId = $_SESSION['id'];

    $sqlInsertReview = "INSERT INTO seller_reviews (seller_id, user_id, rating, comment, created_at) 
                        VALUES ('$sellerId', '$userId', '$rating', '$comment', NOW())";
    $resultInsert = mysqli_query($conn, $sqlInsertReview);

    if (!$resultInsert) {
        echo "Error: " . mysqli_error($conn);
        exit();
    }
}

// Fetch reviews
$sqlReviews = "SELECT r.*, u.username 
               FROM seller_reviews r 
               JOIN user u ON r.user_id = u.id 
               WHERE r.seller_id = '$sellerId' 
               ORDER BY r.created_at DESC";
$resultReviews = mysqli_query($conn, $sqlReviews);

// Calculate overall rating
$sqlOverallRating = "SELECT AVG(rating) AS average_rating, COUNT(*) AS total_reviews 
                     FROM seller_reviews WHERE seller_id = '$sellerId'";
$resultOverallRating = mysqli_query($conn, $sqlOverallRating);
$overallRatingData = mysqli_fetch_assoc($resultOverallRating);
$averageRating = round($overallRatingData['average_rating'], 1);
$totalReviews = $overallRatingData['total_reviews'];

// Fetch total books sold by the seller
$sql_books_sold = "SELECT COUNT(*) AS total_sold FROM orderbook WHERE ownerid = '$sellerId' AND is_purchased = 1";
$result_books_sold = mysqli_query($conn, $sql_books_sold);
$totalBooksSold = 0;

if ($result_books_sold) {
    $row_books_sold = mysqli_fetch_assoc($result_books_sold);
    $totalBooksSold = $row_books_sold['total_sold'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= htmlspecialchars($seller['username']) ?>'s Profile - Easy Book</title>
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

    /* Override the container padding */
    .container-fluid {
      padding-left: 0;
      padding-right: 0;
    }

    /* Full-width for sections */
    .content-section {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
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


    /* General Styling */
    
    .profile-header { text-align: center; margin-bottom: 20px; }
    .seller-icon { font-size: 5rem; color: #007bff; }
    .seller-name { font-size: 1.75rem; font-weight: bold; color: #333; margin-top: 10px; }

    /* Overall Rating Section */
    .overall-rating { text-align: center; margin-top: 20px; margin-bottom: 20px; }
    .rating-stars { color: #FFD700; font-size: 1.5rem; }
    .rating-value { font-size: 1.25rem; color: #555; margin-top: 5px; }
    .total-reviews { font-size: 1rem; color: #888; }

    /* Review Form Styling */
    .review-form { background-color: #f8f9fa; padding: 20px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
    .review-form h3 { margin-bottom: 15px; font-size: 1.2rem; color: #023047; }
    .review-form textarea { width: 100%; height: 100px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd; padding: 10px; font-size: 0.95rem; }
    .review-form select { width: 100%; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ddd; padding: 8px; font-size: 0.95rem; }
    .review-form .btn-submit { background-color: #023047; color: #FFD166; border: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; }
    .review-form .btn-submit:hover { background-color: #005f73; color: #ffffff; }

    /* Reviews Section */
    .review-section { margin-top: 30px; }
    .review { background-color: #ffffff; padding: 20px; border-radius: 10px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
    .review h5 { font-size: 1rem; font-weight: bold; color: #333; margin: 0; }
    .review .rating { color: #E63946; margin-left: 10px; font-weight: bold; }
    .review .comment { margin-top: 10px; color: #555; font-size: 0.95rem; }
    .review .created-at { font-size: 0.85rem; color: #999; margin-top: 8px; }
  </style>
</head>
<body>


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
        <a class="nav-link" href="mybooking.php">
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
      <a class="dropdown-item" href="mybooking.php"><i class="fas fa-shopping-cart"></i> My Cart</a>
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
  <!-- Seller Header Section -->
  <div class="profile-header text-center mb-5">
    <i class="fas fa-user-circle seller-icon text-primary display-4 mb-3"></i>
    <h2 class="fw-bold"><?= htmlspecialchars($seller['username']) ?>'s Profile</h2>
    <p class="text-muted">Total Books Sold: <strong><?= $totalBooksSold ?></strong></p>
  </div>
  <!-- Overall Rating Section -->
  <div class="overall-rating text-center mb-5">
    <div class="rating-stars mb-2">
      <?php
      for ($i = 1; $i <= 5; $i++) {
          echo $i <= floor($averageRating) ? '<i class="fas fa-star text-warning fs-4"></i>' : ($i - $averageRating < 1 ? '<i class="fas fa-star-half-alt text-warning fs-4"></i>' : '<i class="far fa-star text-warning fs-4"></i>');
      }
      ?>
    </div>
    <div class="rating-value fs-5 fw-bold"><?= number_format($averageRating, 1) ?> / 5</div>
    <div class="total-reviews text-muted">(<?= $totalReviews ?> reviews)</div>
  </div>

  <!-- Review Form Section -->
  <div class="review-form bg-light p-4 rounded shadow-sm mb-5">
    <h3 class="fw-bold mb-4">Leave a Review</h3>
    <form method="POST" action="">
      <div class="mb-4">
        <label for="rating" class="form-label">Rating:</label>
        <select name="rating" id="rating" class="form-select" required>
          <option value="5">5 - Excellent</option>
          <option value="4">4 - Very Good</option>
          <option value="3">3 - Good</option>
          <option value="2">2 - Fair</option>
          <option value="1">1 - Poor</option>
        </select>
      </div>
      <div class="mb-4">
        <label for="comment" class="form-label">Review:</label>
        <textarea name="comment" id="comment" rows="4" class="form-control" placeholder="Write your review here..." required></textarea>
      </div>
      <button type="submit" class="btn btn-primary px-5">Submit Review</button>
    </form>
  </div>

  <!-- Reviews Section -->
  <div class="review-section">
    <h3 class="fw-bold mb-4">Customer Reviews</h3>
    <?php if (mysqli_num_rows($resultReviews) > 0) { ?>
      <div class="review-list">
        <?php while ($review = mysqli_fetch_assoc($resultReviews)) {
            $sensorUsername = substr($review['username'], 0, 1) . str_repeat('*', strlen($review['username']) - 1);
        ?>
          <div class="review bg-white p-4 rounded shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="fw-bold text-primary mb-0"><?= htmlspecialchars($sensorUsername) ?></h5>
              <span class="rating text-warning">
                <?= str_repeat('<i class="fas fa-star"></i>', $review['rating']) ?>
                <?= str_repeat('<i class="far fa-star"></i>', 5 - $review['rating']) ?>
              </span>
            </div>
            <p class="comment mb-2"><?= htmlspecialchars($review['comment']) ?></p>
            <p class="created-at text-muted small"><?= date('F j, Y', strtotime($review['created_at'])) ?></p>
          </div>
        <?php } ?>
      </div>
    <?php } else { ?>
      <p class="text-center text-muted">No reviews yet. Be the first to leave a review!</p>
    <?php } ?>
  </div>
</div>



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
