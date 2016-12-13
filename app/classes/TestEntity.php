<?php

/**
* Test enitity class for healthcheck
*/
class TestEntity 
{
	protected $value;

	public function __construct(array $data) {
        $this->value = $data['value'];
        
    }
    public function getValue() {
        return $this->value;
    }
}