<?php

class TestimonialMapper extends Mapper
{
	public function getTestimonials(){
		$sql = "SELECT * FROM testimonials WHERE visible = 1";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new TestimonialEntity($row);
		} 
		return $results;
	}	
	
	public function getTestimonialsById($id){

		$sql = "SELECT * FROM `testimonials` WHERE id={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);

		$results = new TestimonialEntity($row);
		return $results;
	}

	public function save(TestimonialEntity $testimonials){
		
		$sql = "INSERT INTO `testimonials` (`id`, `message`,`author`, `place`, `date`,`date_added` ,`date_updated`,`visible`) VALUES (NULL, '{$testimonials->getMessage()}', '{$testimonials->getAuthor()}','{$testimonials->getPlace()}', '{$testimonials->getDate()}', NOW(), NOW(), 1);";
		
		$result = mysql_query($sql);
		return $result;
	}

	public function update(TestimonialEntity $testimonials){
		$sql = "UPDATE `testimonials` SET `message`='{$testimonials->getMessage()}',`place`='{$testimonials->getPlace()}',`author`='{$testimonials->getAuthor()}',`date`='{$testimonials->getDate()}',`date_updated`= NOW() WHERE id={$testimonials->getId()}";
		
		$result = mysql_query($sql);
		return $result;
	}

	public function delete($id){
		$sql = "UPDATE `testimonials` SET `visible`= 0 WHERE `id`= {$id}";
		
		$result = mysql_query($sql);
		return $result;
	}

}	

 