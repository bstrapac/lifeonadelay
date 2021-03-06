<?php 
ini_set('memory_limit', '2048M');
header('Content-type: text/json');
header('Content-type: application/json; charset=utf-8');
include "connection.php";
session_start();

$jsonID="";
$postData = file_get_contents("php://input");
$postdata = json_decode ($postData);
if(isset($_GET['json_id']))
{
	$jsonID=$_GET['json_id'];
}
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
                $row['image'],
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
                $row['image'],
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
    case 'get_users':
        $sql = "exec user_all;";
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
    case 'get_user':
        if(isset($_GET['name'])){
            $name = $_GET['name'];
        };
        $sql = $conn->prepare("SELECT firstname, lastname FROM users WHERE username = :name;");
        $sql->bindParam('name', $name);
        $sql->execute();
        $record = $sql->fetch();
        if($record != null){
            $json['firstname'] = $record['firstname'];
            $json['lastname'] = $record['lastname'];
        }
        else{
            $json = 'greška';
        }
    break;
}
echo json_encode($json)
?>