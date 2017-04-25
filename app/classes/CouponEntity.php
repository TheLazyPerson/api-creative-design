<?php

/**
* 
*/
class CouponEntity
{
    protected $id;
    protected $code;
    protected $startDate;
    protected $endDate;
    protected $discount;

    function __construct(array $data)
    {
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->code = $data['code'];
            $this->startDate = $data['start_date'];
            $this->endDate = $data['end_date'];
            $this->discount = $data['discount'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getCode() {
        return $this->code;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function getDiscount() {
        return $this->discount;
    }

    


}