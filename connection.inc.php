<?php
session_start();
if(!isset($_SESSION['config'])){
header('location:index.php');
}
$config = $_SESSION['config'];
$server = $config['server'];
$username = $config['username'];
$password = $config['password'];
$dbname = $config['dbname'];
$con = mysqli_connect($server,$username,$password,$dbname);
if (mysqli_connect_errno())
  {
	echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
  }
 ?>