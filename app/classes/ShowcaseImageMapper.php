<?php

class ShowcaseImageMapper extends Mapper
{
	public function getShowcaseImages(){
		$sql = "SELECT s1.* FROM showcase s1 LEFT JOIN showcase s2 ON (s1.image_number = s2.image_number AND s1.id < s2.id) WHERE s2.id IS NULL";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ShowcaseImageEntity($row);
		} 
		return $results;
		
	}
	public function save(ShowcaseImageEntity $showcaseImage){
		
		$sql = "INSERT INTO `showcase` (`id`, `image_path`, `image_number`, `date_added`) VALUES (NULL, '{$showcaseImage->getImagePath()}', '{$showcaseImage->getImageNumber()}', NOW());";
		
		$result = mysql_query($sql);
		return $result;
	}

}	

 