<?php

/**
* 
*/
class TestimonialEntity
{
	protected $id;
	protected $message;
    protected $author;
	protected $place;
    protected $date;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->message = $data['message'];
            $this->author = $data['author'];
            $this->place = $data['place'];
            $this->date = $data['date'];
        }
	}
    
	public function getId() {
        return $this->id;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getPlace() {
        return $this->place;
    }

    public function getDate() {
        return $this->date;
    }
    
}