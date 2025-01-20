<?php
//logout.php
session_start();
if (isset($_SESSION['sessionid'])){
session_destroy();
//this to destroy all session info
header("Location: index.php?msg=User Loged Out");
}
else{
	header("Location: index.php?msg=User Loged Out");
}
?>
