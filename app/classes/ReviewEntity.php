<?php

/**
* 
*/
class ReviewEntity
{
    protected $reviewId;
    protected $productId;
    protected $productType;
    protected $comment;
    protected $stars;
    protected $email;
    protected $name;

    function __construct(array $data)
    {
        //check if the data exist or not
        if (isset($data)) {
            $this->reviewId = $data['review_id'];
            $this->productId = $data['product_id'];
            $this->productType = $data['product_type'];
            $this->comment = $data['comment'];
            $this->stars = $data['stars'];
            $this->email = $data['email'];
            $this->name = $data['name'];
        }
    }

    public function getReviewId() {
        return $this->reviewId;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function getProductType() {
        return $this->productType;
    }

    public function getComment() {
        return $this->comment;
    }

    public function getStars() {
        return $this->stars;
    }

    public function getEmail() {
        return $this->email;
    }
    public function getName() {
        return $this->name;
    }



}