<?php
   $servername="localhost";
   $username="root";
   $password="";
   $dbname="newsystem";

   $conn=mysqli_connect($servername,
                        $username,
                        $password,
                        $dbname);
    if (!$conn){//cannot established
        die ("DB Connection failed<br>");
        }  
        else{//connected to DB
         //   echo "DB Connection established
         //   <br>";
            }
        ?>