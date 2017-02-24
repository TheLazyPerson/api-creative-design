<?php

/**
* 
*/
class SubCategoryEntity
{
	protected $id;
	protected $name;
	protected $description;
    protected $parent;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->description = $data['description'];
            $this->parent = $data['parent'];
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
    public function getParent() {
        return $this->parent;
    }
    
}