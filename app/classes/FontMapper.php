<?php

class FontMapper extends Mapper
{
	
	
	public function getFonts(){
		$sql = "SELECT * FROM `fonts`";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new FontEntity($row);
		} 
		return $results;
		
	}

	public function getFontById($id){

		$sql = "SELECT * FROM fonts WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new FontEntity($row);
		return $results;
	}
	

	public function save(FontEntity $font){
		$sql = "INSERT INTO `fonts` (`id`, `name`, `filepath`, `date_added`,`date_updated`) VALUES (NULL, '{$font->getName()}', '{$font->getFilePath()}', NOW(), NOW())";
		$result = mysql_query($sql);
		return $result;
	}


}	

 