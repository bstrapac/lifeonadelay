<?php
include "connection.php";
session_start();
$actionID="";
$postData = file_get_contents("php://input");
$postdata = json_decode ($postData);

$actionID = $postdata->action_id;

switch($actionID)
{
    //postovi
    case 'get_single_post':
        $post_id = $postdata->post_id;
        $sql = "exec post_single ".$post_id.";" ;
        $record = $conn->query($sql);
        $row = $record->fetch();
        $count = $record->rowCount();
        $rvsp = array();
        if($count < 0){
            $post = new Post (
                $row['id'],
                $row['title'],
                $row['content'],
                $row['date'],
                $row['image'],
                $row['author_name']
            );
            array_push($rvsp, $post);
        }
        else{
            $rvsp['error'] = $row;
            $rvsp['count'] = $count;
        }
        echo json_encode($rvsp);
    break;
    /*
    case 'get_all_user_posts':
        $user_id = $postdata->user_id;
        $sql = "exec post_load_spec ".$user_id.";" ;
        $record = $conn->query($sql);
        $row = $record->fetch();
        $count = $record->rowCount();
        $rvsp = array();
        if($count < 0){
            $post = new Post (
                $row['id'],
                $row['title'],
                $row['content'],
                $row['date'],
                $row['image'],
                $row['author_name']
            );
            array_push($rvsp, $post);
        }
        else{
            $rvsp['error'] = $row;
            $rvsp['count'] = $count;
        }
        echo json_encode($rvsp);
    break;*/
    case 'add_post':
        $post_title = $postdata->title;
        $post_content = $postdata->content;
        $post_image = $postdata->image;
        $user_id = $postdata->user_id;
        $sql = "exec post_new '".$post_title."', '".$post_content."','".$post_image."' ,'".$user_id."';" ;
        $record = $conn->query($sql);
    break;
    case 'delete_post':
        $post_id = $postdata->post_id;
        $sql = "exec post_delete ".$post_id.";" ;
        $record = $conn->query($sql);
    break;
     case 'edit_post':
        $post_id = $postdata->post_id;
        $post_title = $postdata->new_title;
        $post_content = $postdata->new_content;
        $sql = "exec post_edit ".$post_id.",'".$post_title."','".$post_content."';" ;
        $record = $conn->query($sql);
    break;
    //komentari
    case 'get_comments':
        $post_id = $postdata->post_id;
        $sql = "exec comments_load ".$post_id.";" ;
        $record = $conn->query($sql);
        $row = $record->fetch();
        $rvsp = array();
        while($row=$record->fetch(PDO::FETCH_BOTH))
        {
            $comment = new Comment (
                $row['id'],
                $row['content'],
                $row['timestamp'],
                $row['username']
            );
            array_push($rvsp, $comment);
        }
        echo json_encode($rvsp);
    break;
    case 'post_comment':
        $username = $postdata->username;
        $comment = $postdata->comment;
        $post_id = $postdata->post_id;
        $sql = "exec comment_new '".$comment."', 0 , '".$username."', ".$post_id.";";
        $record = $conn->query($sql);
    break;
    case 'delete_comment':
        $comment_id = $postdata->comment_id;
        $sql = "exec comment_delete ".$comment_id.";";
        $record = $conn->query($sql);
    break;
    //useri
    case 'login':
        $email = $postdata->email;
        $password = $postdata->password;
        $sql = "exec login '".$email."', '".$password."'";
        $record = $conn->query($sql);
        $row = $record->fetch();
        $count = $record->rowCount();
        $rvsp = array();
        //echo $count;
        if($count != 0){
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
            $_SESSION['user']= $row['id'];
            $logged_in = 1;
             array_push($rvsp, $user);
             array_push($rvsp, $logged_in);
        }
        else{
            $error = 'nema usera u bazi';
            $logged_in = 0;
            array_push($rvsp, $error);
            array_push($rvsp, $logged_in);
        }
        echo json_encode($rvsp);
    break;
    case 'logout':
        session_destroy();
    break;
    case 'register':
        $username = $postdata->username;
        $firstname = $postdata->firstname;
        $lastname = $postdata->lastname;
        $email = $postdata->email;
        $phone = $postdata->phone;
        $password = $postdata->password;
        $birth_date = $postdata->birth_date;
        $sql = "exec user_register '".$username."', '".$password."', '".$firstname."', '".$lastname."','".$email."', '".$birth_date."', '".$phone."';";
        $record = $conn->query($sql);
    break;
    case 'check_logged_in':
		if( isset($_SESSION['user'])){
			echo 1;
		}
		else{
			echo 0;
		}
	break;
    case 'approve_user':
        $user_id = $postdata->user_id;
        $sql = "exec user_active ".$user_id.";";
        $record = $conn->query($sql);
    break;
    case 'delete_pending_user':
        $user_id = $postdata->user_id;
        $sql = "exec user_delete_pending ".$user_id.";";
        $record = $conn->query($sql);
    break;
    //pomoćne funkcije
}
?>