<?php
include 'connection.inc.php';

$sql = "select `TABLE_SCHEMA` from  `information_schema`.`COLUMNS` where `TABLE_SCHEMA`!='information_schema' and `TABLE_SCHEMA`!='performance_schema' and `TABLE_SCHEMA`!='webauth' and `TABLE_SCHEMA`!='phpmyadmin' and `TABLE_SCHEMA`!='mysql' group by TABLE_SCHEMA";
$result = mysqli_query($con,$sql);
?>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<title>Internet Dreams</title>
<link title="default" media="screen" type="text/css" href="css/jquery-ui.css" rel="stylesheet">
<link title="default" media="screen" type="text/css" href="css/screen.css" rel="stylesheet">
<script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/db.js"></script>
</head>
<body>
<div id="content-outer">
<div id="content"> 
	<div id="page-heading">
		<h1 style="width:120px;">Add product</h1>
		<h3 id= "generate" style="width:180px; cursor:pointer;">Generate all selected</h3>
		<a href="#" id="addtable">Add table</a>
	</div>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" id="content-table">
	<tbody><tr>
		<th class="sized" rowspan="3"><img width="20" height="300" alt="" src="images/shared/side_shadowleft.jpg"></th>
		<th class="topleft"></th>
		<td id="tbl-border-top">&nbsp;</td>
		<th class="topright"></th>
		<th class="sized" rowspan="3"><img width="20" height="300" alt="" src="images/shared/side_shadowright.jpg"></th>
	</tr>
	<tr>
		<td id="tbl-border-left"></td>
		<td>
		<div id="content-table-inner">
		
			<div id="table-content">
			<a href="#" id="creatRelation">Creat Relation</a></br>
			<select id = "db" name="db" >
			<option value="0">Select</option>
			<?php
			while($row = mysqli_fetch_array($result)){
			$db = $row['TABLE_SCHEMA'];
			?>
			<option value="<?php echo $db;?>"><?php echo $db;?></option>
			<?php } ?>
			</select>
			<div id = "show">
				<form action="" id="mainform">
				</form>
			</div>
			</div>
			<div class="clear"></div>
		 
		</div>
		</td>
		<td id="tbl-border-right"></td>
	</tr>
	<tr>
		<th class="sized bottomleft"></th>
		<td id="tbl-border-bottom">&nbsp;</td>
		<th class="sized bottomright"></th>
	</tr>
	</tbody></table>
	<div class="clear">&nbsp;</div>
	<div id="results">&nbsp;</div>
	<div id="tooltip" style="wdth:100px;"></div>
</div>

<div class="clear">&nbsp;</div>
</div>
</body></html>