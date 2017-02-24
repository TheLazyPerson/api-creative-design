<?php

class OurRecentWorkImageMapper extends Mapper
{
	public function getOurRecentWorkImages(){
		$sql = "SELECT r1.* FROM recent r1 LEFT JOIN recent r2 ON (r1.image_number = r2.image_number AND r1.id < r2.id) WHERE r2.id IS NULL";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new OurRecentWorkImageEntity($row);
		} 
		return $results;
		
	}


	public function save(OurRecentWorkImageEntity $ourRecentWork){
		
		$sql = "INSERT INTO `recent` (`id`, `image_path`, `image_number`, `date_added`) VALUES (NULL, '{$ourRecentWork->getImagePath()}', '{$ourRecentWork->getImageNumber()}', NOW());";
		
		$result = mysql_query($sql);
		return $result;
	}

}	

 