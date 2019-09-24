<?php                                         
error_reporting(E_ALL^E_NOTICE);
if(!defined('DB_SERVER')) define("DB_SERVER", getenv("DB_HOST"));
if(!defined('DB_USER')) define("DB_USER", getenv("DB_USERNAME"));
if(!defined('DB_PASS')) define("DB_PASS", getenv("DB_PASSWORD"));
if(!defined('DB_NAME')) define("DB_NAME", getenv("DB_DATABASE"));

$conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS,DB_NAME);
if (!$conn) {
    die('Could not connect: ' . mysqli_errno());
}
	mysqli_query($conn,  "SET character_set_results=utf8");
	mb_language('uni'); 
	mb_internal_encoding('UTF-8');
	mysqli_select_db($conn, DB_NAME)or die("Sorry, The Database Error.".mysqli_error());
	mysqli_query($conn,  "set names 'utf8'");
	mysqli_query($conn,  "SET character_set_results=utf8");
	date_default_timezone_set('Asia/Jakarta');  
//	mysqli_query($conn,  "SET SESSION time_zone = '+8:00'");
	$tgl_true = date('Y/m/d H:i:s', time());
	define("WEB_URL",'http://localhost/docker-crud/');
	require("main_function.php");
	
?>