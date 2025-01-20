<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Easy Book - Register</title>

  <!-- Custom fonts for this template-->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">

</head>

<style type="text/css">
  body {
    background: url('../img/unikl2.jpg') no-repeat center center fixed; /* Change the path to your image */
    background-size: cover; /* This will make the background cover the whole page */
  }
</style>
<body>

  <div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
    <div class="col-xl-6 col-lg-6 col-md-9">
    <div class="card o-hidden border-0 shadow-lg my-5">
      <div class="card-body p-0">
        <!-- Nested Row within Card Body -->
        <div class="row">
          <div class="col-lg-12">
            <div class="p-5">
              <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Create an account now!</h1>
              </div>
              <form class="user" method="POST" action="registerseller.php">

                <div class="form-group">
                  <input name="txt_username" type="text" class="form-control form-control-user" placeholder="Username " autofocus required>
                </div>
                <div class="form-group">
                  <input name="txt_contact" type="text" class="form-control form-control-user" id="exampleInputEmail" placeholder="Phone Number" required>
                </div>
                <div class="form-group">
                  <input name="txt_email" type="email" class="form-control form-control-user" id="exampleInputEmail" placeholder="Email Address" required>
                </div>
                <div class="form-group row">
                  <div class="col-sm-6 mb-3 mb-sm-0">
                    <input name="txt_password" type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" required>
                  </div>
                  <div class="col-sm-6">
                    <input name="txt_repeatpassword" type="password" class="form-control form-control-user" id="exampleRepeatPassword" placeholder="Repeat Password" required>
                  </div>
                </div>
                <button name="btn_register" class="btn btn-primary btn-user btn-block" type="submit">Register Account</button>

              </form>
              <hr>
              <div class="text-center">
                <a class="small" href="../forgot-password/forgot_password.php">Forgot Password?</a>
              </div>
              <div class="text-center">
                <a class="small" href="../login.php">Already have an account? Login!</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="../js/sb-admin-2.min.js"></script>

</body>

</html>
