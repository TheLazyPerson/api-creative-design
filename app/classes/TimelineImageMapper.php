<?php

class TimelineImageMapper extends Mapper
{
	public function getTimelineImages(){
		$sql = "SELECT t1.* FROM timeline t1 LEFT JOIN timeline t2 ON (t1.image_number = t2.image_number AND t1.id < t2.id) WHERE t2.id IS NULL";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new TimelineImageEntity($row);
		} 
		return $results;
		
	}


	public function save(TimelineImageEntity $timelineImage){
		
		$sql = "INSERT INTO `timeline` (`id`, `image_path`, `image_number`) VALUES (NULL, '{$timelineImage->getImagePath()}', '{$timelineImage->getImageNumber()}');";
		
		$result = mysql_query($sql);
		return $result;
	}

}	

 