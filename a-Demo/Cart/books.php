<?php
 

include "header.php";
 

include "model_books.php";
 

$show = 0;
 

$row = "";
 

$message = "";
 

$Object = new model_books();
 

$submitButton = "add";
 

if(isset($_GET['action'])){
 

  if($_GET["action"]=="add"){
 

	  $show = 1;
 

  }
 

  if($_GET["action"]=="edit"){
 

	  $Object->setid($_GET['id']);
 

	  $row=$Object->getbooks();
 

	  if(count($row) > 0){
 

		  $submitButton = "Update";
 

		  $show = 1;
 

	  }		
 

  }
 

  if($_GET["action"]=="delete"){
 

	  $Object->setid($_GET['id']);
 

	  if (!$Object->deletebooks())
 

		{
 

		$message = 'Error: ' . mysqli_error($con);
 

		}else{
 

			$message =  "1 books deleted";
 

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
 

			$message =  "1 books added";
 

		}
 

  }else{
 

	  $Object->PostData();
 

	  if (!$Object->commit())
 

		{
 

			$message = 'Error: ' . mysqli_error($con);
 

		}else{
 

			$message =  "1 books updated";
 

		}
 

  }
 

}
 

?>
 

<div id="mainwrapper" style="width:75%; margin: 0 auto; float:none;">
 

  <div id="maincontent" style="margin-left:0;>

    <div class="homepics">
 

	<h2><?php echo $message; ?></h2>
 

<?php if($show == 1){
 

?>
 

<form id="cart" method="post" action="books.php" enctype="multipart/form-data">
 

  <table width="300" style="margin-top:20px;">
 

	<tbody>
 

       <input type="hidden" name="id" value="<?php if($row){ echo $row[0]->getid();} ?>" />

       	<tr><td align="center">title</td><td><input type="textbox" name="title" value="<?php if($row){ echo $row[0]->gettitle();} ?>" /></td></tr>

       	<tr><td align="center">author</td><td><input type="textbox" name="author" value="<?php if($row){ echo $row[0]->getauthor();} ?>" /></td></tr>

       	<tr><td align="center">price</td><td><input type="textbox" name="price" value="<?php if($row){ echo $row[0]->getprice();} ?>" /></td></tr>

       	<tr><td align="center">photo</td><td><input type="file" name="photo" value="<?php if($row){ echo $row[0]->getphoto();} ?>" /></td></tr>

       	<tr><td align="center">categoryId</td><td><select name="categoryId"><?php echo $Object->dropdown("categoryId",($row?$row[0]->getcategoryId():'')); ?></select></td></tr>

       	<tr><td align="center">Description</td><td><textarea name=Description rows="4" cols="50"><?php if($row){ echo $row[0]->getDescription();} ?></textarea></td></tr>

	  <tr ><td colspan="2" align="center"><input type="submit"  name="submit" value="<?php echo $submitButton; ?>"></td></tr>
 

	</tbody>
 

  </table>
 

</form>
 

<?php }?>
 

<h1><span style="float:left;">books</span><a href="books.php?action=add">Add books</a></h1>
 

<?php
 

$targetpage = "books.php";
 

$tbl_name="`books`";
 

$Object->setid('');
 

if($text!=''){
 

	$Object->settitle($text);
 

	$sql = $Object->searchbooks(1);	

}else{
 

	$sql = $Object->getbooks(1);
 

}
 

include "pagination.php";
 

$result = mysqli_query($con,$sql);
 

 $books=$Object->buildarray($result);
 

?>
 

<table width="670">
 

<thead>
 

<tr>
 

       <th>id</th>

       <th>title</th>

       <th>author</th>

       <th>price</th>

       <th>photo</th>

       <th>Description</th>

</tr>
 

</thead>
 

  <tbody>
 

  <?php
 

for($i=0;$i<count($books);$i++){?>
 

  <tr>
 

       <td align="center"><?php echo $books[$i]->getid(); ?></td>

       <td align="center"><?php echo $books[$i]->gettitle(); ?></td>

       <td align="center"><?php echo $books[$i]->getauthor(); ?></td>

       <td align="center"><?php echo $books[$i]->getprice(); ?></td>

       <td align="center"><img src="images/<?php echo $books[$i]->getphoto(); ?>" height="42" width="42"> </td>

       <td align="center"><?php echo $books[$i]->getDescription(); ?></td>

  <td align="center"><a href="books.php?action=edit&id=<?php echo $books[$i]->getid();?>">Edit</a>
 

	  <a href="books.php?action=delete&id=<?php echo $books[$i]->getid();?>">delete</a>
 

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