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
	public $image = '';
    public $author_name = '';
	public function __construct($id, $title,$content, $date, $image, $author_name)
	{
		$this->id = $id;
		$this->title = $title;
		$this->content =$content;
		$this->date = $date;
		$this->image = $image;
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
	public $id = '';
	public $username = '';
	public $firstname = '';
	public $lastname = '';
	public $email = '';
	public $birth_date = '';
	public $phonenumber = '';
	public $role ='';
	public $active = '';
	public function __construct($id ,$username, $firstname, $lastname, $email, $birth_date, $phonenumber, $role, $active)
	{
		$this->id = $id;
		$this->username = $username;
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->email = $email;
		$this->birth_date = $birth_date;
		$this->phonenumber = $phonenumber;
		$this->role = $role;
		$this->active = $active;

	}
}
?>