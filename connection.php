<?php
include 'classes.php';
$config = new Configuration();
try {  
    $conn = new PDO("sqlsrv:
                    Server= $config->server;
                    Database=$config->db;
                    ConnectionPooling=0", 
                    "$config->username", 
                    "$config->password");   
	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
}  

catch( PDOException $e ) {  
		echo $e;  
		die(); 
	}  
//echo "Connected to SQL Server\n";  
?>