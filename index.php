<?php
if(isset($_POST['submit'])){
	$server = $_POST['servername'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$config = array('server'=>$server,
					'username'=>$username,
					'password'=>$password,
					'dbname'=>'');
	session_start();
	$_SESSION['config']=$config;
	$config = $_SESSION['config'];
	header('location:dbSelection.php');
}
else{
session_start();
unset($_SESSION['config']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<title>Internet Dreams</title>
<link title="default" media="screen" type="text/css" href="css/screen.css" rel="stylesheet">
</head>
<body id="login-bg"> 
<div id="login-holder">

	<div id="logo-login">
		<a href="index.html"><img width="156" height="40" alt="" src="images/shared/logo.png"></a>
	</div>
	
	<div class="clear"></div>
	
	<div id="loginbox">
	
	<div id="login-inner">
		<form action="" method="post">
		<table cellspacing="0" cellpadding="0" border="0">
		<tbody>
		<tr>
			<th>server</th>
			<td><input type="text" name="servername" class="login-inp"></td>
		</tr>
		<tr>
			<th>Username</th>
			<td><input type="text" name="username" class="login-inp"></td>
		</tr>
		<tr>
			<th>Password</th>
			<td><input type="password" name= "password" class="login-inp" onfocus="this.value=''" value=""></td>
		</tr>
		<tr>
			<th></th>
			<td><input type="submit" name= "submit" class="submit-login"></td>
		</tr>
		</tbody></table>
		</form>
	</div>
	<div class="clear"></div>
 </div>
</div>

</body></html>