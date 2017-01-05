<?php

/**
* 
*/
class MaterialEntity
{
	protected $id;
	protected $name;
	protected $description;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->description = $data['description'];
        }
	}

	public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }
    
}