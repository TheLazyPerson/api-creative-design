<?php

class CouponMapper extends Mapper
{
	
	public function getCoupons(){
		$sql = "SELECT * FROM `coupons` WHERE active='1'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new CouponEntity($row);
		} 
		return $results;
	}


	public function getCouponById($id){

		$sql = "SELECT * FROM `coupons` WHERE id ={$id} AND active=1";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new CouponEntity($row);
		return $results;
	}
	

	public function save(CouponEntity $coupon){
		$sql = "INSERT INTO `coupons`(`id`, `code`, `start_date`, `end_date`, `discount`, `active`, `date_added`, `last_updated`) VALUES (NULL,'{$coupon->getCode()}','{$coupon->getStartDate()}','{$coupon->getEndDate()}','{$coupon->getDiscount()}',1, NOW(), NOW())";
		$result = mysql_query($sql);
		return $result;
	}


	public function update(CouponEntity $coupon){
		$sql ="UPDATE `coupons` SET `code`='{$coupon->getCode()}',`start_date`='{$coupon->getStartDate()}',`end_date`='{$coupon->getEndDate()}',`discount`='{$coupon->getDiscount()}',`last_updated`=NOW() WHERE `id`='{$coupon->getId()}' AND `active`='1'";
		
		
		$result = mysql_query($sql);
		return $result;
	}
	
	public function delete($id){
		$sql = "UPDATE `coupons` SET `active`= 0 WHERE `id`= {$id}";
		
		$result = mysql_query($sql);
		return $result;
	}



}	

 