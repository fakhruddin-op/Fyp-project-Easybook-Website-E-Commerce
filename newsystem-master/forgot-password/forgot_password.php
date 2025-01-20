<?php session_start();
include 'sendVerification.php';
require '../dbconnect.php';

if (isset($_POST['btn_resetpassword'])) {
    $email=$_POST['txt_email'];

    // find match in DB
    $querry=mysqli_query($conn,"SELECT * FROM user WHERE email= '$email'");
    if($querry==false){
      echo "Failed to find user<br>";
      echo "SQL error :".mysqli_error($conn);
    }
    $record = mysqli_fetch_array($querry);
    $_SESSION['email']=$record['email'];
    $_SESSION['id']=$record['id'];


     // delete request record 
    $deleterecord= mysqli_query($conn,"DELETE FROM forgotpassword WHERE useremail = '$email'");
    if($deleterecord==false){
      echo "Failed to delete request reset password record<br>";
      echo "SQL error :".mysqli_error($conn);
      exit();
    }
    else{
      header('Location: sendVerification.php?email='.$email);
      exit();
    }
    
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Easy Book - Forgot Password</title>

  <!-- Custom fonts for this template-->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">

</head>
<style type="text/css">
  body {
    background: linear-gradient(to bottom right, #9966ff 0%, #ff66ff 100%)
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
                    <h1 class="h4 text-gray-900 mb-4">Forgot Your Password?</h1>
                     <p class="mb-4">We get it, stuff happens. Just enter your email address below and we'll send you a code to reset your password!</p>

                  </div>
                  <form class="user" method="POST" action="<?=$_SERVER['PHP_SELF'];?>">
                    <div class="form-group">
                      <input type="text" name="txt_email" class="form-control form-control-user" placeholder="Enter Your email" required autofocus>
                    </div>
                    <input type="submit" name="btn_resetpassword" class="btn btn-primary btn-user btn-block" value="Reset Password"> 

                  </form>
                  <hr>
                  <div class="text-center">
                    <a class="small" href="../register">Not register yet? Register here!</a>
                  </div>
                  <div class="text-center">
                    <a class="small" href="../index.php">Already have an account? Login!</a>
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
