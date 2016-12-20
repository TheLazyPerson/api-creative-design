<?php 

/**
* 
*/
class CustomerMapper extends Mapper
{
	

	function getUserDetailsByUserId($userid){
		//get the id of user
		
		$sql = "SELECT * FROM `customers` WHERE `email_address` = '$userid'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        $results = [];
		$row = mysql_fetch_array($result)	
		$results[] = new CustomerEntity($row);

		return $results;
	}


}