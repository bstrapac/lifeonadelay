<?php
class Configuration 
{
	public $server = 'STRAPAC';
	public $db = 'blogsite';
	public $username = 'blogsite';
	public $password = 'blogsite';
}
class Post
{
	public $id = '';
	public $title ='';
	public $content ='';
    public $date= '';
    public $author_name = '';
	public function __construct($id, $title,$content, $date, $author_name)
	{
		$this->id = $id;
		$this->title = $title;
		$this->content =$content;
        $this->date = $date;
        $this->author_name = $author_name;
	}	
}
class Comment
{
	public $id = '';
	public $content ='';
    public $date= '';
    public $username = '';
	public function __construct($id, $content, $date, $username)
	{
		$this->id= $id;
		$this->content =$content;
        $this->date = $date;
        $this->username = $username;
	}	
} 
class User
{
	public $user_id = '';
	public $username = '';
	public $firstname = '';
	public $lastname = '';
	public $user_role ='';
	public $user_active = '';
	public function __construct($user_id ,$username, $firstname, $lastname, $user_role, $user_active)
	{
		$this->user_id = $user_id;
		$this->username = $username;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->user_role = $user_role;
		$this->user_active = $user_active;

	}
}
?>