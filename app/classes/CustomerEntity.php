<?php 

/**
* 
*/
class CustomerEntity
{
	protected $id;
	protected $gender;
	protected $firstname;
	protected $lastname;
	protected $dob;
	protected $email_address;
	protected $default_address_id;
	protected $telephone;
	protected $fax;
	protected $password;
	protected $newsletter; 
	function __construct(array $data)
	{
		if (isset($data)) {
			$this->id = $data["id"];
			$this->gender = $data["gender"];
			$this->firstname = $data["firstname"];
			$this->lastname = $data["lastname"];
			$this->dob = $data["dob"];
			$this->email_address = $data["email_address"];
			$this->default_address_id = $data["default_address_id"];
			$this->telephone = $data["telephone"];
			$this->fax = $data["fax"];
			$this->password = $data["password"];
			$this->newsletter = $data["newsletter"];
		}
	}

	public function getId(){
		return $this->id;
	}
	public function getGender(){
		return $this->gender;
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
	public function getDefaultAddress(){
		return $this->default_address_id;
	}
	public function getTelephoneNumber(){
		return $this->telephone;
	}
	public function getFaxNumber(){
		return $this->fax;
	}
	public function getPassword(){
		return $this->password;
	}
	public function isSubscribedToNewsletter(){
		if ($this->newsletter == 'y') {
			return true;
		} else {
			return false
		} 
	}
}