<?php

class MaterialMapper extends Mapper
{
	public function getMaterials(){
		$sql = "SELECT * FROM materials";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new MaterialEntity($row);
		} 
		return $results;
		
	}

	public function getMaterialById($id){

		$sql = "SELECT * FROM `materials` WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new MaterialEntity($row);
		return $results;
	}
	
	

	public function save(MaterialEntity $material){
		
		$sql = "INSERT INTO `materials` (`id`, `name`, `description`, `date_added`, `last_updated`) VALUES (NULL, '{$material->getName()}', '{$material->getDescription()}', NOW(), NOW());";
		
		$result = mysql_query($sql);
		return $result;
	}

}	

 