<?php

/**
* 
*/
class BlogEntity
{
    protected $id;
    protected $title;
    protected $shortDescription;
    protected $content;
    protected $imagePath;
    protected $visible;

    function __construct(array $data)
    {
        //check if the data exist or not
        if (isset($data)) {
            $this->id = $data['id'];
            $this->title = $data['title'];
            $this->shortDescription = $data['short_description'];
            $this->content = $data['content'];
            $this->imagePath = $data['image_path'];
            $this->visible = $data['visible'];
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getShortDescription() {
        return $this->shortDescription;
    }

    public function getContent() {
        return $this->content;
    }

    public function getImagePath() {
        return $this->imagePath;
    }

    public function isVisible() {
        return $this->visible;
    }


}