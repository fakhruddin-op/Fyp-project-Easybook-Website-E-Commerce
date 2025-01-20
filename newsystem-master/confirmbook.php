<?php session_start();
if (empty($_SESSION['id'])) {
	header('Location: login.php?error=loginfirst');
	exit();
}
	$userid=$_SESSION['id'];
	$idbook=$_GET['idbook'];

	require 'dbconnect.php';
	// update buyer id 
	$sql= "UPDATE orderbook SET buyerid = '$userid' WHERE idbook ='$idbook' ";
	$qr=mysqli_query($conn,$sql);
	if (mysqli_error($conn)) {
		echo "Error: ".mysqli_error($conn);
		exit();
	}
	else
		header('Location: index.php?success=saved');
?>