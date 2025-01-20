<?php session_start();
include "../dbconnect.php";
$sql= "SELECT orderbook.*,user.contact,user.username FROM orderbook 
       JOIN user 
       ON orderbook.ownerid=user.id WHERE buyerid= 0";
$qr=mysqli_query($conn,$sql);
if (mysqli_error($conn)) {
  echo "error".mysqli_error($conn);
  exit();
}
 
    
    
include "header.template.php"
?>
<div class="col-xl-6 col-lg-6 col-md-9">
	<?php
while ($rec=mysqli_fetch_array($qr)) {
	?>
	<div class="card">
	<div class="card-header">
		Book Information
	</div>
	<div class="card-body">
		<h5 class="card-title">Book Title : <?=$rec['contact']?></h5>
		<p class="card-text">Seller Name : <?=$rec['username']?></p>
		<p class="card-text">Price : <?=$rec['price']?></p>
		<p class="card-text">Code Book : <?=$rec['bookcodesubject']?></p>
		
		<a href="admindashboard.php"><button type="button" class="btn btn-danger">Back</button></a>

	</div>
		<?php
}
?>
	</div>
</div>

<?php
include "footer.template.php";
?>