<?php  
 include "connection.php";
 $sql = "SELECT * FROM tbl_images ORDER BY id DESC";  
 $record = $conn->query($sql);
$rvsp = array();
 while($row = $record->fetch(PDO::FETCH_BOTH))  
    { 
        array_push($rvsp, $row['name']);
    }
echo json_encode($rvsp); 
 ?>  