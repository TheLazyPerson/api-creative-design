<?php

class RelatedProductMapper extends Mapper
{
	
	public function getRelatedProductsByProductId($id){

		$sql = "SELECT * FROM `products_related` WHERE `product_id` = {$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			
			$results[] = new RelatedProductEntity($row);
		} 

		return $results;
	}
	

	public function save(RelatedProductEntity $relatedProduct){
		$sql = "INSERT INTO `products_related`(`id`, `product_id`, `related_product_id`, `date_added`, `date_updated`) VALUES (NULL,'{$relatedProduct->getProductId()}','{$relatedProduct->getRelatedProductId()}',NOW(),NOW())";
		$result = mysql_query($sql);
		return $result;
	}


}	

 