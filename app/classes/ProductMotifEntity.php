<?php

/**
* 
*/
class ProductMotifEntity
{
	protected $id;
	protected $productid;
	protected $motifid;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->productid = $data['nameplate_id'];
            $this->motifid = $data['motif_id'];
        }
	}

	public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->productid;
    }

    public function getMotifId() {
        return $this->motifid;
    }
    
}