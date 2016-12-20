<?php 

/**
* 
*/
class CartMapper extends Mapper
{
	protected $userid;
	function __construct($userid)
	{
		$this->userid = $userid;
	}

	function getCart($customer){
		//get the id of user
		$userid = $customer->getId();
		$sql = "SELECT id FROM customer";
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

	function getUserId(){
		return $this->userid;
	}
	function setUserId($userid){
		$this->userid = $userid;
	}
}