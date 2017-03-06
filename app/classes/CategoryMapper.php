<?php

class CategoryMapper extends Mapper
{
	public function getCategories(){
		$sql = "SELECT * FROM categories WHERE status='1'";
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
		$sql = "SELECT s1.id, s1.name, s1.description, c1.name as parent FROM subcategories s1 LEFT JOIN categories c1 on s1.parent = c1.id  WHERE s1.status='1'";
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

		$sql = "SELECT * FROM `categories` WHERE id={$id} AND status='1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
		$row = mysql_fetch_array($result);

		$results = new CategoryEntity($row);
		return $results;
	}
	public function getSubCategoryById($id){

		$sql = "SELECT * FROM `subcategories` WHERE id={$id} AND status='1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }

		$row = mysql_fetch_array($result);

		$results = new SubCategoryEntity($row);
		return $results;
	}
	public function getSubCategoriesByCategoryId($id){

		$sql = "SELECT * FROM `subcategories` WHERE parent ={$id} AND status='1'";
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
		
		$sql = "INSERT INTO `categories` (`id`, `name`, `description`, `status` ,`date_added`, `last_modified`) VALUES (NULL, '{$category->getName()}', '{$category->getDescription()}','1' ,NOW(), NOW());";
		
		$result = mysql_query($sql);
		return $result;
	}

	public function saveSub(SubCategoryEntity $subcategory){
		
		$sql = "INSERT INTO `subcategories` (`id`, `name`, `description`, `parent`, `status`, `date_added`, `last_modified`) VALUES (NULL, '{$subcategory->getName()}', '{$subcategory->getDescription()}', '{$subcategory->getParent()}','1' ,NOW(), NOW());";
		
		$result = mysql_query($sql);
		return $result;
	}

	public function updateCategory(CategoryEntity $category){
		
		$sql = "UPDATE `categories` SET `name`='{$category->getName()}',`description`='{$category->getDescription()}',`status`='1',`last_modified`= NOW() WHERE id={$category->getId()}";
		
		$result = mysql_query($sql);
		return $result;
	}
	public function updateSubCategory(SubCategoryEntity $subcategory){
		
		$sql = "UPDATE `subcategories` SET `name`='{$subcategory->getName()}',`description`='{$subcategory->getDescription()}',`parent`='{$subcategory->getParent()}',`status`='1',`last_modified`=NOW() WHERE id={$subcategory->getId()}";
		
		$result = mysql_query($sql);
		return $result;
	}

	public function deleteCategory($id){
		$sql = "UPDATE `categories` SET `status`= 0 WHERE `id`= {$id}";
		$result = mysql_query($sql);
		return $result;
	}

	public function deleteSubCategory($id){
		$sql = "UPDATE `subcategories` SET `status`= 0 WHERE `id`= {$id}";
		$result = mysql_query($sql);
		return $result;
	}

}	

 