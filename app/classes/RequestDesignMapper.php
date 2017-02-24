<?php

class RequestDesignMapper extends Mapper
{
	
	
	public function getRequestedDesigns(){
		$sql = "SELECT * FROM `requested_designs`";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new RequestDesignEntity($row);
		} 
		return $results;
		
	}

	public function getRequestedDesignsById($id){

		$sql = "SELECT * FROM requested_designs WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new RequestDesignEntity($row);
		return $results;
	}
	

	public function save(RequestDesignEntity $request){
		$sql = "INSERT INTO `requested_designs` (`id`, `name`, `email`, `contact_number`, `requirements`) VALUES (NULL, '{$request->getName()}', '{$request->getEmail()}', '{$request->getContactNumber()}', '{$request->getRequirements()}')";
		$result = mysql_query($sql);
		return $result;
	}


}	

 