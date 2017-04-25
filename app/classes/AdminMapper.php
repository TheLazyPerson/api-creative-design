<?php 

/**
* 
*/
class AdminMapper extends Mapper
{
	public function getAllUsers(){
		$sql = "SELECT `id`, `email`, `password` ,`active`, `privileged`, `date_added`, `date_updated` FROM `admin_users`";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new AdminEntity($row);
		} 
		return $results;
		
	}

	public function getUser($email){

		$sql = "SELECT * FROM `admin_users` WHERE `email` = '{$email}'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
      	if (mysql_num_rows($result) <= 0) {
	
			return false;
        }

		$row = mysql_fetch_array($result);

		$results = new AdminEntity($row);
		return $results;
	}

	function checkIfUserExist($email){
		//get the id of user
		
		$sql = "SELECT * FROM `admin_users` WHERE `email` = '{$email}'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        if (mysql_num_rows($result) > 0) {
	
			return true;
        }
        return false;
	}
	function getLastInsertedId(){
 		return mysql_insert_id();
 	}

	function registerNewUser(AdminEntity $admin){
		$hashed_password = password_hash($admin->getPassword(), PASSWORD_DEFAULT);
		$sql = "INSERT INTO `admin_users`(`id`, `email`, `password`, `active`, `privileged`, `date_added`, `date_updated`) VALUES (NULL,'{$admin->getEmail()}','{$hashed_password}','{$admin->isActive()}','{$admin->isPrivilege()}',NOW(),NOW())";
		$result = mysql_query($sql);
		return $result;
	}


}