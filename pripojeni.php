<?php
///@ob_start();
//session_start();

/*if(!isset($_COOKIE["PHPSESSID"]))
{*/
  @session_start();
//}

$servername = "localhost";
$username = "root";
$password = "";
$databaze = "hudba";

// Create connection
$conn = new mysqli($servername, $username, $password,$databaze);
//mysqli_query("SET COLLATION_CONNECTION='utf8_general_ci'");
// Check connection
if ($conn->connect_error) {

die("Connection failed: " . $conn->connect_error);
}
else{ 
//echo "Connected successfully";
//@ob_start();
//session_start();
$conn->set_charset("utf8");
$_SESSION['conn']=$conn;
}
//session_start();
?>