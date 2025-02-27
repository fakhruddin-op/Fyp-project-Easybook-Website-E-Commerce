<!DOCTYPE html>
<html lang="en">
<!-- blank.html -->
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Easybook - Admin Dashboard</title>

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
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="orderlisting.php">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fad fa-book-reader"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Easybook </div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="admindashboard.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>

      <hr class="sidebar-divider my-0">
      <br>

      <!-- Management Section -->
      <div class="sidebar-heading">
        Management
      </div>
      <li class="nav-item">
        <a class="nav-link" href="approval_page.php">
        <i class="fas fa-fw fa-check-circle"></i>
          <span>Approval Book</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="book_details.php">
        <i class="fas fa-fw fa-book"></i>
          <span>Book Details</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="user_summary.php">
        <i class="fas fa-fw fa-users"></i>
          <span>User Summary</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="manageseller.php">
        <i class="fas fa-fw fa-user-cog"></i>
          <span>Manage Seller</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="managebuyers.php">
        <i class="fas fa-fw fa-user-tag"></i>
          <span>Manage Buyer</span></a>
      </li>

      <!-- Transactions Section -->
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Transactions
      </div>
      <li class="nav-item">
        <a class="nav-link" href="seller_sales_details.php">
        <i class="fas fa-fw fa-shopping-cart"></i>
          <span>Seller Sales Details</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="order_details.php">
        <i class="fas fa-fw fa-shopping-cart"></i>
          <span>Order Details</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="payment_seller.php">
        <i class="fas fa-fw fa-wallet"></i>
          <span>Payment to Seller</span></a>
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
        <h2>Welcome admin <?php echo $_SESSION['username'] ?></h2>

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
            
          </button>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
         
            
            

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
             
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                  <?php 
                  //session_start();
                  if(isset($_SESSION['username'])){
                    echo $_SESSION['username'];
                  }
                  ?>
                </span>
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>

          </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
<!-- header.template.php -->