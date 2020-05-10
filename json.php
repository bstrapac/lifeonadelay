<?php 
ini_set('memory_limit', '2048M');
header('Content-type: text/json');
header('Content-type: application/json; charset=utf-8');
include "connection.php";

$jsonID="";
if(isset($_GET['json_id']))
{
	$jsonID=$_GET['json_id'];
}
$postData = file_get_contents("php://input");
$postdata = json_decode ($postData);
if(isset($_POST['json_id']))
{
    $jsonID = $postdata->json_id;
}
$json=array();
switch($jsonID)
{
    case 'get_last_posts':
        $sql="exec posts_last";
        $record=$conn->query($sql);
        while($row=$record->fetch(PDO::FETCH_BOTH))
        {
        $post=new Post(
            $row['id'],
            $row['title'],
            $row['content'],
            $row['date'],
            $row['author_name']
        );
        array_push($json,$post);
        }
    break;
    case 'get_all_posts':
        $sql = "exec post_load_all;";
        $record = $conn->query($sql);
        while($row = $record->fetch(PDO::FETCH_BOTH))
        {
            $post=new Post(
                $row['id'],
                $row['title'],
                $row['content'],
                $row['date'],
                $row['author_name']
            );
            array_push($json,$post);
        }
    break;
    case 'get_pending_users':
        $sql = "exec user_nonactive;";
        $record = $conn->query($sql);
        while($row = $record->fetch(PDO::FETCH_BOTH))
        {
            $user = new User(
                $row['id'],
                $row['username'],
                $row['firstname'],
                $row['lastname'],
                $row['email'],
                $row['birth_date'],
                $row['phonenumber'],
                $row['role'],
                $row['active']
            );
            array_push($json,$user);
        }
    break;
}
echo json_encode($json)
?>