<?php
require  "../dbconnect.php";
$bookid=$_GET['bookid']; 
$sql="DELETE FROM orderbook 
	  WHERE idbook = '$bookid' ";
$q=mysqli_query($conn,$sql);
if ($q==true){
echo "The record for $bookid has been deleted";
header('Location: admindashboard.php?success=deleted');
exit();
}
else{
echo "Fail to delete record for $bookid";
echo mysqli_error($conn);
}
?>

