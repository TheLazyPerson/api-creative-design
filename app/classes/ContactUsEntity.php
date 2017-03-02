<?php

/**
* 
*/
class ContactUsEntity
{
	protected $id;
	protected $name;
    protected $email;
	protected $subject;
    protected $message;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->email = $data['email'];
            $this->subject = $data['subject'];
            $this->message = $data['message'];
        }
	}
    
	public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getMessage() {
        return $this->message;
    }
    
}