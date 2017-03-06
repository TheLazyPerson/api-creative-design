<?php

class NormalProductMapper extends Mapper
{
	protected $insertedProductId = "";
	public function getProducts(){
		$sql = "SELECT n1.id, n1.name, n1.description, n1.addtional_information, n1.notes, n1.length, n1.height, n1.depth, n1.weight ,c1.name as category, s1.name as subcategory ,m1.name as material, n1.cod, n1.price, n1.status, n1.featured FROM `normal_products` n1 LEFT JOIN materials m1 ON n1.material=m1.id LEFT JOIN categories c1 on n1.category=c1.id LEFT JOIN subcategories s1 on n1.subcategory=s1.id WHERE n1.status = '1' ORDER BY id DESC";
		$result = mysql_query($sql);
        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new NormalProductEntity($row);
		} 
		return $results;
		
	}

	public function getProductById($id){

		$sql = "SELECT n1.id, n1.name, n1.description, n1.addtional_information, n1.notes, n1.length, n1.height, n1.depth, n1.weight,c1.name as category, s1.name as subcategory ,m1.name as material, n1.cod, n1.price, n1.status, n1.featured FROM `normal_products` n1 LEFT JOIN materials m1 ON n1.material=m1.id LEFT JOIN categories c1 on n1.category=c1.id LEFT JOIN subcategories s1 ON n1.subcategory=s1.id WHERE n1.id ={$id} AND n1.status = '1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new NormalProductEntity($row);
		return $results;
	}
	public function getImagesOfProductsById($id){
		$results = array();
		$sql = "SELECT * FROM `images` WHERE `product_id` ={$id} AND `product_type` = 1";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ImageEntity($row);
		} 
		return $results;
	}

	public function getFeaturedProducts(){

		$sql = "SELECT n1.id, n1.name, n1.description, n1.addtional_information, n1.notes, n1.length, n1.height, n1.depth, n1.weight, c1.name as category, s1.name as subcategory,m1.name as material, n1.cod, n1.price, n1.status, n1.featured FROM `normal_products` n1 LEFT JOIN materials m1 ON n1.material=m1.id LEFT JOIN categories c1 on n1.category=c1.id LEFT JOIN subcategories s1 on n1.subcategory=s1.id WHERE n1.featured = '1' AND n1.status = '1' ORDER BY id DESC";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new NormalProductEntity($row);
		} 
		return $results;
		
	}
	
	public function saveImage(ImageEntity $image){

		$sql = "INSERT INTO `images` (`images_id`, `path`, `product_id`, `image_number`,`product_type`) VALUES (NULL, '{$image->getPath()}', '{$image->getProductId()}', '{$image->getImageNumber()}' , '{$image->getProductType()}')";
		$result = mysql_query($sql);
		return $result;
	}
	public function updateImage(ImageEntity $image){

		$sql = "UPDATE `images` SET `path`='{$image->getPath()}' WHERE `product_id`='{$image->getProductId()}' AND `image_number`='{$image->getImageNumber()}' AND `product_type`='{$image->getProductType()}'";
		$result = mysql_query($sql);
		return $result;
	}

	public function setProductId($name){
		$sql = "SELECT id FROM `normal_products` WHERE `name`='{$name}' ORDER BY id DESC LIMIT 1";
		$result = mysql_query($sql);
		$output = mysql_fetch_array($result);
		$this->insertedProductId = $output["id"];
		
	}
	public function getProductId(){

		return $this->insertedProductId;
	}

	public function save(NormalProductEntity $product){
		
		$sql = "INSERT INTO `normal_products` (`id`, `name`, `description`, `addtional_information`,`notes`, `length`, `height`, `depth`, `weight`, `category`,`subcategory`, `material`, `cod`,  `price`, `date_added`, `last_modified`,  `status`, `featured` ,`tax_class_id`) VALUES (NULL,'{$product->getName()}', '{$product->getDescription()}', '{$product->getAddtionalInformation()}','{$product->getNotes()}','{$product->getLength()}', '{$product->getHeight()}','{$product->getDepth()}','{$product->getWeight()}','{$product->getCategory()}','{$product->getSubCategory()}','{$product->getMaterial()}', '{$product->getCOD()}', '{$product->getPrice()}', NOW(), NOW(), '1', '{$product->getFeatured()}' ,'1')";
		
		$result = mysql_query($sql);
		return $result;
	}
	public function update(NormalProductEntity $product){
		
		$sql = "UPDATE `normal_products` SET `name`='{$product->getName()}',`description`='{$product->getDescription()}',`addtional_information`='{$product->getAddtionalInformation()}',`notes`='{$product->getNotes()}',`length`='{$product->getLength()}',`height`='{$product->getHeight()}',`depth`='{$product->getDepth()}',`weight`='{$product->getWeight()}',`category`='{$product->getCategory()}',`subcategory`='{$product->getSubCategory()}',`material`='{$product->getMaterial()}',`cod`='{$product->getCOD()}',`price`='{$product->getPrice()}',`last_modified`=NOW(),`featured`='{$product->getFeatured()}' WHERE `id`='{$product->getId()}' ";
		
		$result = mysql_query($sql);
		return $result;
	}

	
	public function delete($id){
		
		$sql = "UPDATE `normal_products` SET `status`= 0 WHERE `id`= {$id}";
		
		$result = mysql_query($sql);
		return $result;
	}

	


}	

 