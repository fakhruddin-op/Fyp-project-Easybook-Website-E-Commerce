<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Easybook - Seller Dashboard</title>

  <!-- Custom fonts for this template-->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
 <!-- Custom styles for this page -->
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="seller_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fad fa-book-reader"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Easybook </div>
      </a>

     <!-- Divider -->
     <hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item">
  <a class="nav-link" href="seller_dashboard.php">
    <i class="fas fa-fw fa-tachometer-alt"></i>
    <span>Dashboard</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
  Management
</div>

<li class="nav-item">
  <a class="nav-link" href="addbook.php">
    <i class="fas fa-fw fa-plus-circle"></i>
    <span>Add Book</span></a>
</li>
<li class="nav-item">
  <a class="nav-link" href="view_listing_book.php">
    <i class="fas fa-fw fa-list"></i>
    <span>View Book Listing</span></a>
</li>

<div class="sidebar-heading">
  Transactions
</div>

<li class="nav-item">
  <a class="nav-link" href="approval_book.php">
    <i class="fas fa-fw fa-check"></i>
    <span>Approval Book</span></a>
</li>
<li class="nav-item">
  <a class="nav-link" href="seller_orders.php">
    <i class="fas fa-fw fa-shopping-cart"></i>
    <span>My Orders</span></a>
</li>
<li class="nav-item">
  <a class="nav-link" href="seller_payments.php">
    <i class="fas fa-fw fa-wallet"></i>
    <span>Payments</span></a>
</li>
<li class="nav-item">
  <a class="nav-link" href="sales_summary.php">
    <i class="fas fa-fw fa-chart-line"></i>
    <span>Sales Summary</span></a>
</li>

<div class="sidebar-heading">
  Feedback
</div>

<li class="nav-item">
  <a class="nav-link" href="seller_reviews.php">
    <i class="fas fa-fw fa-star"></i>
    <span>Reviews from Users</span></a>
</li>
<li class="nav-item">
  <a class="nav-link" href="seller_chat.php">
    <i class="fas fa-fw fa-comments"></i>
    <span>Chat Box</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
  <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>
<!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <h2 class="text-primary">Welcome back, <?php echo $_SESSION['username']; ?></h2>
        
        <ul class="navbar-nav ml-auto">
            <!-- Notification Icon for New Messages -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Notifications -->
                <span id="notificationCount" class="badge badge-danger badge-counter">0</span>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">Chat Notifications</h6>
                <div id="notificationList">
                  <!-- Notifications will be loaded here by JavaScript -->
                </div>
              </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['username']; ?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profile.php"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

         

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            

            

            <div class="topbar-divider d-none d-sm-block"></div>

            

          </ul>
          

        </nav>
        <!-- End of Topbar -->
          <!-- JavaScript to Fetch Notifications -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script>
    function fetchNotifications() {
      $.ajax({
        url: "fetch_notifications.php",
        method: "GET",
        success: function(data) {
          const notifications = JSON.parse(data);
          const notificationCount = notifications.length;

          // Update the notification badge count
          $("#notificationCount").text(notificationCount > 0 ? notificationCount : "");

          // Update the notification list dropdown
          let notificationHtml = "";
          if (notificationCount > 0) {
            notifications.forEach(function(notification) {
              notificationHtml += `
                <a class="dropdown-item d-flex align-items-center" href="seller_chat.php?buyer_id=${notification.buyer_id}">
                  <div>
                    <div class="text-truncate">${notification.message}</div>
                    <div class="small text-gray-500">From ${notification.username}</div>
                  </div>
                </a>`;
            });
          } else {
            notificationHtml = "<p class='text-center m-3'>No new notifications</p>";
          }
          $("#notificationList").html(notificationHtml);
        }
      });
    }

    // Fetch notifications on page load and periodically
    $(document).ready(function() {
      fetchNotifications(); // Initial fetch
      setInterval(fetchNotifications, 10000); // Fetch every 10 seconds
    });
  </script>

        <!-- Begin Page Content -->
        <div class="container-fluid">
<!-- header.template.php -->