<?php

class NormalProductMapper extends Mapper
{
	protected $insertedProductId = "";
	
	public function getProducts(){
		$sql = "SELECT * FROM normal_products";
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

		$sql = "SELECT * FROM normal_products WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new NormalProductEntity($row);
		return $results;
	}

	public function getFeaturedProducts(){

		$sql = "SELECT * FROM `normal_products` WHERE `featured` = '1'";
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

		$sql = "INSERT INTO `images` (`images_id`, `path`, `product_id`, `product_type`) VALUES (NULL, '{$image->getPath()}', '{$image->getProductId()}', '{$image->getProductType()}')";
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
		
		$sql = "INSERT INTO `normal_products` (`id`, `name`, `description`, `material`, `cod`,  `price`, `date_added`, `last_modified`,  `status`, `featured` ,`tax_class_id`) VALUES (NULL,'{$product->getName()}', '{$product->getDescription()}','{$product->getMaterial()}', '{$product->getCOD()}', '{$product->getPrice()}', NOW(), NOW(), '1', '{$product->getFeatured()}' ,'1')";
		$this->setProductId($product->getName());
		$result = mysql_query($sql);
		return $result;
	}


}	

 