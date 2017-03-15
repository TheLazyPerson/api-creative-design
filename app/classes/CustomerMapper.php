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

	function sendEmailToUser($email,$message,$subject){      
		$mail = new PHPMailer();
		$mail->IsSMTP(); 
		$mail->SMTPDebug  = 0;                     
		$mail->SMTPAuth   = NULL;                  
		$mail->SMTPSecure = NULL;                 
		$mail->Host       = "localhost";      
		$mail->Port       = 25;             
		$mail->AddAddress($email);
		$mail->Username="contact@kalakrutiindia.com";  
		$mail->Password="kalakruti123";            
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

	function checkIfUserExist($userid){
		//get the id of user
		
		$sql = "SELECT * FROM `customers` WHERE `email_address` = '$userid'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        if (mysql_num_rows($result) > 0) {
	
			return false;
        }
        return true;
	}


	function registerNewUser(CustomerEntity $customer){

	}


	function generateToken(){
		return md5(uniqid(rand()));
	}

}