<?php

/**
* 
*/
class ProductFontEntity
{
	protected $id;
	protected $productid;
	protected $fontid;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->productid = $data['product_id'];
            $this->fontid = $data['font_id'];
        }
	}

	public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->productid;
    }

    public function getFontId() {
        return $this->fontid;
    }
    
}