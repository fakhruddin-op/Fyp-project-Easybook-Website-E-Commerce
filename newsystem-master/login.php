<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">


  <title>Easy Book- Login</title>
  

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<style type="text/css">
  body {
    background: url('img/unikl2.jpg') no-repeat center center fixed;
    background-size: cover;
  }
</style>

</style>
<body >


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
                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                  </div>

                  <?php
	                  if (isset($_GET['success'])) {
                      if ($_GET['success']=="passwordchange"||$_GET['success']=="registered") {
                      echo '<div class="alert alert-success" role="alert">Please login using your new password<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                      }
                      
                    }
                  ?>
                  <form class="user" method="post" action="verify.php">
                    <div class="form-group">
                      <input name="email" type="email" class="form-control form-control-user" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Enter Email Address..." required autofocus >
                    </div>
                    <div class="form-group">
                      <input name="password" type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" required>
                    </div>

                  <button class="btn btn-primary btn-user btn-block" type="submit">Login</button>
                    
                  </form>
                  <hr>
                  <div class="text-center">

                    <a class="small" href="forgot-password/forgot_password.php">Forgot Password?</a>

                 

                  </div>
                  <div class="text-center">
                    <a class="small" href="register/">Create an Account!</a>
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
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

</body>


</html>




