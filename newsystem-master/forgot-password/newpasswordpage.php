<?php session_start(); 
$email=$_SESSION['email'];

if (empty($email)) {
  header('Location: index.php?error=notauthorised');
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

  <title>KUIS e-voting - Set new Password</title>

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
    <div class="row">
      <!-- for empty space on the left -->
      <div class="col"> </div>
      <div class="col-xl-6"> <br>    
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-header py-3" align="center">
            <h5 class="m-0  font-weight-bold text-primary">Please set your new password</h5>
          </div>
          <div class="card-body p-5" >        
            <?php 
                if (isset($_GET['error'])) {
                  if ($_GET['error']=="emptyfield") {
                    echo '<div class="alert alert-danger" role="alert">Please set your new password</div> ';
                  }
                  elseif (isset($_GET['error'])=="notsame") {
                    echo '<div class="alert alert-danger" role="alert">Your password are not same</div> ';
                  }
                }
               ?>
          <!-- form start -->
          <form class="user" method="POST" action="newpasswordpage.php">

            <div class="form-group row">
              <label class="col-sm-4 col-form-label ">New password: </label>
              <div class="col-sm-8">
                <input name="txt_newpassword" type="password" class="form-control form-control-user" id="newpassword" placeholder="Please insert your new password" required autofocus>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Repeat Password: </label>
              <div class="col-sm-8">
                <input name="txt_repeatpassword" type="password" class="form-control form-control-user" id="passwordrepeat" placeholder="Repeat your new password" required autofocus>
              </div>
            </div>

            <hr>
            <div align="center">
              <div class="form-group col-xl-6">
               <input class="btn btn-primary btn-user btn-block" type="submit" name="btn_submit_newpassword" value="Change password">
              </div>
            </div>
          </form>
          </div>
        </div>
      </div>
      <!-- for empty space on the right -->
      <div class="col"> </div>
    </div>
  </div>
</body>
</html>
<?php 
if (isset($_POST['btn_submit_newpassword'])) {
require "../dbconnect.php";

  $newpassword=md5($_POST['txt_newpassword']);
  $repeatpassword =md5($_POST['txt_repeatpassword']);
  // check if password is inserted if not return error message
  if (empty($newpassword) || empty($repeatpassword)) {
    header('Location: newpasswordpage.php?error=emptyfield');
  }
  elseif ($newpassword!=$repeatpassword) {
    header('Location: newpasswordpage.php?error=notsame');
  }
  elseif ($newpassword==$repeatpassword) {
    // update password on DB
    $qr=mysqli_query($conn,"UPDATE user SET password ='$newpassword' WHERE email ='$email' ");
    if($qr==false){
      echo "Failed update new password <br>";
      echo "SQL error :".mysqli_error($conn);
    }
    else{
      session_destroy();
      header('Location: ../login.php?success=passwordchange');
      exit();
    }
  }
}
