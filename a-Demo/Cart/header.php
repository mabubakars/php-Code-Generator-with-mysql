<?php
// Include database connection
require_once('inc/global.inc.php');
// Include functions
require_once('inc/functions.inc.php');
session_start();
$text= "";
	if(isset($_POST["stext"])){
		$text = $_POST["stext"];
		$_SESSION["search"] = $text;
	}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Book Store</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<script src="js/jquery-2.0.2.min.js"></script>
</head>
<body>
<div id="maincontainer">
<div id="header">
  <div class="logo"> <img src="images/logo.gif" alt="logo" border="0" height="70" width="280"> </div>
  <div class="hdrR"> <a href="admin/index.php">Admin Panel</a><br>
    <br>
    <div class="searchbox">
      <form method="post" action="">
        <input name="page" value="1" type="hidden">
        <input name="stext" size="16" class="search" type="text" value="<?php if(isset($_SESSION['search'])){ echo $_SESSION['search'];}?>">
        <input name="Submit" value="Search" class="sub" type="submit">
      </form>
    </div>
  </div>
  <div class="clr"></div>
  <div id="topnav">
    <ul id="navlist">
      <li><a href="index.php">Home</a></li>
	  <li><a href="bookstore.php">Book Store</a></li>
	  <li><a href="order.php">Purchase Books</a></li>
    </ul>
  </div>
</div>
