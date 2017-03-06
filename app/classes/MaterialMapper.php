<?php

class MaterialMapper extends Mapper
{
	public function getMaterials(){
		$sql = "SELECT * FROM materials WHERE status='1'";
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

		$sql = "SELECT * FROM `materials` WHERE id ={$id} AND status='1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new MaterialEntity($row);
		return $results;
	}
	
	public function save(MaterialEntity $material){
		
		$sql = "INSERT INTO `materials` (`id`, `name`, `description`, `status`, `date_added`, `last_updated`) VALUES (NULL, '{$material->getName()}', '{$material->getDescription()}', '1', NOW(), NOW());";

		$result = mysql_query($sql);
		return $result;
	}
	public function update(MaterialEntity $material){
		
		$sql = "UPDATE `materials` SET `name`='{$material->getName()}',`description`='{$material->getDescription()}',`last_updated`=NOW() WHERE id='{$material->getId()}'";

		$result = mysql_query($sql);
		return $result;
	}
	public function delete($id){
		$sql = "UPDATE `materials` SET `status`= 0 WHERE `id`= {$id}";
		$result = mysql_query($sql);
		return $result;
	}

}	

 