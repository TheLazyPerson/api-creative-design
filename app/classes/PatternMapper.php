<?php

class PatternMapper extends Mapper
{
	
	
	public function getPatterns(){
		$sql = "SELECT * FROM `patterns`";
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

		$sql = "SELECT * FROM patterns WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new PatternEntity($row);
		return $results;
	}
	

	public function save(PatternEntity $pattern){
		$sql = "INSERT INTO `patterns` (`id`, `name`, `pattern_path`, `date_added`) VALUES (NULL, '{$pattern->getName()}', '{$pattern->getPatternPath()}', NOW())";
		$result = mysql_query($sql);
		return $result;
	}


}	

 