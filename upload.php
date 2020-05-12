<?php  
include "connection.php";
$postData = file_get_contents("php://input");
$postdata = json_decode ($postData);
if(!empty($_FILES))  
{  
     $path = 'upload/' . $_FILES['file']['name'];  
     if(move_uploaded_file($_FILES['file']['tmp_name'], $path))  
     {  
        $image_path = $_FILES['file']['name'];
        //array_push($rvsp, $image_path);
        echo json_encode($image_path);
         /*//$post_title = $postdata->title;
         //$post_content = $postdata->content;
         //$user_id = $postdata->user_id;
         //$sql = "exec post_new '".$post_title."', '".$post_content."', ('lifeonadelay/upload/".$_FILES['file']['name']."'), ".$user_id." ;" ;
         //$record = $conn->query($sql);
         /* $sql = "INSERT INTO tbl_images(name) VALUES ('".$_FILES['file']['name']."')";
         if($record = $conn->query($sql))
         {  
              echo 'File Uploaded';  
         }  
         else  
         {  
              echo 'File Uploaded But not Saved';  
         } */ 
     }  
}
 else  
 {  
      echo 'Some Error';  
 }  
 ?> 