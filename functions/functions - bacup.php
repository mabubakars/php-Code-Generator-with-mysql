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
	//echo '<pre>';
	//print_r($tbl);
	//echo '</pre>';
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
$classStartc='';
$classEnd='';
$classEndc='';
$datamembers='';
$datamembersc='';
$getsetProperties='';
$getsetPropertiesc='';
$sql = ' SET ';
$insert='"INSERT INTO ';
$insertc='"INSERT INTO ';
$update='"UPDATE ';
$updatec='"UPDATE ';
$commit='';
$commitc='';
$postFunction='';
$postFunctionc='';
$setFunction='';
$setFunctionc='';
$getFunction='';
$getFunctionc='';
$searchFunction = '';
$searchFunctionc = '';
$deleteFunction = '';
$deleteFunctionc = '';
$select = '"select ';
$search = ' where ';
$source = '';
$check = 0;
$relationCounter = 0;
	foreach($data as $tbl=>$val){
		$tblName = $tbl;
		$insert .= $tblName;
		$update .= $tblName;
		$classStart.= 'class model_'.$tblName.'{';
		$classStartc .= showCode('  class model_'.$tblName.'{');
		 
		
		$classStart.= '	function __construct() {';
		$classStartc.= showCode('	function __construct() {');
		
		$classStart.= '	}';
		$classStartc .= showCode('	}');
		
		$classEnd.= '}';
		$classEndc.= showCode('}'); 
		
		$setFunction.= '	private function set'.$tblName.'($row){';
		$setFunctionc.=showCode('	private function set'.$tblName.'($row){');
		 
		
		$postFunction.= '	public function PostData(){';
		$postFunctionc.=showCode('	public function PostData(){');
		 
		
		foreach($val as $key=>$extract){
			$key = $key + $relationCounter;
			if($check == 1 && $extract->position != 2){
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
				if($extract->option[1]->isparent=="No"){
					$dm = explode(",",$extract->option[0]->displaymemeber );
					
					for($i=0;$i<count($dm)-1;$i++){
						$select .= ',`'.$temp[0].'`.`'.$dm[$i].'`';
						$search .= ' AND `'.$temp[0].'`.`'.$dm[$i].'` LIKE :%".$this->get'.$dm[$i].'()."%:';
						$datamembers.= '	private $'.$dm[$i].';';
						$datamembersc.=showCode('	private $'.$dm[$i].';');
						
						$getsetProperties.= '	public function get'.$dm[$i].'(){';
						$getsetPropertiesc.=showCode('	public function get'.$dm[$i].'(){');
						 
					
						$getsetProperties.= '		return $this->'.$dm[$i].';';
						$getsetPropertiesc.=showCode('		return $this->'.$dm[$i].';');
						 
					
						$getsetProperties.= '	}';
						$getsetPropertiesc.=showCode('	}');
						
						$getsetProperties.= '	public function set'.$dm[$i].'($value){';
						$getsetPropertiesc.=showCode('	public function set'.$dm[$i].'($value){');
						 
						
						$getsetProperties.= '		$this->'.$dm[$i].' = $value;';
						$getsetPropertiesc.= showCode('		$this->'.$dm[$i].' = $value;');
						
						
						$getsetProperties.= '	}';
						$getsetPropertiesc.= showCode('	}');
						
						$setFunction.= '		$this->set'.$dm[$i].'($row['.$key.']);';
						$setFunctionc.=showCode('		$this->set'.$dm[$i].'($row['.$key.']);');
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
				$search .= '`'.$tblName.'`.`'.$extract->name.'` LIKE :%".$this->get'.$extract->name.'()."%:';
				$check = 1;
			}
			//generating Data Member of the class
			$datamembers.= '	private $'.$extract->name.';';
			$datamembersc.=showCode('	private $'.$extract->name.';');
			 
		
			//generating get properties of the data members
			$getsetProperties.= '	public function get'.$extract->name.'(){';
			$getsetPropertiesc.=showCode('	public function get'.$extract->name.'(){');
			 
		
			$getsetProperties.= '		return $this->'.$extract->name.';';
			$getsetPropertiesc.=showCode('		return $this->'.$extract->name.';');
			 
		
			$getsetProperties.= '	}';
			$getsetPropertiesc.=showCode('	}');
			 
		
			//generating set properties of the data members
			$getsetProperties.= '	public function set'.$extract->name.'($value){';
			$getsetPropertiesc.=showCode('	public function set'.$extract->name.'($value){');
			 
			
			$getsetProperties.= '		$this->'.$extract->name.' = $value;';
			$getsetPropertiesc.= showCode('		$this->'.$extract->name.' = $value;');
			
			
			$getsetProperties.= '	}';
			$getsetPropertiesc.= showCode('	}');
			
			if($extract->name!= "id"){
				if(in_array($extract->DataType, $DataTypes)){
					$sql.= $extract->name.'=".$this->get'.$extract->name.'()."';
				}else{
					$sql.=$extract->name.'=`".$this->get'.$extract->name.'()."`';
				}
			}
			
			
			$setFunction.= '		$this->set'.$extract->name.'($row['.$key.']);';
			$setFunctionc.=showCode('		$this->set'.$extract->name.'($row['.$key.']);');
			 
			
			$postFunction.= '		$this->set'.$extract->name.'($_POST["'.$extract->name.'"]);';
			$postFunctionc.=showCode('		$this->set'.$extract->name.'($_POST["'.$extract->name.'"]);');
			 
		}
		$check = 0;
		$setFunction.= '	}';
		$setFunctionc.=showCode('	}');
		 
		
		$postFunction.= '	}';
		$postFunctionc.= showCode('	}');
		
		
		$where = '';
		$select.=' from `'.$tblName.'`';
		if(count($relation)>0){
			foreach($relation as $relate){
				$select.= ' LEFT JOIN `'.$relate['table'].'` ON `'.$tblName.'`.`'.$relate['field'].'`=`'.$relate['table'].'`.`id`';
				if($relate['isparent']=="Yes"){
					$where = ' where `'.$relate['table'].'`.`id`=".$this->get'.$relate['field'];
					$check = 1;
					$parent = $relate;
				}
			}
		}
		$sql = str_replace("`","'",$sql);
		$search = str_replace(":","'",$search);
		$insert .= $sql.'"';
		$update .= $sql.' where id=".$this->getid()';
		$commit .= '	public function commit(){';
		$commitc.= showCode('	public function commit(){');
		$commit .= '		if($this->getid()>0){';
		$commitc.= showCode('		if($this->getid()>0){');
		$commit .= '			$sql='.$update.';';
		$commitc.= showCode('			$sql='.$update.';');
		$commit .= '			mysqli_query($con,$sql);';
		$commitc.= showCode('			mysqli_query($con,$sql);');
		$commit .= '		}else{';
		$commitc.= showCode('		}else{');
		$commit .= '			$sql='.$insert.';';
		$commitc .= showCode('			$sql='.$insert.';');
		$commit .= '			mysqli_query($con,$sql);';
		$commitc.= showCode('			mysqli_query($con,$sql);');
		$commit .= '		}';
		$commitc.= showCode('		}');
		$commit .= '	}';
		$commitc.= showCode('	}');
		
		
		$deleteFunction .= '	public function delete'.$tblName.'(){';
		$deleteFunctionc.= showCode('	public function delete'.$tblName.'(){');
		
		$getFunction .= '	public function get'.$tblName.'(){';
		$getFunctionc.=showCode('	public function get'.$tblName.'(){');
		$getFunction .= '		$'.$tblName.'= array();';
		$getFunctionc.=showCode('		$'.$tblName.'= array();');
		
		if($parent==''){
			$deleteFunction .= '		if($this->getId() > 0){';
			$deleteFunctionc.= showCode('		if($this->getId() > 0){');
			$deleteFunction .= '			mysqli_query($con,"delete from '.$tblName.' where id =".$this->getid());';
			$deleteFunctionc.= showCode('			mysqli_query($con,"delete from '.$tblName.' where id =".$this->getid());');
			$deleteFunction .= '		}';
			$deleteFunctionc.= showCode('		}');
			
			$getFunction .= '		if($this->getId() > 0){';
			$getFunctionc.=showCode('		if($this->getId() > 0){');
			$getFunction .= '			$sql = '.$select.' where `'.$tblName.'`.`id`=".$this->getid();';
			$getFunctionc .= showCode('			$sql = '.$select.' where `'.$tblName.'`.`id`=".$this->getid();');
			$getFunction .= '			$result = mysqli_query($con,$sql);';
			$getFunctionc.=showCode('			$result = mysqli_query($con,$sql);');
			$getFunction .= '		}else{';
			$getFunctionc.=showCode('		}else{');
			$getFunction .= '			$sql = '.$select.'";';
			$getFunctionc .= showCode('				$sql = '.$select.'";');
			$getFunction .= '			$result = mysqli_query($con,$sql);';
			$getFunctionc.=showCode('			$result = mysqli_query($con,$sql);');
			$getFunction .= '		}';
			$getFunctionc.=showCode('		}');
			
			$searchFunction .= '	public function search'.$tblName.'(){';
			$searchFunctionc.=showCode('	public function search'.$tblName.'(){');
			$searchFunction .= '		$'.$tblName.'= array();';
			$searchFunctionc.=showCode('		$'.$tblName.'= array();');
			
			$searchFunction .= '		$sql = '.$select.$search.'";';
			$searchFunctionc.= showCode('		$sql = '.$select.$search.'";');
			$searchFunction .= '		$result = mysqli_query($con,$sql);';
			$searchFunctionc.=showCode('		$result = mysqli_query($con,$sql);');
			
			$getFunction .= '		if($result){';
			$getFunctionc .= showCode('		if($result){');
			$getFunction .= '			if(mysqli_num_rows($result)>0){';
			$getFunctionc.=	showCode('			if(mysqli_num_rows($result)>0){');
			$getFunction .= '				while($row = mysqli_fetch_array($result)){';
			$getFunctionc.=showCode('				while($row = mysqli_fetch_array($result)){');
			$getFunction .= '					$std = new model_'.$tblName.'();';
			$getFunctionc.=showCode('					$std = new model_'.$tblName.'();');
			$getFunction .= '					$std->set'.$tblName.'($row);';
			$getFunctionc.=showCode('					$std->set'.$tblName.'($row);');
			$getFunction .= '					$'.$tblName.'[] = $std;';
			$getFunctionc.=showCode('					$'.$tblName.'[] = $std;');
			$getFunction .= '				}';
			$getFunctionc.=showCode('				}');
			$getFunction .= '			}';
			$getFunctionc.=showCode('			}');
			$getFunction .= '		}';
			$getFunctionc.=showCode('		}');
			
			$searchFunction .= '		if($result){';
			$searchFunctionc .= showCode('		if($result){');
			$searchFunction .= '			if(mysqli_num_rows($result)>0){';
			$searchFunctionc.=	showCode('			if(mysqli_num_rows($result)>0){');
			$searchFunction .= '				while($row = mysqli_fetch_array($result)){';
			$searchFunctionc.=showCode('				while($row = mysqli_fetch_array($result)){');
			$searchFunction .= '					$std = new model_'.$tblName.'();';
			$searchFunctionc.=showCode('					$std = new model_'.$tblName.'();');
			$searchFunction .= '					$std->set'.$tblName.'($row);';
			$searchFunctionc.=showCode('					$std->set'.$tblName.'($row);');
			$searchFunction .= '					$'.$tblName.'[] = $std;';
			$searchFunctionc.=showCode('					$'.$tblName.'[] = $std;');
			$searchFunction .= '				}';
			$searchFunctionc.=showCode('				}');
			$searchFunction .= '			}';
			$searchFunctionc.=showCode('			}');
			$searchFunction .= '		}';
			$searchFunctionc.=showCode('		}');
			
			$searchFunction .= '		return $'.$tblName.';';
			$searchFunctionc.=showCode('		return $'.$tblName.';');
			$searchFunction .= '	}';
			$searchFunctionc.=showCode('	}');
			
		}else{
			$deleteFunction .= '		if($this->get'.$parent['field'].'()>0{';
			$deleteFunctionc.= showCode('		if($this->get'.$parent['field'].'()>0{');
			$deleteFunction .= '			mysqli_query($con,"delete from '.$tblName.' where '.$parent['field'].'=".$this->get'.$parent['field'].'());';
			$deleteFunctionc.= showCode('			mysqli_query($con,"delete from '.$tblName.' where '.$parent['field'].'=".$this->get'.$parent['field'].'());');
			$deleteFunction .= '		}';
			$deleteFunctionc.= showCode('		}');
			
			$getFunction .='		if($this->get'.$parent['field'].'()>0{';
			$getFunctionc.=showCode('		if($this->get'.$parent['field'].'()>0{');
			$getFunction .='			$sql='.$select.' where '.$parent['table'].'.'.$parent['field'].'=".$this->get'.$parent['field'].'()';
			$getFunctionc .=showCode('			$sql='.$select.' where '.$parent['table'].'.'.$parent['field'].'=".$this->get'.$parent['field'].'()');
			$getFunction .='			$result = mysqli_query($con,$sql);';
			$getFunctionc.=showCode('			$result = mysqli_query($con,$sql);');
			$getFunction .='		}';
			$getFunctionc.=showCode('		}');
			$getFunction .= '		if($result){';
			$getFunctionc .= showCode('		if($result){');
			$getFunctionc.=showCode('			if(mysqli_num_rows($result)>0){');
			$getFunction .= '					while($row = mysqli_fetch_array($result)){';
			$getFunctionc.=showCode('					while($row = mysqli_fetch_array($result)){');
			$getFunction .= '						$std = new model_'.$tblName.'();';
			$getFunctionc.=showCode('						$std = new model_'.$tblName.'();');
			$getFunction .= '						$std->set'.$tblName.'($row);';
			$getFunctionc.=showCode('						$std->set'.$tblName.'($row);');
			$getFunction .= '						$'.$tblName.'[] = $std;';
			$getFunctionc.=showCode('						$'.$tblName.'[] = $std;');
			$getFunction .= '					}';
			$getFunctionc.=showCode('					}');
			$getFunction .= '			}';
			$getFunctionc.=showCode('			}');
			$getFunction .= '		}';
			$getFunctionc.=showCode('		}');
		}
		
		$deleteFunction .= '	}';
		$deleteFunctionc.= showCode('	}');
		$getFunction .= '		return $'.$tblName.';';
		$getFunctionc.=showCode('		return $'.$tblName.';');
		$getFunction .= '	}';
		$getFunctionc.=showCode('	}');
	}
	//echo commit;
	//mysqli_query($con,"INSERT INTO Persons (FirstName, LastName, Age) 
	//$source = $classStart.$datamembers.$getsetProperties.$setFunction.$postFunction;
	//$geshi = new GeSHi($source, $language);
	//$geshi->enable_line_numbers(true);
	//$geshi->enable_multiline_span(true);
	//echo $geshi->parse_code();
	$source = $classStartc.$datamembersc.$getsetPropertiesc.$setFunctionc.$postFunctionc.$commitc.$getFunctionc.$searchFunctionc.$deleteFunctionc.$classEndc;
	echo $source;
}
?>