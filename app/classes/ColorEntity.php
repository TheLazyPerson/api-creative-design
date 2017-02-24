<?php

/**
* 
*/
class ColorEntity
{
	protected $id;
	protected $productid;
	protected $colorHashcode;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->productid = $data['product_id'];
            $this->colorHashcode = $data['color_hashcode'];
        }
	}

	public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->productid;
    }

    public function getColorHashCode() {
        return $this->colorHashcode;
    }
    
}