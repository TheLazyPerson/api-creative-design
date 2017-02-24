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
    protected $notes;
    protected $length;
    protected $height;
    protected $depth;
    protected $weight;
	protected $material;
    protected $category;
    protected $subcategory;
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
            $this->notes = $data['notes'];
            $this->length = $data['length'];
            $this->height = $data['height'];
            $this->depth = $data['depth'];
            $this->weight = $data['weight'];
            $this->category = $data['category'];
            $this->subcategory = $data['subcategory'];
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
    public function getNotes() {
        return $this->notes;
    }
     public function getLength() {
        return $this->length;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getDepth() {
        return $this->depth;
    }

    public function getWeight() {
        return $this->weight;
    }


    public function getCategory() {
        return $this->category;
    }

    public function getSubCategory() {
        return $this->subcategory;
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