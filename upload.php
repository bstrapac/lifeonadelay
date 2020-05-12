<?php  
include "connection.php";
$postData = file_get_contents("php://input");
$postdata = json_decode ($postData);
if(!empty($_FILES))  
{  
     $path = 'upload/' . $_FILES['file']['name'];  
     if(move_uploaded_file($_FILES['file']['tmp_name'], $path))  
     {  
        echo ( $_FILES['file']['name']);
     }  
}
else  
{  
     echo 'Some Error';  
}  
?> 