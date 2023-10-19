<?php
include_once '../connection.inc.php';
include_once '../geshi/geshi.php';
$geshi = new GeSHi('', 'php');
if(isset($_GET['db'])){
	$db = $_GET['db'];
	getAttributes($db);
}
if(isset($_POST['json'])){
	$tbl = json_decode($_POST['json']);
	creatCode($tbl);
}
class obj {
	public $attr = array();
}
function getAttributes($dbname){
	global $con;
	$sql = "SELECT `TABLE_NAME`,`COLUMN_NAME`,`ORDINAL_POSITION`,`DATA_TYPE` FROM `information_schema`.`COLUMNS` where `TABLE_SCHEMA`='".$dbname."' order by TABLE_NAME,ORDINAL_POSITION";
	$columns = mysqli_query($con,$sql);
	$table = array();
	$tbl = "";
		while($column = mysqli_fetch_array($columns)){
			if($tbl!=$column['TABLE_NAME']){
				if($tbl!= ""){
					$table[$tbl]=$obj->attr;
				}
				$tbl=$column['TABLE_NAME'];
				$relations = getRelations($tbl);
				$obj = new obj();
			}
			$relation = "";
			if (array_key_exists($column['COLUMN_NAME'], $relations)) {
				$relation = $relations[$column['COLUMN_NAME']];
			}
			$obj->attr[] = array('name'=>$column['COLUMN_NAME'],
							'position'=>$column['ORDINAL_POSITION'],
							'DataType'=>$column['DATA_TYPE'],
							'relation'=>$relation);
		}
	$table[$tbl]=$obj->attr;
	echo json_encode($table);
}
mysqli_close($con);
function getRelations($table){
global $con;
global $db;
$relat = array();
$sql = "select `TABLE_NAME`,`COLUMN_NAME`,`REFERENCED_TABLE_NAME`,`REFERENCED_COLUMN_NAME` from `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` where CONSTRAINT_SCHEMA='".$db."' and REFERENCED_TABLE_NAME != '' and TABLE_NAME='".$table."'";
	$relations = mysqli_query($con,$sql);
	if(mysqli_num_rows($relations)>0){   
		while($relation = mysqli_fetch_array($relations)){
				if($relation['REFERENCED_TABLE_NAME']!= ""){
				$obj = new obj();
				$sql = "select `COLUMN_NAME` from `information_schema`.`COLUMNS` where TABLE_SCHEMA='".$db."' and TABLE_NAME ='".$relation['REFERENCED_TABLE_NAME']."'";
				$fields = mysqli_query($con,$sql);
				if(mysqli_num_rows($fields)>0){
					while($field = mysqli_fetch_array($fields)){
						$obj->attr[] = $field['COLUMN_NAME'];
					}
				}
				$relat[$relation['COLUMN_NAME']] = array('table'=>$relation['REFERENCED_TABLE_NAME'],'column'=>$relation['REFERENCED_COLUMN_NAME'],'displayFileds'=>$obj->attr);
				}
			}	
		}
	return $relat;
}
function showCode($code){
	global $geshi;
	$geshi->set_source($code);
	return $geshi->parse_code();
}
function creatCode($data){
$DataTypes = array('int','float','decimal','double','real');
$relation = array();
$parent = '';
$language = 'php';
$tblName = "";
$classStart='';
$classEnd='';
$datamembers=showCode('	private $relations;');
$getsetProperties='';
$sql = ' SET ';
$insert='"INSERT INTO ';
$update='"UPDATE ';
$commit='';
$postFunction='';
$setFunction='';
$getFunction='';
$buildArrayFunction = '';
$dropDownFunction= '';
$searchFunction= '';
$deleteFunction= '';
$fileUploadFunction= '';
$fileCode = '';
$select = '"select ';
$search = ' where ';
$related = '';
$getsetRelationProperty = showCode('	public function getrelations(){');
$source = '';
$check = 0;
$relationCounter = 0;
$dropdown = '<select name="{name}">';

$form = '';
$table = '';
$header = '';
$fullpage = '';

$fullfile = fopen("../Template/full.txt", "r") or exit("Unable to open file!");
$ajaxfile = fopen("../Template/ajax.txt", "r") or exit("Unable to open file!");
$formfile = fopen("../Template/form_row.txt", "r") or exit("Unable to open file!");
$tablefile = fopen("../Template/table_row.txt", "r") or exit("Unable to open file!");
$tableheaderfile = fopen("../Template/table_header.txt", "r") or exit("Unable to open file!");

$formrow = fgets($formfile);
$tablerow = fgets($tablefile);
$tableheader = fgets($tableheaderfile);

	//Table attribute main loop.
	foreach($data as $tbl=>$val){
		$tblName = $tbl;
		$insert.= '`'.$tblName.'`';
		$update.= '`'.$tblName.'`';
		$classStart.=showCode('  class model_'.$tblName.'{');
		$classStart.=showCode('	function __construct() {');
		$classStart.=showCode('	}');
		$classEnd.=showCode('}'); 
		$setFunction.=showCode('	private function set'.$tblName.'object($row){');
		$postFunction.=showCode('	public function PostData(){');
		
		//Loop for relational content to build query with join.
		foreach($val as $key=>$extract){
			$tempheader = $tableheader;
			$type = is_array($extract->option) ? '': $extract->option;
			$tempform = $formrow;
			$input = '<input type="{type}" name="{Name}" value="<?php if($row){ echo $row[0]->get{Name}();} ?>" />';
			$tblShow = '<?php echo $'.$tblName.'[$i]->get'.$extract->name.'(); ?>';
			
			if($type!=''){
				if($type =='image'){
					$type = 'file';
					$input=str_replace('{type}',$type,$input);
				}elseif($type =='Textarea'){
					$input='<textarea name='.$extract->name.' rows="4" cols="50"><?php if($row){ echo $row[0]->get'.$extract->name.'();} ?></textarea>';
				}elseif($type =='checkbox'){
					$input='<input type="checkbox" name="'.$extract->name.'" value="<?php if($row){ echo $row[0]->get'.$extract->name.'();} ?>" <?php echo (($row?$row[0]->get'.$extract->name.'():\'\')==1?\'checked\':\'\') ?> />';
				}elseif($type =='datetime'){
					$input='<input type="textbox" onfocus="javascript:NewCssCal (\'inputField\',\'yyyyMMdd\',\'dropdown\',true,\'24\',true)" id = "inputField" name="'.$extract->name.'" value="<?php if($row){ echo $row[0]->get'.$extract->name.'();} ?>"/>';
				}
				else{
					$input=str_replace('{type}',$type,$input);
				}
				if($type =='hidden'){
					$tempform = $input;
				}else{
					$tempform = str_replace('{input}',$input,$tempform);
				}
				
			}else{
				$dropdown = str_replace('{name}',$extract->name,$dropdown);
				$dropdown .= '<?php echo $Object->dropdown("'.$extract->name.'",($row?$row[0]->get'.$extract->name.'():**)); ?>';
				$dropdown .= '</select>';
				$tempform = str_replace('{input}',$dropdown,$tempform); 
			}
			
			$key = $key + $relationCounter;
			if($check == 1 && $extract->position != 2 && $type !='file'){
				$sql .= ', '; 
			}
			//Extracting relations if exist
			$temp = explode(".", $extract->relation);
			if($extract->relation != 'No'){
				$relation[] = array('table'=>$temp[0],
									'field'=>$extract->name,
									'relation'=>$extract->relation,
									'displaymemeber'=>$extract->option[0]->displaymemeber,
									'isparent'=>$extract->option[1]->isparent
									);
				//check if relational table is parent
				if($extract->option[1]->isparent=="No"){
					$dm = explode(",",$extract->option[0]->displaymemeber );
					//loop for display member of relational attributes.
					for($i=0;$i<count($dm);$i++){
						$select .= ',`'.$temp[0].'`.`'.$dm[$i].'`';
						$search .= ' AND `'.$temp[0].'`.`'.$dm[$i].'` LIKE :%".$this->get'.$dm[$i].'()."%:';
						$datamembers.=showCode('	private $'.$dm[$i].';');
						$getsetProperties.=showCode('	public function get'.$dm[$i].'(){');
						$getsetProperties.=showCode('		return $this->'.$dm[$i].';');
						$getsetProperties.=showCode('	}');
						$getsetProperties.=showCode('	public function set'.$dm[$i].'($value){');
						$getsetProperties.=showCode('		$this->'.$dm[$i].' = $value;');
						$getsetProperties.=showCode('	}');
						$setFunction.=showCode('		$this->set'.$dm[$i].'($row['.$key.']);');
						if($dm[$i] =='photo'){
							$tblShow='<img src="images/<?php echo $'.$tblName.'[$i]->get'.$dm[$i].'(); ?>" height="42" width="42"> ';
						}else{
							$tblShow = '<?php echo $'.$tblName.'[$i]->get'.$dm[$i].'(); ?>';
						}
						$tblShow= str_replace('{table_name}',$tblName,$tblShow);
						$tblShow= str_replace('{Name}',$dm[$i],$tblShow);
						$table.=showCode('       '.str_replace('{input}',$tblShow,$temprow));
						$tempheader = $tableheader;
						$header.=showCode('       '.str_replace('{Name}',$dm[$i],$tempheader));
						
						$relationCounter++;
						$key = $key + 1;
					}
					$check = 1;
				}
				$select .= ',`'. $tblName.'`.`'.$extract->name.'`';
			}else{
				if($check == 1){
					$select .= ', ';
					$search .=' AND ';
				}
				$select .= '`'.$tblName.'`.`'.$extract->name.'`';
				$search .= '`'.$tblName.'`.`'.$extract->name.'` LIKE \'%".$this->get'.$extract->name.'()."%\'';
				$temprow = $tablerow;
				
				if($type =='file'){
					$tblShow='<img src="images/<?php echo $'.$tblName.'[$i]->get'.$extract->name.'(); ?>" height="42" width="42"> ';
				}else{
					$tblShow = '<?php echo $'.$tblName.'[$i]->get'.$extract->name.'(); ?>';
				}
				
				$table.=showCode('       '.str_replace('{input}',$tblShow,$temprow));
				
				$tempheader = $tableheader;
				$header.=showCode('       '.str_replace('{Name}',$extract->name,$tempheader));
				$check = 1;
			}
			$tempform = str_replace('*',"'",$tempform);
			$form.=showCode('       '.str_replace('{Name}',$extract->name,$tempform));
			$datamembers.=showCode('	private $'.$extract->name.';');
			$getsetProperties.=showCode('	public function get'.$extract->name.'(){');
			$getsetProperties.=showCode('		return $this->'.$extract->name.';');
			$getsetProperties.=showCode('	}');
			$getsetProperties.=showCode('	public function set'.$extract->name.'($value){');
			$getsetProperties.=showCode('		$this->'.$extract->name.' = $value;');
			$getsetProperties.=showCode('	}');
			//generating query with caring of data types
			if($extract->name!= "id" && $type !='file'){
				if(in_array($extract->DataType, $DataTypes)){
					$sql.= '`'.$extract->name.'`=".$this->get'.$extract->name.'()."';
				}else{
					$sql.='`'.$extract->name.'`=\'".$this->get'.$extract->name.'()."\'';
				}
			}
			$setFunction.=showCode('		$this->set'.$extract->name.'($row['.$key.']);');
			if($type=='file'){
				$postFunction.=showCode('		if(isset($_FILES[\''.$extract->name.'\'])){');
				$postFunction.=showCode('			$this->set'.$extract->name.'($_FILES["'.$extract->name.'"]);');
				$postFunction.=showCode('		}');
				$fileCode.=showCode('		if($this->getphoto()[\'size\'] > 0){');
				$fileCode.=showCode('			$this->fileUpload($this->getphoto());');
				$fileCode.=showCode('		}');
			}else{
				$postFunction.=showCode('		$this->set'.$extract->name.'($_POST["'.$extract->name.'"]);'); 
			}
		}
		$check = 0;
		$setFunction.=showCode('	}');
		$postFunction.=showCode('	}');
		$where = '';
		$select.=' from `'.$tblName.'`';
		
		//Creating join for query if relation exist.
		if(count($relation)>0){
		$getsetRelationProperty .=showCode(' 		$relation=array(');
		$counter = 0;
			foreach($relation as $relate){
				if($counter!=0){
					$related .= ',';
					$getsetRelationProperty .=showCode(',');
				}
				$getsetRelationProperty .=showCode('			'.$counter."=>array('table'=>'".$relate['table']."','field'=>'".$relate['field']."','displayField'=>'".$relate['displaymemeber']."')");
				$select.= ' LEFT JOIN `'.$relate['table'].'` ON `'.$tblName.'`.`'.$relate['field'].'`=`'.$relate['table'].'`.`id`';
				if($relate['isparent']=="Yes"){
					$where = ' where `'.$relate['table'].'`.`id`=".$this->get'.$relate['field'];
					$check = 1;
					$parent = $relate;
				}
				$counter++;
			}
			$getsetRelationProperty .=showCode('		);');
		}
		$getsetRelationProperty .=showCode('	return $relation;');
		$getsetRelationProperty .=showCode('	}');
		$getsetProperties.=	$getsetRelationProperty;
		$insert.= $sql.'"';
		$update.= $sql.' where `id`=".$this->getid()';
		$buildArrayFunction.=showCode('	public function buildarray($result){');
		$buildArrayFunction.=showCode('		$'.$tblName.'= array();');
		$buildArrayFunction.=showCode('		if($result){');
		$buildArrayFunction.=showCode('			if(mysqli_num_rows($result)>0){');
		$buildArrayFunction.=showCode('				while($row = mysqli_fetch_array($result)){');
		$buildArrayFunction.=showCode('					$std = new model_'.$tblName.'();');
		$buildArrayFunction.=showCode('					$std->set'.$tblName.'object($row);');
		$buildArrayFunction.=showCode('					$'.$tblName.'[] = $std;');
		$buildArrayFunction.=showCode('				}');
		$buildArrayFunction.=showCode('			}');
		$buildArrayFunction.=showCode('		}');
		$buildArrayFunction.=showCode('		return $'.$tblName.';');
		$buildArrayFunction.=showCode('	}');
		$commit.=showCode('	public function commit(){');
		$commit.=showCode('		global $con;');
		$commit.=showCode('		if($this->getid()>0){');
		$commit.=showCode('			$sql='.$update.';');
		$commit.=showCode('			if(!mysqli_query($con,$sql)){');
		$commit.=showCode('				return false;');
		$commit.=showCode('			}');
		$commit.=showCode('		}else{');
		$commit.=showCode('			$sql='.$insert.';');
		$commit.=showCode('			if(!mysqli_query($con,$sql)){');
		$commit.=showCode('				return false;');
		$commit.=showCode('			}');
		$commit.=showCode('		$this->setid(mysqli_insert_id($con));');
		$commit.=showCode('		}');
		if($fileCode!=''){
			$commit.=$fileCode;
		}
		$commit.=showCode('		return true;');
		$commit.=showCode('	}');
		$deleteFunction.=showCode('	public function delete'.$tblName.'(){');
		$deleteFunction.=showCode('		global $con;');
		$getFunction.=showCode('	public function get'.$tblName.'($get=0){');
		$getFunction.=showCode('		global $con;');
		$getFunction.=showCode('		$'.$tblName.'= array();');
		//Generating code for table having no parent including search function.
		if($parent==''){
			$deleteFunction.=showCode('		if($this->getId() > 0){');
			$deleteFunction.=showCode('			$sql="delete from '.$tblName.' where id =".$this->getid();');
			$deleteFunction.=showCode('			if(!mysqli_query($con,$sql)){');
			$deleteFunction.=showCode('				return false;');
			$deleteFunction.=showCode('			}');
			$deleteFunction.=showCode('			return true;');
			$deleteFunction.=showCode('		}');
			$getFunction.=showCode('		if($this->getId() > 0){');
			$getFunction.=showCode('			$sql = '.$select.' where `'.$tblName.'`.`id`=".$this->getid();');
			$getFunction.=showCode('			if($get==1){');
			$getFunction.=showCode('				return $sql;');
			$getFunction.=showCode('			}');
			$getFunction.=showCode('			$result = mysqli_query($con,$sql);');
			$getFunction.=showCode('		}else{');
			$getFunction.=showCode('			$sql = '.$select.'";');
			$getFunction.=showCode('			if($get==1){');
			$getFunction.=showCode('				return $sql;');
			$getFunction.=showCode('			}');
			$getFunction.=showCode('			$result = mysqli_query($con,$sql);');
			$getFunction.=showCode('		}');
			$searchFunction.=showCode('	public function search'.$tblName.'($get=0){');
			$searchFunction.=showCode('		global $con;');
			$searchFunction.=showCode('		$'.$tblName.'= array();');
			$searchFunction.=showCode('		$sql = '.$select.$search.'";');
			$searchFunction.=showCode('			if($get==1){');
			$searchFunction.=showCode('				return $sql;');
			$searchFunction.=showCode('			}');
			$searchFunction.=showCode('		$result = mysqli_query($con,$sql);');
			$getFunction.=showCode('		if($result){');
			$getFunction.=showCode('			if(mysqli_num_rows($result)>0){');
			$getFunction.=showCode('				while($row = mysqli_fetch_array($result)){');
			$getFunction.=showCode('					$std = new model_'.$tblName.'();');
			$getFunction.=showCode('					$std->set'.$tblName.'object($row);');
			$getFunction.=showCode('					$'.$tblName.'[] = $std;');
			$getFunction.=showCode('				}');
			$getFunction.=showCode('			}');
			$getFunction.=showCode('		}');
			$searchFunction.=showCode('		if($result){');
			$searchFunction.=	showCode('			if(mysqli_num_rows($result)>0){');
			$searchFunction.=showCode('				while($row = mysqli_fetch_array($result)){');
			$searchFunction.=showCode('					$std = new model_'.$tblName.'();');
			$searchFunction.=showCode('					$std->set'.$tblName.'object($row);');
			$searchFunction.=showCode('					$'.$tblName.'[] = $std;');
			$searchFunction.=showCode('				}');
			$searchFunction.=showCode('			}');
			$searchFunction.=showCode('		}');
			$searchFunction.=showCode('		return $'.$tblName.';');
			$searchFunction.=showCode('	}');
			$dropDownFunction.=showCode('	public function dropdown($field="",$value=""){');
			$dropDownFunction.=showCode('		global $con;');
			$dropDownFunction.=showCode('		$sql = "";');
			$dropDownFunction.=showCode('		$relate="";');
			$dropDownFunction.=showCode('		$related=$this->getrelations();');
			$dropDownFunction.=showCode('		foreach($related as $row){');
			$dropDownFunction.=showCode('			if($row["field"]==$field){');
			$dropDownFunction.=showCode('				$sql = "select id,".$row["displayField"]." from ".$row["table"]."";');
			$dropDownFunction.=showCode('				$relate=$row;');
			$dropDownFunction.=showCode('			}');
			$dropDownFunction.=showCode('		}');
			$dropDownFunction.=showCode(' 		$option=array();');
			$test = '		$option[]=*<option value="0">Select</option>*;';
			$test = str_replace('*',"'",$test);
			$dropDownFunction.=showCode($test);
			$dropDownFunction.=showCode('		$result = mysqli_query($con,$sql);');
			$dropDownFunction.=showCode('		if($result){');
			$dropDownFunction.=showCode('			if(mysqli_num_rows($result)>0){');
			$dropDownFunction.=showCode('				$select="";');
			
			$dropDownFunction.=showCode('				while($row = mysqli_fetch_array($result)){');
			$dropDownFunction.=showCode('					if($value==$row["id"]){');
			$dropDownFunction.=showCode('						$select="selected";');
			$dropDownFunction.=showCode('					}');
			$test = '					$option[]=*<option value="*.$row[*id*].*"*.$select.* >*.$row[$relate[*displayField*]].*</option>*;';
			$test = str_replace('*',"'",$test);
			$dropDownFunction.=showCode($test);	
			$dropDownFunction.=showCode('				}');
			$dropDownFunction.=showCode('			}');
			$dropDownFunction.=showCode('		}');
			$dropDownFunction.=showCode('	return join("",$option);');
			$dropDownFunction.=showCode('	}');
			
			$fileUploadFunction.=showCode('	public function fileUpload($file){');
			$fileUploadFunction.=showCode('		global $con;');
			$fileUploadFunction.=showCode('		$name1 = $file [\'name\'];');
			$fileUploadFunction.=showCode('		$type = $file [\'type\'];');
			$fileUploadFunction.=showCode('		$size = $file [\'size\'];');
			$fileUploadFunction.=showCode('		$tmppath = $file [\'tmp_name\'];');
			$fileUploadFunction.=showCode('		$extension = explode(".", $name1);');
			$fileUploadFunction.=showCode('		$extension=$extension[1];');
			$fileUploadFunction.=showCode('		$name1=$this->getid().".".$extension;');
			$fileUploadFunction.=showCode('		if(move_uploaded_file ($tmppath, "images/".$name1)){');
			$fileUploadFunction.=showCode('			$query="update '.$tblName.' set photo=\'".$name1."\' where id=".$this->getid();');
			$fileUploadFunction.=showCode('			if (!mysqli_query($con,$query)){');
			$fileUploadFunction.=showCode('		  		echo(\'Error: \' . mysqli_error($con));');
			$fileUploadFunction.=showCode('			}');
			$fileUploadFunction.=showCode('			$file = "images/".$name1;');
			$fileUploadFunction.=showCode('			$resizedFile = "images/".$name1;');
			$fileUploadFunction.=showCode('			smart_resize_image($file , 144 , 176 , false , $resizedFile , false , false ,100 );');
			$fileUploadFunction.=showCode('		}');
			$fileUploadFunction.=showCode('	}');
			
		}else{
			$fullfile = $ajaxfile;
			$deleteFunction.=showCode('		if($this->get'.$parent['field'].'()>0{');
			$deleteFunction.=showCode('			$sql = "delete from '.$tblName.' where `'.$tblName.'`.`'.$parent['field'].'`=".$this->get'.$parent['field'].'();');
			$deleteFunction.=showCode('			if(!mysqli_query($con,$sql)){');
			$deleteFunction.=showCode('				return false;');
			$deleteFunction.=showCode('			}');
			$deleteFunction.=showCode('			return true;');
			$deleteFunction.=showCode('		}');
			$getFunction.=showCode('		if($this->get'.$parent['field'].'()>0{');
			$getFunction.=showCode('			$sql='.$select.' where `'.$tblName.'`.`'.$parent['field'].'`=".$this->get'.$parent['field'].'()');
			$getFunction.=showCode('			if($get==1){');
			$getFunction.=showCode('				return $sql;');
			$getFunction.=showCode('			}');
			$getFunction.=showCode('			$result = mysqli_query($con,$sql);');
			$getFunction.=showCode('		}');
			$getFunction.=showCode('		if($result){');
			$getFunction.=showCode('			if(mysqli_num_rows($result)>0){');
			$getFunction.=showCode('					while($row = mysqli_fetch_array($result)){');
			$getFunction.=showCode('						$std = new model_'.$tblName.'();');
			$getFunction.=showCode('						$std->set'.$tblName.'($row);');
			$getFunction.=showCode('						$'.$tblName.'[] = $std;');
			$getFunction.=showCode('					}');
			$getFunction.=showCode('			}');
			$getFunction.=showCode('		}');
		}
		$deleteFunction.=showCode('	}');
		$getFunction.=showCode('		return $'.$tblName.';');
		$getFunction.=showCode('	}');
	}
		$class = 'model_'.$tblName;
		$i=0;
		while (!feof($fullfile)) {
		  $check = 0;
		  $temp = fgets($fullfile);
		  $temp = str_replace('{table_name}',$tblName,$temp);
		  $temp = str_replace('{class}',$class,$temp);
		  
		  if(strpos($temp , '{form_row}' )){
			$temp = str_replace('{form_row}',$form,$temp);
			$check = 1;
		  }
		  if(strpos($temp , '{table_row}' )){
			$temp = str_replace('{table_row}',$table,$temp);
			$check = 1;
		  }
		  if(strpos($temp , '{table_header}' )){
			$temp = str_replace('{table_header}',$header,$temp);
			$check = 1;
		  }
		  if(strpos($temp , '{relatedId}' )){
			$temp = str_replace('{relatedId}',$parent['field'],$temp);
		  }
		  if($check==0){
				$fullpage.=showCode($temp);
			}else{
				$fullpage.=$temp;
			}
		}
	fclose($formfile);
	fclose($tablefile);
	fclose($tableheaderfile);
	fclose($fullfile);
	$source = $classStart.$datamembers.$getsetProperties.$setFunction.$buildArrayFunction.$dropDownFunction.$fileUploadFunction.$postFunction.$commit.$getFunction.$searchFunction.$deleteFunction.$classEnd;
	echo $source;
	echo $fullpage;
}
?>