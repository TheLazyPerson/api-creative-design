<?php

class ProductMapper extends Mapper
{
	public function getProducts(){
		$sql = "SELECT * FROM products";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			
			$results[] = new ProductEntity($row);
		} 

		return $results;
		
	}

	public function getProductById($id){

		$sql = "SELECT * FROM products WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new ProductEntity($row);
		return $results;
	}
	
	public function save(ProductEntity $product){
		
		$sql = "INSERT INTO `products` (`id`, `name`, `description`, `max_rows`, `max_characters`, `material`, `cod`, `letter_type`, `nameplate_used`, `fitting_place`, `length`, `height`, `depth`, `weight`, `images_id`, `price`, `date_added`, `last_modified`, `date_available`, `status`, `tax_class_id`) VALUES (NULL, '{$product->getName()}', '{$product->getDescription()}', '{$product->getMaxRows()}', '{$product->getMaxCharacters()}', '{$product->getMaterial()}', '{$product->getCOD()}', '{$product->getLetterType()}', '{$product->getNameplateUsed()}', '{$product->getFittingPlace()}', '{$product->getLength()}', '{$product->getHeight()}', '{$product->getDepth()}', '{$product->getWeight()}', '{$product->getImages()}', '{$product->getPrice()}', NOW(), NOW(), '2016-12-26 00:00:00', '1', '1');";
		/*$sql = "INSERT INTO `products`(`id`, `name`, `description`, `max_rows`, `max_characters`, `material`, `cod`, `letter_type`, `nameplate_used`, `fitting_place`, `length`, `height`, `depth`, `weight`, `images_id`, `price`, `date_added`, `last_modified`, `date_available`, `status`, `tax_class_id`) VALUES ('".$product->getName()."','".$product->getDescription()."',".$product->getMaxRows().",".$product->getMaxCharacters().",'".$product->getMaterial()."',".$product->getCOD().",'".$product->getLetterType()."','".$product->getNameplateUsed()."','".$product->getFittingPlace()."',".$product->getLength().",".$product->getHeight().",".$product->getDepth().",".$product->getWeight().",".$product->getImages().",".$product->getPrice().",NOW(),NOW(),'2016-12-09 00:00:00',1,1)";*/

		$result = mysql_query($sql);
		return $result;
	}

}	

 