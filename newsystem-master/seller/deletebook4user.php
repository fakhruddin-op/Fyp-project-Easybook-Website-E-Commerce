<?php
require  "../dbconnect.php";
$bookid=$_GET['bookid'];
$sql="delete from orderbook
 where idbook='$bookid' ";
$q=mysqli_query($conn,$sql);
if ($q==true){
echo "The record for $bookid has been deleted";
header('Location: listing.php?success=deleted');
exit();
}
else{
echo "Fail to delete record for $bookid";
echo mysqli_error($conn);
}
?>