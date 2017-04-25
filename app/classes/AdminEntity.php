<?php 

/**
* 
*/
class AdminEntity
{
	protected $id;
	protected $email;
	protected $password;
	protected $active;
	protected $previledge;
	
	function __construct(array $data)
	{
		if (isset($data)) {
			$this->id = $data["id"];
			$this->email = $data["email"];
			$this->password = $data["password"];
			$this->active = $data["active"];
			$this->previledge = $data["privileged"];
		}
	}

	public function getId(){
		return $this->id;
	}
	public function getEmail(){
		return $this->email;
	}
	public function getPassword(){
		return $this->password;
	}
	public function isActive(){
		return $this->active;
	}
	public function isPrivilege(){
		return $this->previledge;
	}
	
}	