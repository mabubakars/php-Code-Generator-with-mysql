<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'cart';
//$db = &new MySQL($host,$user,$pass,$name);

$con = mysqli_connect($host, $user, $pass,$name);
if (mysqli_connect_errno()) {
    die('Could not connect: ' . mysqli_connect_error());
}
?>