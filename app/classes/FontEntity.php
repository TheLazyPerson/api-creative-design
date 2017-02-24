<?php

/**
* 
*/
class FontEntity
{
    protected $id;
    protected $name;
    protected $filepath;

    function __construct(array $data)
    {
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->filepath = $data['filepath'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getFilePath() {
        return $this->filepath;
    }


}