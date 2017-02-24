<?php

/**
* 
*/
class RequestDesignEntity
{
    protected $id;
    protected $name;
    protected $email;
    protected $contactNo;
    protected $requirements;

    function __construct(array $data)
    {
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->email = $data['email'];
            $this->contactNo = $data['contact_number'];
            $this->requirements = $data['requirements'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getContactNumber() {
        return $this->contactNo;
    }
    public function getRequirements() {
        return $this->requirements;
    }

}