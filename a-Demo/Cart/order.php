<?php
 

include "header.php";
 

include "model_order.php";
 

$show = 0;
 

$row = "";
 

$message = "";
 

$Object = new model_order();
 

$submitButton = "add";
 

if(isset($_GET['action'])){
 

  if($_GET["action"]=="add"){
 

	  $show = 1;
 

  }
 

  if($_GET["action"]=="edit"){
 

	  $Object->setid($_GET['id']);
 

	  $row=$Object->getorder();
 

	  if(count($row) > 0){
 

		  $submitButton = "Update";
 

		  $show = 1;
 

	  }		
 

  }
 

  if($_GET["action"]=="delete"){
 

	  $Object->setid($_GET['id']);
 

	  if (!$Object->deleteorder())
 

		{
 

		$message = 'Error: ' . mysqli_error($con);
 

		}else{
 

			$message =  "1 order deleted";
 

		}
 

 
 

  }
 

}
 

if(isset($_POST['submit'])){
 

  if($_POST['submit']=='add'){
 

	  $Object->PostData();
 

	  if (!$Object->commit()){
 

			$message = 'Error: ' . mysqli_error($con);
 

		}
 

		else{
 

			$message =  "1 order added";
 

		}
 

  }else{
 

	  $Object->PostData();
 

	  if (!$Object->commit())
 

		{
 

			$message = 'Error: ' . mysqli_error($con);
 

		}else{
 

			$message =  "1 order updated";
 

		}
 

  }
 

}
 

?>
 
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.css" />
<script type="text/javascript" src="js/datetimepicker_css.js"></script>
<div id="mainwrapper" style="width:75%; margin: 0 auto; float:none;">
 

  <div id="maincontent" style="margin-left:0;>

    <div class="homepics">
 

	<h2><?php echo $message; ?></h2>
 

<?php if($show == 1){
 

?>
 

<form id="cart" method="post" action="order.php" enctype="multipart/form-data">
 

  <table width="300" style="margin-top:20px;">
 

	<tbody>
 

       <input type="hidden" name="id" value="<?php if($row){ echo $row[0]->getid();} ?>" />

       	<tr><td align="center">name</td><td><input type="textbox" name="name" value="<?php if($row){ echo $row[0]->getname();} ?>" /></td></tr>

       	<tr><td align="center">cardno</td><td><input type="textbox" name="cardno" value="<?php if($row){ echo $row[0]->getcardno();} ?>" /></td></tr>

       	<tr><td align="center">address</td><td><textarea name=address rows="4" cols="50"><?php if($row){ echo $row[0]->getaddress();} ?></textarea></td></tr>

       	<tr><td align="center">deliveraddress</td><td><textarea name=deliveraddress rows="4" cols="50"><?php if($row){ echo $row[0]->getdeliveraddress();} ?></textarea></td></tr>

       	<tr><td align="center">datetime</td><td><input type="textbox" onfocus="javascript:NewCssCal ('inputField','yyyyMMdd','dropdown',true,'24',true)" id = "inputField" name="datetime" value="<?php if($row){ echo $row[0]->getdatetime();} ?>"/></td></tr>

       	<tr><td align="center">deliverd</td><td><input type="checkbox" name="deliverd" value="<?php if($row){ echo $row[0]->getdeliverd();} ?>" <?php echo (($row?$row[0]->getdeliverd():'')==1?'checked':'') ?> /></td></tr>

	  <tr ><td colspan="2" align="center"><input type="submit"  name="submit" value="<?php echo $submitButton; ?>"></td></tr>
 

	</tbody>
 

  </table>
 

</form>
 

<?php }?>
 

<h1><span style="float:left;">order</span><a href="order.php?action=add">Add order</a></h1>
 

<?php
 

$targetpage = "order.php";
 

$tbl_name="`order`";
 

$Object->setid('');
 

if($text!=''){
 

	$Object->settitle($text);
 

	$sql = $Object->searchorder(1);
 

}else{
 

	$sql = $Object->getorder(1);
 

}
 

include "pagination.php";
 

$result = mysqli_query($con,$sql);
 

 $order=$Object->buildarray($result);
 

?>
 

<table width="670">
 

<thead>
 

<tr>
 

       <th>id</th>

       <th>name</th>

       <th>cardno</th>

       <th>address</th>

       <th>deliveraddress</th>

       <th>datetime</th>

       <th>deliverd</th>

</tr>
 

</thead>
 

  <tbody>
 

  <?php
 

for($i=0;$i<count($order);$i++){?>
 

  <tr>
 

       <td align="center"><?php echo $order[$i]->getid(); ?></td>

       <td align="center"><?php echo $order[$i]->getname(); ?></td>

       <td align="center"><?php echo $order[$i]->getcardno(); ?></td>

       <td align="center"><?php echo $order[$i]->getaddress(); ?></td>

       <td align="center"><?php echo $order[$i]->getdeliveraddress(); ?></td>

       <td align="center"><?php echo $order[$i]->getdatetime(); ?></td>

       <td align="center"><?php echo $order[$i]->getdeliverd(); ?></td>

  <td align="center"><a href="order.php?action=edit&id=<?php echo $order[$i]->getid();?>">Edit</a>
 

	  <a href="order.php?action=delete&id=<?php echo $order[$i]->getid();?>">delete</a>
 

  </td>
 

  </tr>
 

<?php
 

}
 

mysqli_close($con);
 

?>
 

</tbody>
 

</table>
 

<?php echo $pagination; ?>
 

</div>
 

</div>
 

</div>
 

<?php include "footer.php"; ?>