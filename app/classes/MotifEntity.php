
<?php

/**
* 
*/
class MotifEntity
{
	protected $id;
	protected $name;
	protected $motifPath;
	protected $description;

	function __construct(array $data)
	{
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->motifPath = $data['motif_path'];
            $this->description = $data['description'];
        }
	}

	public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getMotifPath() {
        return $this->motifPath;
    }

    public function getDescription() {
        return $this->description;
    }
    
}