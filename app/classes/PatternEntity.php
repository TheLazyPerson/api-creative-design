<?php

/**
* 
*/
class PatternEntity
{
    protected $id;
    protected $name;
    protected $patternPath;

    function __construct(array $data)
    {
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->patternPath = $data['pattern_path'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPatternPath() {
        return $this->patternPath;
    }


}