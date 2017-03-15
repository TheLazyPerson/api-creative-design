<?php

class BlogMapper extends Mapper
{
	

	public function getBlogById($id){

		$sql = "SELECT * FROM blog WHERE id ={$id} AND visible=1";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new NameplateDesignEntity($row);
		return $results;
	}
	

	public function save(NameplateDesignEntity $nameplate){
		$nameplateJson = addslashes($nameplate->getJson());

		$blogShortDescription = addslashes($blog->getShortDescription());
		$sql = "INSERT INTO `nameplate_designs`(`nameplate_designs_id`, `tnx_id`, `image_path`, `product_id`, `date_added`) VALUES (NULL,'{$nameplate->getTransactionId()}','{$nameplate->getImagePath()}','{$nameplate->getProductId()}',NOW())";
		$result = mysql_query($sql);
		return $result;
	}


	



}	

 