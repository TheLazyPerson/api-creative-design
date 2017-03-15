<?php

class ReviewMapper extends Mapper
{
	protected $insertedProductId = "";
	
	public function getReviews(){
		$sql = "SELECT * FROM `customer_reviews` WHERE status='1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ReviewEntity($row);
		} 
		return $results;
	}

	public function getReviewsForProduct($productid, $producttype){
		$sql = "SELECT * FROM `customer_reviews` WHERE `product_id`= {$productid} AND `product_type` = {$producttype} AND `status`= '1'";

		$result = mysql_query($sql);
        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ReviewEntity($row);
			
		} 
		return $results;
	}

	public function getReviewById($id){

		$sql = "SELECT * FROM `customer_reviews` WHERE `review_id`='{$id}' AND `status`= '1' ";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new ReviewEntity($row);
		return $results;
	}
	

	public function save(ReviewEntity $review){
		$sql = "INSERT INTO `customer_reviews`(`review_id`, `product_id`, `product_type`, `comment`, `stars`, `email`, `name`, `status`, `date_added`) VALUES (NULL, '{$review->getProductId()}','{$review->getProductType()}','{$review->getComment()}','{$review->getStars()}','{$review->getEmail()}','{$review->getName()}','1',NOW())";
		$result = mysql_query($sql);
		return $result;
	}


	
	public function delete($id){
		$sql = "UPDATE `customer_reviews` SET `status`= 0 WHERE `review_id`= {$id}";
		
		$result = mysql_query($sql);
		return $result;
	}



}	

 