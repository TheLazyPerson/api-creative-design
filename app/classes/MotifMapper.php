<?php

class MotifMapper extends Mapper
{
	public function getMotifs(){
		$sql = "SELECT * FROM motif";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new MotifEntity($row);
		} 
		return $results;
		
	}

	public function getMotifById($id){

		$sql = "SELECT * FROM `motif` WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new MotifEntity($row);
		return $results;
	}
	
	public function save(MotifEntity $motif){
		
		$sql = "INSERT INTO `motif` (`id`, `name`, `motif_path`, `description`, `date_added`, `last_modified`) VALUES (NULL, '{$motif->getName()}', '{$motif->getMotifPath()}', '{$motif->getDescription()}', NOW(), NOW())";
		
		$result = mysql_query($sql);
		return $result;
	}

}	

 