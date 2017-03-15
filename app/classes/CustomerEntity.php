<?php 

/**
* 
*/
class CustomerEntity
{
	protected $id;
	protected $firstname;
	protected $lastname;
	protected $email_address;
	protected $phone;
	protected $city;
	protected $password;
	protected $token_code;

	function __construct(array $data)
	{
		if (isset($data)) {
			$this->id = $data["id"];
			$this->firstname = $data["firstname"];
			$this->lastname = $data["lastname"];
			$this->email_address = $data["email_address"];
			$this->phone = $data["phone"];
			$this->city = $data["city"];
			$this->password = $data["password"];
			$this->token_code = $data["token_code"];
		}
	}

	public function getId(){
		return $this->id;
	}
	public function getFirstName(){
		return $this->firstname;
	}
	public function getLastName(){
		return $this->lastname;
	}
	public function getEmailAddress(){
		return $this->email_address;
	}
	public function getPhoneNumber(){
		return $this->phone;
	}
	public function getCity(){
		return $this->city;
	}
	public function getPassword(){
		return $this->password;
	}
	public function getTokenCode(){
		return $this->token_code;
	}
}