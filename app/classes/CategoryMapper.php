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
	public function getSubCategories(){
		$sql = "SELECT s1.id, s1.name, s1.description, c1.name as parent FROM subcategories s1 LEFT JOIN categories c1 on s1.parent = c1.id";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new SubCategoryEntity($row);
		} 
		return $results;
		
	}

	public function getCategoryById($id){

		$sql = "SELECT * FROM `categories` WHERE id={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);

		$results = new CategoryEntity($row);
		return $results;
	}
	public function getSubCategoryById($id){

		$sql = "SELECT * FROM `subcategories` WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new SubCategoryEntity($row);
		return $results;
	}
	public function getSubCategoriesByCategoryId($id){

		$sql = "SELECT * FROM `subcategories` WHERE parent ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new SubCategoryEntity($row);
		} 
		return $results;
	}
	

	public function save(CategoryEntity $category){
		
		$sql = "INSERT INTO `categories` (`id`, `name`, `description`, `date_added`, `last_modified`) VALUES (NULL, '{$category->getName()}', '{$category->getDescription()}', NOW(), NOW());";
		
		$result = mysql_query($sql);
		return $result;
	}

	public function saveSub(SubCategoryEntity $subcategory){
		
		$sql = "INSERT INTO `subcategories` (`id`, `name`, `description`, `parent`, `date_added`, `last_modified`) VALUES (NULL, '{$subcategory->getName()}', '{$subcategory->getDescription()}', '{$subcategory->getParent()}', NOW(), NOW());";
		
		$result = mysql_query($sql);
		return $result;
	}

}	

 