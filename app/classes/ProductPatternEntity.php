<?php

/**
* 
*/
class ProductPatternEntity
{
	protected $id;
	protected $productid;
	protected $pattern;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->productid = $data['product_id'];
            $this->pattern = $data['pattern'];
        }
	}

	public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->productid;
    }

    public function getPattern() {
        return $this->pattern;
    }
    
}