<?php

/**
* 
*/
class ProductEntity
{
	protected $id;
	protected $name;
	protected $description;
	protected $max_rows;
	protected $max_characters;
	protected $material;
	protected $cod;
	protected $letter_type;
	protected $nameplate_used;
	protected $fitting_place;
	protected $length;
	protected $height;
	protected $depth;
	protected $weight;
	protected $images;
	protected $price;


	function __construct(array $data)
	{
		$this->id = $data['id'];
		$this->name = $data['name'];
		$this->description = $data['description'];
		$this->max_rows = $data['max_rows'];
		$this->max_characters = $data['max_characters'];
		$this->material = $data['material'];
		$this->cod = $data['cod'];
		$this->letter_type = $data['letter_type'];
		$this->nameplate_used = $data['nameplate_used'];
		$this->fitting_place = $data['fitting_place'];
		$this->length = $data['length'];
		$this->height = $data['height'];
		$this->depth = $data['depth'];
		$this->weight = $data['depth'];
		$this->images = $data['images_id'];
		$this->price = $data['price'];
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

    public function getMaxRows() {
        return $this->max_rows;
    }

    public function getMaxCharacters() {
        return $this->max_characters;
    }

    public function getMaterial() {
        return $this->material;
    }

    public function getCOD() {
        return $this->cod;
    }

    public function getLetterType() {
        return $this->letter_type;
    }

    public function getNameplateUsed() {
        return $this->nameplate_used;
    }

    public function getFittingPlace() {
        return $this->fitting_place;
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

    public function getImages() {
        return $this->images;
    }

    public function getPrice() {
        return $this->price;
    }

    

}