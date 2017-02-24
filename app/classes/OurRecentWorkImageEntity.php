<?php

/**
* 
*/
class OurRecentWorkImageEntity
{
	protected $id;
	protected $imagePath;
	protected $imageNumber;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->imagePath = $data['image_path'];
            $this->imageNumber = $data['image_number'];
        }
	}

	public function getId() {
        return $this->id;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function getImageNumber() {
        return $this->imageNumber;
    }
    
}