<?php

/**
* 
*/
class RelatedProductEntity
{
    protected $id;
    protected $productId;
    protected $relatedProductId;

    function __construct(array $data)
    {
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->productId = $data['product_id'];
            $this->relatedProductId = $data['related_product_id'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function getRelatedProductId() {
        return $this->relatedProductId;
    }

    
}