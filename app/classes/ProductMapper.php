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
	
}	

 