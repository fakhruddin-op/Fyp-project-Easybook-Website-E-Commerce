<?php
session_start();
include 'header.template.php';
require '../dbconnect.php';

// Check if the user is logged in and is a seller
if (!isset($_SESSION['id']) || $_SESSION['accesslevel'] != 'seller') {
    header('Location: login.php');
    exit();
}

$sellerId = $_SESSION['id'];

// Fetch seller information
$sqlSeller = "SELECT username FROM user WHERE id = '$sellerId'";
$resultSeller = mysqli_query($conn, $sqlSeller);
$seller = mysqli_fetch_assoc($resultSeller);

// Fetch all reviews for the logged-in seller and calculate average rating
$sqlReviews = "SELECT r.*, u.username AS reviewer_username 
               FROM seller_reviews r 
               JOIN user u ON r.user_id = u.id 
               WHERE r.seller_id = '$sellerId' 
               ORDER BY r.created_at DESC";
$resultReviews = mysqli_query($conn, $sqlReviews);

// Calculate average rating
$sqlAvgRating = "SELECT AVG(rating) as avg_rating FROM seller_reviews WHERE seller_id = '$sellerId'";
$resultAvgRating = mysqli_query($conn, $sqlAvgRating);
$avgRating = 0;
if ($resultAvgRating) {
    $row = mysqli_fetch_assoc($resultAvgRating);
    $avgRating = round($row['avg_rating'], 1); // round to one decimal
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= htmlspecialchars($seller['username']) ?>'s Reviews - Easy Book</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    /* Styling for the review page */
    .container { max-width: 800px; margin-top: 50px; }
    .page-header { display: flex; align-items: center; margin-bottom: 20px; }
    .seller-icon { font-size: 2rem; color: #FFB703; margin-right: 10px; }
    .seller-name { font-size: 1.5rem; font-weight: bold; color: #333; }
    .overall-rating { font-size: 1.2rem; color: #FFD700; margin-left: 15px; }

    /* Review Styling */
    .review-card {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }
    .review-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    .review-header h5 {
      font-size: 1.1rem;
      font-weight: bold;
      color: #333;
    }
    .review-rating { color: #E63946; }
    .review-comment { color: #555; font-size: 0.9rem; line-height: 1.5; margin-top: 5px; }
    .review-date { font-size: 0.85rem; color: #999; margin-top: 5px; }
  </style>
</head>
<body>

<div class="container">
  <!-- Page Header -->
  <div class="page-header">
    <i class="fas fa-user-circle seller-icon"></i>
    <span class="seller-name"><?= htmlspecialchars($seller['username']) ?>'s Reviews</span>
    <?php if ($avgRating > 0): ?>
      <span class="overall-rating">
        <?= str_repeat('⭐', floor($avgRating)) ?>
        <?= ($avgRating - floor($avgRating)) >= 0.5 ? '⭐' : '' ?>
        (<?= $avgRating ?>)
      </span>
    <?php else: ?>
      <span class="overall-rating">No ratings yet</span>
    <?php endif; ?>
  </div>

  <!-- Display Reviews -->
  <?php if (mysqli_num_rows($resultReviews) > 0): ?>
    <?php while ($review = mysqli_fetch_assoc($resultReviews)):
        // Censor the reviewer's username (e.g., "John" -> "J***")
        $sensorUsername = substr($review['reviewer_username'], 0, 1) . str_repeat('*', strlen($review['reviewer_username']) - 1);
    ?>
      <div class="review-card">
        <div class="review-header">
          <h5><?= htmlspecialchars($sensorUsername) ?> <span class="review-rating">⭐ <?= $review['rating'] ?></span></h5>
        </div>
        <p class="review-comment"><?= htmlspecialchars($review['comment']) ?></p>
        <p class="review-date"><?= date('F j, Y', strtotime($review['created_at'])) ?></p>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No reviews have been left for your profile yet.</p>
  <?php endif; ?>
</div>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'footer.template.php';
?>
