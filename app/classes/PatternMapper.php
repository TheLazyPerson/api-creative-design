<?php

class PatternMapper extends Mapper
{
	
	
	public function getPatterns(){
		$sql = "SELECT * FROM `patterns` WHERE status = '1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new PatternEntity($row);
		} 
		return $results;
		
	}

	public function getPatternById($id){

		$sql = "SELECT * FROM patterns WHERE id ={$id} AND status = '1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }


		$row = mysql_fetch_array($result);
		
        if (is_bool($row) === true) {
        	return $row;
        }
		$results = new PatternEntity($row);
		return $results;
	}
	

	public function save(PatternEntity $pattern){
		$sql = "INSERT INTO `patterns` (`id`, `name`, `pattern_path`, `status` ,`date_added`) VALUES (NULL, '{$pattern->getName()}', '{$pattern->getPatternPath()}', '1', NOW())";
		$result = mysql_query($sql);
		return $result;
	}
	public function delete($id){
		$sql = "UPDATE `patterns` SET `status`= 0 WHERE `id`= {$id}";
		$result = mysql_query($sql);
		return $result;
	}

}	

 