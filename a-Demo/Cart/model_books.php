<?php
  class model_books{

	function __construct() {

	}

	private $relations;

	private $id;

	private $title;

	private $author;

	private $price;

	private $photo;

	private $categoryId;

	private $Description;

	public function getid(){

		return $this->id;

	}

	public function setid($value){

		$this->id = $value;

	}

	public function gettitle(){

		return $this->title;

	}

	public function settitle($value){

		$this->title = $value;

	}

	public function getauthor(){

		return $this->author;

	}

	public function setauthor($value){

		$this->author = $value;

	}

	public function getprice(){

		return $this->price;

	}

	public function setprice($value){

		$this->price = $value;

	}

	public function getphoto(){

		return $this->photo;

	}

	public function setphoto($value){

		$this->photo = $value;

	}

	public function getcategoryId(){

		return $this->categoryId;

	}

	public function setcategoryId($value){

		$this->categoryId = $value;

	}

	public function getDescription(){

		return $this->Description;

	}

	public function setDescription($value){

		$this->Description = $value;

	}

	public function getrelations(){

 		$relation=array(

			0=>array('table'=>'category','field'=>'categoryId','displayField'=>'category')

		);

	return $relation;

	}

	private function setbooksobject($row){

		$this->setid($row[0]);

		$this->settitle($row[1]);

		$this->setauthor($row[2]);

		$this->setprice($row[3]);

		$this->setphoto($row[4]);

		$this->setcategoryId($row[5]);

		$this->setDescription($row[6]);

	}

	public function buildarray($result){

		$books= array();

		if($result){

			if(mysqli_num_rows($result)>0){

				while($row = mysqli_fetch_array($result)){

					$std = new model_books();

					$std->setbooksobject($row);

					$books[] = $std;

				}

			}

		}

		return $books;

	}

	public function dropdown($field="",$value=""){

		global $con;

		$sql = "";

		$relate="";

		$related=$this->getrelations();

		foreach($related as $row){

			if($row["field"]==$field){

				$sql = "select id,".$row["displayField"]." from ".$row["table"]."";

				$relate=$row;

			}

		}

 		$option=array();

		$option[]='<option value="0">Select</option>';

		$result = mysqli_query($con,$sql);

		if($result){

			if(mysqli_num_rows($result)>0){

				$select="";

				while($row = mysqli_fetch_array($result)){

					if($value==$row["id"]){

						$select="selected";

					}

					$option[]='<option value="'.$row['id'].'"'.$select.' >'.$row[$relate['displayField']].'</option>';

				}

			}

		}

	return join("",$option);

	}

	public function fileUpload($file){

		global $con;

		$name1 = $file ['name'];

		$type = $file ['type'];

		$size = $file ['size'];

		$tmppath = $file ['tmp_name'];

		$extension = explode(".", $name1);

		$extension=$extension[1];

		$name1=$this->getid().".".$extension;

		if(move_uploaded_file ($tmppath, "images/".$name1)){

			$query="update books set photo='".$name1."' where id=".$this->getid();

			if (!mysqli_query($con,$query)){

		  		echo('Error: ' . mysqli_error($con));

			}

			$file = "images/".$name1;

			$resizedFile = "images/".$name1;

			smart_resize_image($file , 144 , 176 , false , $resizedFile , false , false ,100 );

		}

	}

	public function PostData(){

		$this->setid($_POST["id"]);

		$this->settitle($_POST["title"]);

		$this->setauthor($_POST["author"]);

		$this->setprice($_POST["price"]);

		if(isset($_FILES['photo'])){

			$this->setphoto($_FILES["photo"]);

		}

		$this->setcategoryId($_POST["categoryId"]);

		$this->setDescription($_POST["Description"]);

	}

	public function commit(){

		global $con;

		if($this->getid()>0){

			$sql="UPDATE `books` SET title='".$this->gettitle()."', author='".$this->getauthor()."', price=".$this->getprice().", categoryId=".$this->getcategoryId().", Description='".$this->getDescription()."' where id=".$this->getid();

			if(!mysqli_query($con,$sql)){

				return false;

			}

		}else{

			$sql="INSERT INTO `books` SET title='".$this->gettitle()."', author='".$this->getauthor()."', price=".$this->getprice().", categoryId=".$this->getcategoryId().", Description='".$this->getDescription()."'";

			if(!mysqli_query($con,$sql)){

				return false;

			}

		$this->setid(mysqli_insert_id($con));

		}

		if($this->getphoto()['size'] > 0){

			$this->fileUpload($this->getphoto());

		}

		return true;

	}

	public function getbooks($get=0){

		global $con;

		$books= array();

		if($this->getId() > 0){

			$sql = "select `books`.`id`, `books`.`title`, `books`.`author`, `books`.`price`, `books`.`photo`,`books`.`categoryId`, `books`.`Description` from `books` LEFT JOIN `category` ON `books`.`categoryId`=`category`.`id` where `books`.`id`=".$this->getid();

			if($get==1){

				return $sql;

			}

			$result = mysqli_query($con,$sql);

		}else{

			$sql = "select `books`.`id`, `books`.`title`, `books`.`author`, `books`.`price`, `books`.`photo`,`books`.`categoryId`, `books`.`Description` from `books` LEFT JOIN `category` ON `books`.`categoryId`=`category`.`id`";

			if($get==1){

				return $sql;

			}

			$result = mysqli_query($con,$sql);

		}

		if($result){

			if(mysqli_num_rows($result)>0){

				while($row = mysqli_fetch_array($result)){

					$std = new model_books();

					$std->setbooksobject($row);

					$books[] = $std;

				}

			}

		}

		return $books;

	}

	public function searchbooks($get=0){

		global $con;

		$books= array();

		$sql = "select `books`.`id`, `books`.`title`, `books`.`author`, `books`.`price`, `books`.`photo`,`books`.`categoryId`, `books`.`Description` from `books` LEFT JOIN `category` ON `books`.`categoryId`=`category`.`id` where `books`.`id` LIKE '%".$this->getid()."%' AND `books`.`title` LIKE '%".$this->gettitle()."%' AND `books`.`author` LIKE '%".$this->getauthor()."%' AND `books`.`price` LIKE '%".$this->getprice()."%' AND `books`.`photo` LIKE '%".$this->getphoto()."%' AND `books`.`Description` LIKE '%".$this->getDescription()."%'";

			if($get==1){

				return $sql;

			}

		$result = mysqli_query($con,$sql);

		if($result){

			if(mysqli_num_rows($result)>0){

				while($row = mysqli_fetch_array($result)){

					$std = new model_books();

					$std->setbooksobject($row);

					$books[] = $std;

				}

			}

		}

		return $books;

	}

	public function deletebooks(){

		global $con;

		if($this->getId() > 0){

			$sql="delete from books where id =".$this->getid();

			if(!mysqli_query($con,$sql)){

				return false;

			}

			return true;

		}

	}

}
?>