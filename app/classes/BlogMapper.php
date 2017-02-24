<?php

class BlogMapper extends Mapper
{
	protected $insertedProductId = "";
	
	public function getBlogs(){
		$sql = "SELECT * FROM `blog`";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new BlogEntity($row);
		} 
		return $results;
	}

	public function getBlogsForHome(){
		$sql = "SELECT id,title,short_description,content,image_path,DATE_FORMAT(date_added,\"%M %d,%Y\") as released ,visible FROM blog ORDER BY id DESC LIMIT 4";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		while ( $row = mysql_fetch_array($result)) {
			$blog = new BlogEntity($row);
			$blog->setReleased($row['released']);
			$results[] = $blog;
		} 
		return $results;
		
	}

	public function getBlogById($id){

		$sql = "SELECT * FROM blog WHERE id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new BlogEntity($row);
		return $results;
	}
	

	public function save(BlogEntity $blog){
		$blogContent = addslashes($blog->getContent());
		$blogShortDescription = addslashes($blog->getShortDescription());
		$sql = "INSERT INTO `blog` (`id`, `title`, `short_description`, `content`, `image_path`, `date_added`, `last_updated`, `visible`) VALUES (NULL, '{$blog->getTitle()}', '{$blogShortDescription}', '{ $blogContent }', '{$blog->getImagePath()}', NOW(), NOW(), '{$blog->isVisible()}}')";
		$result = mysql_query($sql);
		return $result;
	}


}	

 