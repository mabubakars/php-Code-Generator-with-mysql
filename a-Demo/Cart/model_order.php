<?php 
  class model_order{

	function __construct() {

	}

	private $relations;

	private $id;

	private $name;

	private $cardno;

	private $address;

	private $deliveraddress;

	private $datetime;

	private $deliverd;

	public function getid(){

		return $this->id;

	}

	public function setid($value){

		$this->id = $value;

	}

	public function getname(){

		return $this->name;

	}

	public function setname($value){

		$this->name = $value;

	}

	public function getcardno(){

		return $this->cardno;

	}

	public function setcardno($value){

		$this->cardno = $value;

	}

	public function getaddress(){

		return $this->address;

	}

	public function setaddress($value){

		$this->address = $value;

	}

	public function getdeliveraddress(){

		return $this->deliveraddress;

	}

	public function setdeliveraddress($value){

		$this->deliveraddress = $value;

	}

	public function getdatetime(){

		return $this->datetime;

	}

	public function setdatetime($value){

		$this->datetime = $value;

	}

	public function getdeliverd(){

		return $this->deliverd;

	}

	public function setdeliverd($value){

		$this->deliverd = $value;

	}

	public function getrelations(){

	return $relation;

	}

	private function setorderobject($row){

		$this->setid($row[0]);

		$this->setname($row[1]);

		$this->setcardno($row[2]);

		$this->setaddress($row[3]);

		$this->setdeliveraddress($row[4]);

		$this->setdatetime($row[5]);

		$this->setdeliverd($row[6]);

	}

	public function buildarray($result){

		$order= array();

		if($result){

			if(mysqli_num_rows($result)>0){

				while($row = mysqli_fetch_array($result)){

					$std = new model_order();

					$std->setorderobject($row);

					$order[] = $std;

				}

			}

		}

		return $order;

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

			$query="update order set photo='".$name1."' where id=".$this->getid();

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

		$this->setname($_POST["name"]);

		$this->setcardno($_POST["cardno"]);

		$this->setaddress($_POST["address"]);

		$this->setdeliveraddress($_POST["deliveraddress"]);

		$this->setdatetime($_POST["datetime"]);

		$this->setdeliverd($_POST["deliverd"]);

	}

	public function commit(){

		global $con;

		if($this->getid()>0){

			$sql="UPDATE `order` SET `name`='".$this->getname()."', `cardno`='".$this->getcardno()."', `address`='".$this->getaddress()."', `deliveraddress`='".$this->getdeliveraddress()."', `datetime`='".$this->getdatetime()."', `deliverd`=".$this->getdeliverd()." where `id`=".$this->getid();
			if(!mysqli_query($con,$sql)){

				return false;

			}

		}else{

			$sql="INSERT INTO `order` SET `name`='".$this->getname()."', `cardno`='".$this->getcardno()."', `address`='".$this->getaddress()."', `deliveraddress`='".$this->getdeliveraddress()."', `datetime`='".$this->getdatetime()."', `deliverd`=".$this->getdeliverd()."";

			if(!mysqli_query($con,$sql)){

				return false;

			}

		$this->setid(mysqli_insert_id($con));

		}

		return true;

	}

	public function getorder($get=0){

		global $con;

		$order= array();

		if($this->getId() > 0){

			$sql = "select `order`.`id`, `order`.`name`, `order`.`cardno`, `order`.`address`, `order`.`deliveraddress`, `order`.`datetime`, `order`.`deliverd` from `order` where `order`.`id`=".$this->getid();

			if($get==1){

				return $sql;

			}

			$result = mysqli_query($con,$sql);

		}else{

			$sql = "select `order`.`id`, `order`.`name`, `order`.`cardno`, `order`.`address`, `order`.`deliveraddress`, `order`.`datetime`, `order`.`deliverd` from `order`";

			if($get==1){

				return $sql;

			}

			$result = mysqli_query($con,$sql);

		}

		if($result){

			if(mysqli_num_rows($result)>0){

				while($row = mysqli_fetch_array($result)){

					$std = new model_order();

					$std->setorderobject($row);

					$order[] = $std;

				}

			}

		}

		return $order;

	}

	public function searchorder($get=0){

		global $con;

		$order= array();

		$sql = "select `order`.`id`, `order`.`name`, `order`.`cardno`, `order`.`address`, `order`.`deliveraddress`, `order`.`datetime`, `order`.`deliverd` from `order` where `order`.`id` LIKE '%".$this->getid()."%' AND `order`.`name` LIKE '%".$this->getname()."%' AND `order`.`cardno` LIKE '%".$this->getcardno()."%' AND `order`.`address` LIKE '%".$this->getaddress()."%' AND `order`.`deliveraddress` LIKE '%".$this->getdeliveraddress()."%' AND `order`.`datetime` LIKE '%".$this->getdatetime()."%' AND `order`.`deliverd` LIKE '%".$this->getdeliverd()."%'";

			if($get==1){

				return $sql;

			}

		$result = mysqli_query($con,$sql);

		if($result){

			if(mysqli_num_rows($result)>0){

				while($row = mysqli_fetch_array($result)){

					$std = new model_order();

					$std->setorderobject($row);

					$order[] = $std;

				}

			}

		}

		return $order;

	}

	public function deleteorder(){

		global $con;

		if($this->getId() > 0){

			$sql="delete from order where id =".$this->getid();

			if(!mysqli_query($con,$sql)){

				return false;

			}

			return true;

		}

	}

}
?>