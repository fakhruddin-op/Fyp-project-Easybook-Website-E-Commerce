<?php 

if (isset($_GET['email'])) {
	require '../dbconnect.php';

	$email=$_GET['email'];
	// create pin 
	$pin=random_int(10000, 99999);
	$hashpin=md5($pin);

	 // message in email
	$messagesubject="Easy Book Reset Password Request";
	$headers = "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$message ="This is your code for verify your request";
	$message .="<br>Code: "."<b>".$pin."</b><br>";
	$message .="<br>If your not request to reset password, please ignore this email";

	    	// send email 
			// check whether email has been send and return to login page with message
			if (mail($email, $messagesubject, $message,$headers)) {
				$query= mysqli_query($conn,"INSERT INTO forgotpassword (useremail,reset_token) VALUES ('$email','$hashpin')");
				if($query==false){
			      echo "Failed to load reset password request<br>";
			      echo "SQL error :".mysqli_error($conn); 
			      exit();
			    }
			    else
			      header('Location: requestverification.php?success=emailsended');
			}
			else
			  header('Location: forgot_password.php');
}

 
 ?>
