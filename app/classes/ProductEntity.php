<?php

/**
* 
*/
class ProductEntity
{
	protected $id;
	protected $name;
    protected $price;
	protected $description;
    protected $additionalInformation;
    protected $notes;
	protected $maxCharacters;
    protected $perCharPriceAfterMaxCharacters;
    protected $maxFontSize;
    protected $perCharPriceAfterMaxFontSize;
	protected $material;
    protected $category;
    protected $subcategory;
	protected $cod;
	protected $letterType;
	protected $nameplateUsed;
	protected $fittingPlace;
	protected $length;
	protected $height;
	protected $depth;
	protected $weight;
    protected $trending;
    protected $fonteffect;
    protected $status;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->description = $data['description'];
            $this->additionalInformation = $data['addtional_information'];
            $this->notes = $data['notes'];
            $this->perCharPriceAfterMaxCharacters = $data['per_char_charge'];
            $this->maxCharacters = $data['max_characters'];
            $this->perCharPriceAfterMaxFontSize = $data['price_after_max_font_size'];
            $this->maxFontSize = $data['max_font_size'];
            $this->material = $data['material'];
            $this->category = $data['category'];
            $this->subcategory = $data['subcategory'];
            $this->cod = $data['cod'];
            $this->letterType = $data['letter_type'];
            $this->nameplateUsed = $data['nameplate_used'];
            $this->fittingPlace = $data['fitting_place'];
            $this->length = $data['length'];
            $this->depth = $data['depth'];
            $this->height = $data['height'];
            $this->weight = $data['weight'];
            $this->price = $data['price'];
            $this->trending = $data['trending'];
            $this->status = $data['status'];
            $this->fonteffect = $data['font_effect'];
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

    public function getMaxCharacters() {
        return $this->maxCharacters;
    }

    public function getPerCharPriceAfterMaxCharacters() {
        return $this->perCharPriceAfterMaxCharacters;
    }

    public function getMaxFontSize() {
        return $this->maxFontSize;
    }

    public function getPerCharPriceAfterMaxFontSize() {
        return $this->perCharPriceAfterMaxFontSize;
    }
    public function getMaterial() {
        return $this->material;
    }
    public function getCategory() {
        return $this->category;
    }
    public function getSubCategory() {
        return $this->subcategory;
    }

    public function getCOD() {
        return $this->cod;
    }

    public function getLetterType() {
        return $this->letterType;
    }

    public function getNameplateUsed() {
        return $this->nameplateUsed;
    }

    public function getFittingPlace() {
        return $this->fittingPlace;
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

    public function getPrice() {
        return $this->price;
    }

    public function getTrending() {
        return $this->trending;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getFontEffect() {
        return $this->fonteffect;
    }
    

}