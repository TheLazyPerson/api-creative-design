<?php

/**
* 
*/
class PasswordManipulator
{
	protected $password;
	function __construct($password)
	{
		$this->password = $password;
	}


	public function encryptPassword(){
		//encryption logic
	}

	public function decryptPassword(){
		//decryption logic
	}
}