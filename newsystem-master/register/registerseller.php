<?php 
if (isset($_POST['btn_register'])) {
	require '../dbconnect.php';

	$email=$_POST['txt_email'];
	$username=$_POST['txt_username'];
	$contact=$_POST['txt_contact'];
	$password=md5($_POST['txt_password']);
	$repeatpassword=md5($_POST['txt_repeatpassword']);

	// chech if the password are macthing if not return to register page
	if ($password!=$repeatpassword) {
		header('Location: index.php?error=notmatch');
	}
	$sql= "INSERT INTO user (email,username,password,accesslevel,contact) 
		   VALUES ('$email','$username','$password','buyer','$contact')";
	$qr=mysqli_query($conn,$sql);
	if (mysqli_error($conn)) {
		echo "Error".mysqli_error($conn);
		exit();
	}
	else
		header('Location: ../login.php?success=registered');

}			

 ?>