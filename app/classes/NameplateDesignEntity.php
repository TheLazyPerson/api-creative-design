<?php

/**
* 
*/
class NameplateDesignEntity
{
    protected $id;
    protected $tnxId;
    protected $imagePath;
    protected $productId;

    function __construct(array $data)
    {
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->tnxId = $data['tnx_id'];
            $this->imagePath = $data['image_path'];
            $this->productId = $data['product_id'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getTransactionId() {
        return $this->tnxId;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function getProductId() {
        return $this->productId;
    }

}