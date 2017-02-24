<?php

class ProductMapper extends Mapper
{

	protected $insertedProductId = "";

	public function setProductId($name){
		$sql = "SELECT id FROM `products` WHERE `name`='{$name}' ORDER BY id DESC LIMIT 1";
		$result = mysql_query($sql);
		$output = mysql_fetch_array($result);
		$this->insertedProductId = $output["id"];
		
	}
	public function getProductId(){

		return $this->insertedProductId;
	}

	public function getProducts(){
		$sql = "SELECT p1.id, p1.name, p1.description, p1.addtional_information, p1.notes,  p1.max_characters, p1.per_char_charge, p1.max_font_size, p1.price_after_max_font_size, m1.name as 'material', c1.name as 'category', s1.name as 'subcategory', p1.cod, p1.letter_type, p1.nameplate_used, p1.fitting_place, p1.length, p1.height, p1.weight, p1.depth, p1.trending, p1.font_effect, p1.price, p1.date_added, p1.last_modified, p1.date_available, p1.status, p1.tax_class_id FROM products p1 LEFT JOIN materials m1 ON p1.material= m1.id LEFT JOIN categories c1 ON p1.category = c1.id LEFT JOIN subcategories s1 ON p1.subcategory = s1.id ORDER BY id DESC";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			
			$results[] = new ProductEntity($row);
		} 

		return $results;
		
	}

	public function getImagesOfProductsById($id){
		$results = array();
		$sql = "SELECT * FROM `images` WHERE `product_id` ={$id} AND `product_type` = 2";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ImageEntity($row);
		} 
		return $results;
	}

	public function getMotifsOfProductsById($id){
		$results = array();
		$sql = "SELECT * FROM `product_motif` WHERE `nameplate_id` = '{$id}'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ProductMotifEntity($row);
		} 
		return $results;
	}

	public function getColorsOfProductsById($id){
		$results = array();
		$sql = "SELECT * FROM `product_colors` WHERE `product_id` = '{$id}'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ColorEntity($row);
		} 
		return $results;
	}

	public function getPatternsOfProductsById($id){
		$results = array();
		$sql = "SELECT * FROM `product_patterns` WHERE `product_id` = '{$id}'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
		while ( $row = mysql_fetch_array($result)) {

			$results[] = new ProductPatternEntity($row);
		} 

		return $results;
	}
	public function getFontsOfProductsById($id){
		$results = array();
		$sql = "SELECT * FROM `product_fonts` WHERE `product_id` = '{$id}'";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
		while ( $row = mysql_fetch_array($result)) {

			$results[] = new ProductFontEntity($row);
		} 

		return $results;
	}




	public function getProductByCategoryId($id){

		$sql = "SELECT p1.id, p1.name, p1.description, p1.addtional_information, p1.notes, p1.max_characters, p1.per_char_charge, m1.name as 'material', c1.name as 'category', p1.cod, p1.letter_type, p1.nameplate_used, p1.fitting_place, p1.length, p1.height, p1.weight, p1.depth, p1.trending, p1.price, p1.date_added, p1.last_modified, p1.date_available, p1.status, p1.tax_class_id FROM products p1 LEFT JOIN materials m1 ON p1.material= m1.id LEFT JOIN categories c1 ON p1.category = c1.id  WHERE p1.id ={$id}";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new ProductEntity($row);
		return $results;
	}

	public function getProductById($id){

		$sql = "SELECT p1.id, p1.name, p1.description, p1.addtional_information, p1.notes,  p1.max_characters, p1.per_char_charge, p1.max_font_size, p1.price_after_max_font_size, m1.name as 'material', c1.name as 'category', s1.name as 'subcategory', p1.cod, p1.letter_type, p1.nameplate_used, p1.fitting_place, p1.length, p1.height, p1.weight, p1.depth, p1.trending, p1.font_effect, p1.price, p1.date_added, p1.last_modified, p1.date_available, p1.status, p1.tax_class_id FROM products p1 LEFT JOIN materials m1 ON p1.material= m1.id LEFT JOIN categories c1 ON p1.category = c1.id LEFT JOIN subcategories s1 ON p1.subcategory = s1.id  WHERE p1.id ='{$id}' ORDER BY p1.id DESC";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());

        }
        
		$row = mysql_fetch_array($result);
		$results = new ProductEntity($row);
		return $results;
	}
	public function getTrendingProducts(){

		$sql = "SELECT p1.id, p1.name, p1.description, p1.addtional_information, p1.notes,  p1.max_characters, p1.per_char_charge, p1.max_font_size, p1.price_after_max_font_size, m1.name as 'material', c1.name as 'category', s1.name as 'subcategory', p1.cod, p1.letter_type, p1.nameplate_used, p1.fitting_place, p1.length, p1.height, p1.weight, p1.depth, p1.trending, p1.font_effect, p1.price, p1.date_added, p1.last_modified, p1.date_available, p1.status, p1.tax_class_id FROM products p1 LEFT JOIN materials m1 ON p1.material= m1.id LEFT JOIN categories c1 ON p1.category = c1.id LEFT JOIN subcategories s1 ON p1.subcategory = s1.id  WHERE p1.trending = '1' ORDER BY p1.id DESC";
		$result = mysql_query($sql);

        if (!$result){
            die("Database Query Failed: ". mysql_error());
        }
        $results = [];
		// $rows = mysql_fetch_array($result);
		while ( $row = mysql_fetch_array($result)) {
			$results[] = new ProductEntity($row);
		} 
		return $results;
		
	}
	public function saveImage(ImageEntity $image){

		$sql = "INSERT INTO `images` (`images_id`, `path`, `product_id`, `product_type`) VALUES (NULL, '{$image->getPath()}', '{$image->getProductId()}', '{$image->getProductType()}')";
		$result = mysql_query($sql);
		return $result;
	}

	public function saveColor(ColorEntity $color){

		$sql = "INSERT INTO `product_colors`(`id`, `product_id`, `color_hashcode`, `date_added`) VALUES (NULL,'{$color->getProductId()}','{$color->getColorHashCode()}',NOW())";
		$result = mysql_query($sql);
		return $result;
	}
	public function saveMotif(ProductMotifEntity $productMotif){

		$sql = "INSERT INTO `product_motif`(`id`, `motif_id`, `nameplate_id`, `date_added`) VALUES (NULL,'{$productMotif->getMotifId()}','{$productMotif->getProductId()}',NOW())";
		$result = mysql_query($sql);
		return $result;
	}
	public function savePattern(ProductPatternEntity $productPattern){

		$sql = "INSERT INTO `product_patterns`(`id`, `product_id`, `pattern`, `date_added`) VALUES (NULL,'{$productPattern->getProductId()}','{$productPattern->getPattern()}',NOW())";
		$result = mysql_query($sql);
		return $result;
	}
	public function saveFont(ProductFontEntity $productFont){

		$sql = "INSERT INTO `product_fonts`(`id`, `product_id`, `font_id`, `date_added`, `date_updated`) VALUES (NULL,'{$productFont->getProductId()}','{$productFont->getFontId()}',NOW(), NOW())";
		$result = mysql_query($sql);
		return $result;
	}
	public function save(ProductEntity $product){
		
		$sql = "INSERT INTO `products` (`id`, `name`, `description`, `addtional_information`, `notes`,`max_characters`, `per_char_charge`,`max_font_size`, `price_after_max_font_size`,`material`, `category`,`subcategory`, `cod`, `letter_type`, `nameplate_used`, `fitting_place`, `length`, `height`, `depth`, `weight`, `trending`,`font_effect`, `price`, `date_added`, `last_modified`, `date_available`, `status`, `tax_class_id`) VALUES (NULL, '{$product->getName()}', '{$product->getDescription()}', '{$product->getAddtionalInformation()}','{$product->getNotes()}' ,'{$product->getMaxCharacters()}', '{$product->getPerCharPriceAfterMaxCharacters()}', '{$product->getMaxFontSize()}', '{$product->getPerCharPriceAfterMaxFontSize()}','{$product->getMaterial()}', '{$product->getCategory()}', '{$product->getSubCategory()}', '{$product->getCOD()}', '{$product->getLetterType()}', '{$product->getNameplateUsed()}', '{$product->getFittingPlace()}', '{$product->getLength()}', '{$product->getHeight()}', '{$product->getDepth()}', '{$product->getWeight()}', '{$product->getTrending()}', '{$product->getFontEffect()}','{$product->getPrice()}', NOW(), NOW(), NOW(), '1', '1');";
		

		$result = mysql_query($sql);
		return $result;
	}

}	

 