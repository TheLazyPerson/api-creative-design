<?php

class MotifMapper extends Mapper
{
	public function getMotifs(){
		$sql = "SELECT * FROM motif WHERE status = '1'";
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

		$sql = "SELECT * FROM `motif` WHERE id ={$id} AND status='1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
	 	if (is_bool($row) === true) {
        	return $row;
        }
		$results = new MotifEntity($row);
		return $results;
	}
	
	public function save(MotifEntity $motif){
		
		$sql = "INSERT INTO `motif` (`id`, `name`, `motif_path`, `description`, `status`,`date_added`, `last_modified`) VALUES (NULL, '{$motif->getName()}', '{$motif->getMotifPath()}', '{$motif->getDescription()}','1' ,NOW(), NOW())";
		
		$result = mysql_query($sql);
		return $result;
	}


	public function delete($id){
		$sql = "UPDATE `motif` SET `status`= 0 WHERE `id`= {$id}";
		$result = mysql_query($sql);
		return $result;
	}

}	

 