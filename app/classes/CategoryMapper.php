<?php

class CategoryMapper extends Mapper
{
	public function getCategories(){
		$sql = "SELECT * FROM categories";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new CategoryEntity($row);
		} 
		return $results;
		
	}

	public function getCategoryById($id){

		$sql = "SELECT * FROM `categories` WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new CategoryEntity($row);
		return $results;
	}
	
	

	public function save(CategoryEntity $category){
		
		$sql = "INSERT INTO `categories` (`id`, `name`, `description`, `date_added`, `last_modified`) VALUES (NULL, '{$category->getName()}', '{$category->getDescription()}', NOW(), NOW());";
		
		$result = mysql_query($sql);
		return $result;
	}

}	

 