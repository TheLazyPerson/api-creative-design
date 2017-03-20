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
         if (mysql_num_rows($result) <= 0) {
	
			return false;
        }
        $row = mysql_fetch_array($result);	
		$results = new CustomerEntity($row);

		return $results;
	}



	function sendEmailToUser($email,$message,$subject){      
		$mail = new PHPMailer();
		$mail->IsSMTP(); 
		$mail->SMTPDebug  = 0;      
		
		/*$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "ssl";                 
		$mail->Host       = "smtp.gmail.com";      
		$mail->Port       = 465;             
		   */         
		$mail->SMTPAuth   = NULL;                  
		$mail->SMTPSecure = NULL;                 
		$mail->Host       = "localhost";      
		$mail->Port       = 25;      
		 
		$mail->AddAddress($email);
		
		$mail->Username="no-reply@kalakrutiindia.com";  
		$mail->Password="kalakruti123";  
		
		/*$mail->Username="omkomawar222@gmail.com";  
		$mail->Password="pappa1968"; */           
		$mail->SetFrom('no-reply@kalakrutiindia.com','Kalakruti India');
		$mail->AddReplyTo("no-reply@kalakrutiindia.com","Kalakruti India");
		$mail->Subject = $subject;
		$mail->MsgHTML($message);
		if ($mail->Send()) {
			return true;
		} else {
			return false;
		}
		
 	} 

 	function getLastInsertedId(){
 		return mysql_insert_id();
 	}
	function checkIfUserExist($userid){
		//get the id of user
		
		$sql = "SELECT * FROM `customers` WHERE `email_address` = '$userid'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        if (mysql_num_rows($result) > 0) {
	
			return true;
        }
        return false;
	}

	function verifyUser($id, $token){
		//get the id of user
		
		$sql = "SELECT * FROM `customers` WHERE `id` = '{$id}' AND `token_code` = '{$token}'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        if (mysql_num_rows($result) > 0) {
	
			return true;
        }
        return false;
	}

	function getUserStatus($id, $token){
		$sql = "SELECT * FROM `customers` WHERE `id` = '{$id}' AND `token_code` = '{$token}'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		return $row["status"];
	}
	function getUserStatusByUserId($id){
		$sql = "SELECT * FROM `customers` WHERE `id` = '$id' ";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        
		$row = mysql_fetch_array($result);
		return $row["status"];
	}

	function registerNewUser(CustomerEntity $customer){
		$hashed_password = password_hash($customer->getPassword(), PASSWORD_DEFAULT);
		$sql = "INSERT INTO `customers`(`id`, `firstname`, `lastname`, `email_address`, `phone`, `city`, `password`, `token_code`, `status`, `date_added`) VALUES (NULL,'{$customer->getFirstName()}','{$customer->getLastName()}','{$customer->getEmailAddress()}','{$customer->getPhoneNumber()}','{$customer->getCity()}','{$hashed_password}','{$customer->getTokenCode()}','0',NOW())";
		$result = mysql_query($sql);
		return $result;
	}

	function generateToken(){
		return md5(uniqid(rand()));
	}


	function updateUserStatus($id){
		$sql = "UPDATE `customers` SET `status`='1' WHERE `id`='{$id}'";
		$result = mysql_query($sql);
		return $result;
	}

}