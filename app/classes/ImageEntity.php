<?php

/**
* 
*/
class ImageEntity
{
	protected $id;
	protected $path;
	protected $product_id;
    protected $product_type;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['images_id'];
            $this->path = $data['path'];
            $this->product_id = $data['product_id'];
            $this->product_type = $data['product_type'];
        }
	}

	public function getId() {
        return $this->id;
    }

    public function getPath() {
        return $this->path;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getProductType() {
        return $this->product_type;
    }
    
}