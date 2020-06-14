<?php
include "connection.php";
session_start();
$actionID="";
$postData = file_get_contents("php://input");
$postdata = json_decode ($postData);
if(isset($_POST['action_id'])){
    $actionID = $_POST['action_id'];
}
else{
    $actionID = $postdata->action_id;
}
switch($actionID)
{
    //postovi
    case 'get_single_post':
        $post_id = $postdata->post_id;
        $sql = $conn->prepare( "exec post_single :post_id;");
        $sql->bindParam('post_id', $post_id);
        $sql->execute();
        $record = $sql->fetch();
        $rvsp = array();
        if($record != null){
            $post = new Post (
                $record['id'],
                $record['title'],
                $record['content'],
                $record['date'],
                $record['image'],
                $record['author_name']
            );
            array_push($rvsp, $post);
        }
        else{
            $rvsp['error'] = "greška";
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
        $sql = $conn->prepare( "exec post_new :post_title, :post_content, :post_image, :user_id;");
        $sql->bindParam('post_title', $post_title);
        $sql->bindParam('post_content', $post_content);
        $sql->bindParam('post_image', $post_image);
        $sql->bindParam('user_id', $user_id);
        $sql->execute();
    break;
    case 'delete_post':
        $post_id = $postdata->post_id;
        $sql = $conn->prepare("exec post_delete :post_id;");
        $sql->bindParam('post_id', $post_id);
        $sql->execute();
    break;
     case 'edit_post':
        $post_id = $postdata->post_id;
        $post_title = $postdata->new_title;
        $post_content = $postdata->new_content;
        $sql = $conn->prepare( "exec post_edit :post_id, :post_title, :post_content;");
        $sql->bindParam('post_id', $post_id);
        $sql->bindParam('post_title', $post_title);
        $sql->bindParam('post_content', $post_content);
        $sql->execute();
    break;
    //komentari
    case 'get_comments':
        $rvsp = array();
        if(isset($_POST['post_id'])){
            $post_id = $_POST['post_id'];
        }
        else if($postdata->post_id != null || $postdata->post_id != " "){
            $post_id = $postdata->post_id;
        }
        else{
            $error= 'greška';
            array_push($rvsp, $error);
        };
        $sql = $conn->prepare("exec comments_load :post_id;");
        $sql->bindParam('post_id', $post_id);
        $sql->execute();
        while($record = $sql->fetch(PDO::FETCH_BOTH)){
                $comment = new Comment (
                    $record['id'],
                    $record['content'],
                    $record['timestamp'],
                    $record['username']
            );
            array_push($rvsp, $comment);
        }
        echo json_encode($rvsp);
    break;
    case 'post_comment':
        $username = $postdata->username;
        $comment = $postdata->comment;
        $post_id = $postdata->post_id;
        $user_id = 0;
        $sql = $conn->prepare("exec comment_new :comment, :user_id , :username, :post_id;");
        $sql->bindParam('comment', $comment);
        $sql->bindParam('user_id', $user_id);
        $sql->bindParam('username', $username);
        $sql->bindParam('post_id', $post_id);
        $sql->execute();
    break;
    case 'delete_comment':
        $comment_id = $postdata->comment_id;
        $sql = $conn->prepare("exec comment_delete :comment_id;");
        $sql->bindParam('comment_id', $comment_id);
        $sql->execute();
    break;
    //useri
    case 'login':
        $email = $postdata->email;
        $password = $postdata->password;
        $sql = $conn->prepare("exec login :email, :password;"); 
        $sql->bindParam('email', $email);
        $sql->bindParam('password', $password);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        $rvsp = array();
        if($result != null){
            $user = new User(
                $result['id'],
                $result['username'],
                $result['firstname'],
                $result['lastname'],
                $result['email'],
                $result['birth_date'],
                $result['phonenumber'],
                $result['role'],
                $result['active']
            );
            $_SESSION['user']= $result['id'];
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
        $sql = $conn->prepare("exec user_register :username, :password, :firstname, :lastname, :email, :birth_date, :phone;");
        $sql->bindParam('username', $username);
        $sql->bindParam('password', $password);
        $sql->bindParam('firstname', $firstname);
        $sql->bindParam('lastname', $lastname);
        $sql->bindParam('email', $email);
        $sql->bindParam('birth_date', $birth_date);
        $sql->bindParam('phone', $phone);
        $sql->execute();
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
        $sql = $conn->prepare("exec user_active :user_id;");
        $sql->bindParam('user_id', $user_id);
        $sql->execute();
    break;
    case 'delete_pending_user':
        $user_id = $postdata->user_id;
        $sql = $conn->prepare("exec user_delete_pending :user_id;");
        $sql->bindParam('user_id', $user_id);
        $sql->execute();
    break;
    //primjeri koda za sql injection 
    case 'get_user':
        $name = $postdata->name;
        $sql = $conn->prepare("SELECT firstname, lastname FROM users WHERE username = :name;");
        $sql->bindParam('name', $name);
        $sql->execute();
        $record = $sql->fetch();
        $rvsp = array();
        if($record != null){
            $rvsp['firstname'] = $record['firstname'];
            $rvsp['lastname'] = $record['lastname'];
        }
        else{
            $rvsp = 'nema usera u bazi';
        }
        echo json_encode($rvsp);
    break;
    case 'login_sql':
        $email = $postdata->email;
        $password = $postdata->password;
        $sql = "SELECT * FROM users WHERE email = '".$email."' AND password = CONVERT(VARCHAR(50), HashBytes('MD5', '".$password."' ), 2);";
        $record = $conn->query($sql);
        $row = $record->fetch();
        $count = $record->rowCount();
        $rvsp = array();
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
    case 'login_vulnerable':
        $email = $postdata->email;
        $password = $postdata->password;
        $sql = "exec login_vulnerable '".$email."','".$password."'";
        $record = $conn->query($sql);
        $row = $record->fetch();
        $count = $record->rowCount();
        $rvsp = array();
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
}
?>