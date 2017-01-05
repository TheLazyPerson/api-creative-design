<?php

/**
* 
*/
class NormalProductEntity
{
	protected $id;
	protected $name;
	protected $description;
    protected $additionalInformation;
	protected $material;
	protected $cod;
	protected $price;
    protected $status;
    protected $featured;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->description = $data['description'];
            $this->additionalInformation = $data['addtional_information'];
            $this->material = $data['material'];
            $this->cod = $data['cod'];
            $this->price = $data['price'];
            $this->status = $data['status'];
            $this->featured = $data['featured'];
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
     public function getAddtionalInformation() {
        return $this->additionalInformation;
    }

    public function getMaterial() {
        return $this->material;
    }

    public function getCOD() {
        return $this->cod;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getStatus() {
        return $this->status;
    }
    public function getFeatured() {
        return $this->featured;
    }

    

}