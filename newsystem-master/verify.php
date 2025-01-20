<?php
session_start();

include "dbconnect.php";
$email=$_POST['email'];
$password=md5($_POST['password']);

$sql="SELECT id,username,email,password, accesslevel from user
WHERE email='$email'
AND password='$password'";

$rs=mysqli_query($conn,$sql);

if (mysqli_error($conn)) {
	echo "error".mysqli_error($conn);
}
if(mysqli_num_rows($rs)==1){
	$rec=mysqli_fetch_array($rs);
	//session data
	$_SESSION['sessionid']=session_id();
	$_SESSION['id']=$rec['id'];
	$_SESSION['username']=$rec['username'];
	$_SESSION['email']=$rec['email'];
	$_SESSION['accesslevel']=$rec['accesslevel'];

//redirect to dashboard
if($rec['accesslevel']=='admin'){
	header ("Location: admin/admindashboard.php");
}else if ($rec['accesslevel']=='seller'){
	header ("Location: seller/listing.php");	
}else if ($rec['accesslevel']=='buyer'){
	header ("Location: index.php");
}
echo "1 user founded";

echo " admin name ".$rec['username'];

exit();
}
else{
//redirect to login1.php
header ("Location: login.php?msg=Login failed");
echo "no user founded";
}
?>