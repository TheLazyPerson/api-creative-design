<?php

class ContactUsMapper extends Mapper
{
	public function getContactInformation(){
		$sql = "SELECT * FROM `contactus`";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ContactUsEntity($row);
		} 
		return $results;
	}	
	
	public function getContactInformationById($id){

		$sql = "SELECT * FROM `contactus` WHERE id={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);

		$results = new ContactUsEntity($row);
		return $results;
	}

	public function save(ContactUsEntity $contactus){
		
		$sql = "INSERT INTO `contactus`(`id`, `name`, `email`, `subject`, `message`) VALUES (NULL,'{$contactus->getName()}','{$contactus->getEmail()}','{$contactus->getSubject()}','{$contactus->getMessage()}');";
		
		$result = mysql_query($sql);
		return $result;
	}


}	

 