<?php
session_start();//mesti di atas
include "header.template.php";
//searchactivity.php
if (isset($_GET['katakunci'])){
	$key=$_GET['katakunci'];
}else{
	$key="";
}
$sql="SELECT idbook,isbn,bookname,bookcodesubject
	FROM orderbook
	WHERE bookname
	 LIKE '%$key%' ";
include "dbconnect.php";
$rs=mysqli_query($conn, $sql);
if(mysqli_num_rows($rs)==0){
	echo "No record found";
}else{//paparan rekod
?>
<table class="table">
	<tr>
		<td>idbook</td>
		<td>ISBN</td>
		<td>Book Name</td>
		<td>Book Code Subject</td>
		
	</tr>
	<?php
		while($rec=mysqli_fetch_array($rs)){
			echo "<tr><td>";
			$id=$rec['idbook'];
			if(isset($_SESSION['accesslevel']) &&
				$_SESSION['accesslevel']=='admin'){
				echo "<a href='editactivity.php?id=$id'>
				<i class='fa fa-edit'></i></a> ".
				"<a href='#'onclick='confirmdelete($id)'>
				<i class='fa fa-trash danger'  style='color:red'></i></a> ";
			}//end display for admin
			echo $rec['idbook'];
			echo "</td>";
			echo "<td>".$rec['isbn']."</td>";
			echo "<td>".$rec['bookname']."</td>";
			echo "<td>".$rec['bookcodesubject']."</td>";
			
			echo "</tr>";
		}
	?>

</table>
<script>
	function confirmdelete(id){
		var answer=confirm("Are u sure to delete?");
		if (answer==true){
			//redirect
			window.location.href = "deleteactivity.php?id="+id;
		}
	}
</script>
<?php
}
include "footer.template.php";
?>