<?php
session_start();
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';
require '../app/classes/constants.php';

spl_autoload_register(function ($classname) {
    require ("../app/classes/" . $classname . ".php");
});

$config['determineRouteBeforeAppMiddleware'] = false;
$config['displayErrorDetails'] = true;
$config['db']['driver'] = DB_DRIVER;
$config['db']['host'] = DB_HOST;
$config['db']['username'] = DB_USERNAME;
$config['db']['password'] = DB_PASSWORD;
$config['db']['database'] = DB_NAME;
$config['db']['charset'] = DB_CHARSET;
$config['db']['collation'] = DB_COLLATION;
$config['db']['prefix'] = DB_PREFIX;


$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('Creative-Design');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    $pdo = new DbConnect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $db = $pdo->connect();
    return $db;
};

/*
 * Middleware for getting request information	
 */
$checkProxyHeaders = true;
$app->add(new RKA\Middleware\IpAddress($checkProxyHeaders));

$app->add(function (Request $request,Response $response,Callable $next) {
	$headers = $request->getHeaders();
	$ipAddress = $request->getAttribute('ip_address');
	$this->logger->addInfo("Request Recieved from $ipAddress");
	foreach ($headers as $name => $values) {
		// Code .. # for adding data to database for requests
	   	$this->logger->addInfo($name . ": " . implode(", ", $values));
	   	//$this->logger->addInfo($request->getHeader(''));
	}

	if ($request->hasHeader('HTTP_X_FORWARDED_FOR')) {
	    $this->logger->addInfo($request->getHeader('HTTP_X_FORWARDED_FOR'));
	}
	//extract information from request to analyse requests 
    $response = $next($request, $response);
    
	return $response;
});


$app->get('/', function ($request, $response, $args) {
    $ipAddress = $request->getAttribute('ip_address');

    return $response;
});



$app->post('/login', function(Request $request, Response $response) {
	
	
   	return $response->withJson($result);
});

$app->post('/signup', function(Request $request, Response $response) {
	//signup functionality
	
   	return $response->withJson($result);
});


$app->get('/testsqlconnection', function(Request $request, Response $response) {
	
	$this->logger->addInfo("Testing Sql Connection $ipAddress.. ");
	if (isset($this->db)) {
		$this->logger->addInfo("Database Connection Found .. ");
	}
	$testDatabase = new TestDatabaseConnection($this->db);
	
	$data = $testDatabase->testSqlConnection();
	if (isset($data)) {
		$this->logger->addInfo("Data Retrived .. ");
	}
	$result = [];
	foreach ($data as $key) {
		$result["value"] = $key->getValue();
	}
	if ($result["value"] == "1") {
		$this->logger->addInfo("Database Connection Established .. ");
	}
	
   	return $response->withJson($result);
});

/* 
	Pattern Overlay Section 
*/

$app->get('/patterns', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of patterns .. ");
	$patternMapper = new PatternMapper($this->db);
	$result = array();
	$data = $patternMapper->getPatterns();
	if (isset($data) ) {
		$this->logger->addInfo("Pattern's Retrived .. ");
		
		foreach ($data as $key) {
			
			$pattern["id"] = $key->getId();
			$pattern["name"] = $key->getName();
			$pattern["pattern_path"] = $key->getPatternPath();
		
			$result ["patterns"][$key->getId()] = $pattern;
			
		}
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});



$app->post('/pattern/add', function (Request $request, Response $response, $args){
   	$imagesTargetPath = "images/patterns/";
    $patternDetails = $request->getParsedBody();
    $file = $request->getUploadedFiles();
    	
    //add validations here	

    $this->logger->addInfo("Inserting pattern ". $patternDetails["name"] ." into database .. ");
    $patternTemp["id"] = 0;
    $patternTemp["name"] = $patternDetails["name"];
    
	
    $string = $patternTemp["name"];
	$imageno = 1;
	$path = "";
    foreach ($file as $key => $value) {

    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);

	    $ext = "";
	    if ($value->getClientMediaType() == "image/jpeg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/jpg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/png" ) {
	    	$ext = ".png";
	    }
	    if (isset($string) && isset($ext)) {
	    	$path = $imagesTargetPath.$string.$imageno.$ext;
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$patternTemp["pattern_path"] = $path;
	    }
	   
    }
 	$pattern = new PatternEntity($patternTemp);
    $this->logger->addInfo("Created object for pattern ". $pattern->getName() .".. ");

    $motifMapper = new PatternMapper($this->db);
    $isCreated = $motifMapper->save($pattern);

    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot process data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully added pattern {$pattern->getName()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});

/*
	Category Section
 */

$app->get('/categories', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of Categories .. ");
	$categoryMapper = new CategoryMapper($this->db);
	$result = array();
	$data = $categoryMapper->getCategories();
	if (isset($data) ) {
		$this->logger->addInfo("Categories Retrived .. ");
		
		foreach ($data as $key) {

			$id = $key->getId();
			$category["id"] = $key->getId();
			$category["name"] = $key->getName();
			$category["description"] = $key->getDescription();
			
			$subcategories = $categoryMapper->getSubCategoriesByCategoryId($id);
			if (!empty($subcategories)) {
				foreach ($subcategories as $temp) {
					$subcategory["id"] = $temp->getId();
					$subcategory["name"] = $temp->getName();
					$subcategory["description"] = $temp->getDescription();
				
					$category ["subcategories"][$temp->getId()] = $subcategory;
				}
			}else{
				$category ["subcategories"] = null;
			}
			
				
				
				$result ["categories"][$key->getId()] = $category;
			
		}
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});



$app->post('/category/add', function (Request $request, Response $response, $args){
	
    $categoryDetails = $request->getParsedBody();
    	
    //add validations here	

    $this->logger->addInfo("Inserting Category ". $categoryDetails["name"] ." into database .. ");
    $categoryDetails["id"] = 0;
    
    $category = new CategoryEntity($categoryDetails);
    $this->logger->addInfo("Created object for category ". $category->getName() .".. ");
    
    $categoryMapper = new CategoryMapper($this->db);
    $isCreated = $categoryMapper->save($category);
   
    
    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot insert data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully Created category {$category->getName()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});

$app->get('/category/{id}', function (Request $request, Response $response, $args) {

    $categoryid = $args["id"];

	$this->logger->addInfo(" Request Recieved for listing of Sub categories for category .. ");
	$categoryMapper = new CategoryMapper($this->db);
	$result = array();
	$data = $categoryMapper->getSubCategoriesByCategoryId($categoryid);
	if (isset($data) ) {
		$this->logger->addInfo("Sub Categories Retrived .. ");
		
		foreach ($data as $key) {
			
			$subcategory["id"] = $key->getId();
			$subcategory["name"] = $key->getName();
			$subcategory["description"] = $key->getDescription();
		
			$result ["subcategories"][$key->getId()] = $subcategory;
			
		}
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});
$app->delete('/category/{id}', function (Request $request, Response $response, $args) {

    $categoryid = $args["id"];

	$this->logger->addInfo(" Request Recieved for listing of Sub categories for category .. ");
	$categoryMapper = new CategoryMapper($this->db);
	$result = array();
	$data = $categoryMapper->deleteCategory($categoryid);
	if (isset($data) ) {
		$this->logger->addInfo("Sub Categories Retrived .. ");
		
		foreach ($data as $key) {
			
			$subcategory["id"] = $key->getId();
			$subcategory["name"] = $key->getName();
			$subcategory["description"] = $key->getDescription();
		
			$result ["subcategories"][$key->getId()] = $subcategory;
			
		}
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});
/*
	Subcategory Section
 */
$app->get('/subcategories', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of SubCategories .. ");
	$categoryMapper = new CategoryMapper($this->db);
	$result = array();
	$data = $categoryMapper->getSubCategories();
	if (isset($data) ) {
		$this->logger->addInfo("Categories Retrived .. ");
		
		foreach ($data as $key) {
			;
			$subcategory["id"] = $key->getId();
			$subcategory["name"] = $key->getName();
			$subcategory["description"] = $key->getDescription();

			$subcategory["parent"] = $key->getParent();
		
			$result ["subcategories"][$key->getId()] = $subcategory;
			
		}
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});



$app->post('/subcategory/add', function (Request $request, Response $response, $args){
	
    $subcategoryDetails = $request->getParsedBody();
    	
    //add validations here	

    $this->logger->addInfo("Inserting SubCategory ". $subcategoryDetails["name"] ." into database .. ");
    $subcategoryDetails["id"] = 0;
    
    $subcategory = new SubCategoryEntity($subcategoryDetails);
    $this->logger->addInfo("Created object for subcategory ". $subcategory->getName() .".. ");
    
    $categoryMapper = new CategoryMapper($this->db);
    $isCreated = $categoryMapper->saveSub($subcategory);
   
    
    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot insert data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully Created category {$subcategory->getName()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});


/* 
	Fonts Section 
*/

$app->get('/fonts/list', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of fonts .. ");
	$fontMapper = new FontMapper($this->db);
	$result = array();
	$data = $fontMapper->getFonts();
	if (isset($data) ) {
		$this->logger->addInfo("Pattern's Retrived .. ");
		
		foreach ($data as $key) {
			
			$font["id"] = $key->getId();
			$font["name"] = $key->getName();
			$font["filepath"] = $key->getFilePath();
		
			$result ["fonts"][$key->getId()] = $font;
			
		}
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});


$app->get('/fonts/get/{id}', function (Request $request, Response $response, $args){
    $fontid = $args["id"];
    $this->logger->addInfo(" Fetching Font for ". $fontid ." id.. ");

    $fontMapper = new FontMapper($this->db);
	$result = array();
	$data = $fontMapper->getFontById($fontid);
	
	if (isset($data) ) {
		$this->logger->addInfo("Font Retrived .. ");

		$font["id"] = $data->getId();
		$font["name"] = $data->getName();
		$font["filepath"] = $data->getFilePath();
		
		$result ["fonts"] = $font;

   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data for ". $blogid ." id .. ");
	$result["error"] = "cannot process request contact your administrator";

    return $response;
});

$app->post('/fonts/add', function (Request $request, Response $response, $args){
   	$fontsTargetPath = "fonts/";
    $fontDetails = $request->getParsedBody();
    $file = $request->getUploadedFiles();
    	
    //add validations here	

    $this->logger->addInfo("Inserting font ". $fontDetails["name"] ." into database .. ");
    $fontTemp["id"] = 0;
    $fontTemp["name"] = $fontDetails["name"];
    
	
    $string = $fontTemp["name"];
	$imageno = 1;
	$path = "";
    foreach ($file as $key => $value) {

    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);

	    $ext = "";
	    if ($value->getClientMediaType() == "application/x-font-ttf" ) {
	    	$ext = ".ttf";
	    }
	    
	    if (isset($string) && isset($ext)) {
	    	$path = $fontsTargetPath.$string.$imageno.$ext;
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded font ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$fontTemp["filepath"] = $path;
	    }
	   
    }
 	$font = new FontEntity($fontTemp);
    $this->logger->addInfo("Created object for font ". $font->getName() .".. ");

    $fontMapper = new FontMapper($this->db);
    $isCreated = $fontMapper->save($font);

    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot process data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully added font {$font->getName()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});

/*
	Materials Section
 */

$app->get('/materials', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of Materials .. ");
	$materialMapper = new MaterialMapper($this->db);
	$result = array();
	$data = $materialMapper->getMaterials();
	if (isset($data) ) {
		$this->logger->addInfo("Materials Retrived .. ");
		
		foreach ($data as $key) {
			
			$category["id"] = $key->getId();
			$category["name"] = $key->getName();
			$category["description"] = $key->getDescription();
		
			$result ["materials"][$key->getId()] = $category;
			
		}
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});



$app->post('/material/add', function (Request $request, Response $response, $args){
	
    $materialDetails = $request->getParsedBody();
    	
    //add validations here	

    $this->logger->addInfo("Inserting Material ". $materialDetails["name"] ." into database .. ");
    $materialDetails["id"] = 0;
    
    $material = new MaterialEntity($materialDetails);
    $this->logger->addInfo("Created object for material ". $material->getName() .".. ");
    
    $materialMapper = new MaterialMapper($this->db);
    $isCreated = $materialMapper->save($material);
   
    
    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot insert data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully Created material {$material->getName()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});


/*
	Product Section
 */


$app->get('/products/normal', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of products .. ");
	$productMapper = new NormalProductMapper($this->db);
	$result = array();
	$data = $productMapper->getProducts();
	if (isset($data) ) {
		$this->logger->addInfo("Products Retrived .. ");
		
		foreach ($data as $key) {
			
			$images = $productMapper->getImagesOfProductsById($key->getId());
			
			$product["id"] = $key->getId();
			$product["name"] = $key->getName();
			$product["description"] = $key->getDescription();
			$product["additionalInformation"] = $key->getAddtionalInformation();
			$product["notes"] = $key->getNotes();
			$product["length"] = $key->getLength();
			$product["height"] = $key->getHeight();
			$product["depth"] = $key->getDepth();
			$product["weight"] = $key->getWeight();
			$product["category"] = $key->getCategory();
			$product["subcategory"] = $key->getSubCategory();
			$product["material"] = $key->getMaterial();
			$product["cod"] = $key->getCOD();
			$product["price"] = $key->getPrice();
			$product["status"] = $key->getStatus();
			
			$i = 1;
			foreach ($images as $key) {
				$product["images"][$i] = $key->getPath();
				$i++;
			}
			$result ["products"][$key->getId()] = $product;
			
		}
	
		
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});

$app->get('/products/featured', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of featured products .. ");
	$productMapper = new NormalProductMapper($this->db);
	$result = array();
	$data = $productMapper->getFeaturedProducts();
	if (isset($data) ) {
		$this->logger->addInfo("Products Retrived .. ");
		
		foreach ($data as $key) {
			
			$images = $productMapper->getImagesOfProductsById($key->getId());

			$product["id"] = $key->getId();
			$product["name"] = $key->getName();
			$product["description"] = $key->getDescription();
			$product["additionalInformation"] = $key->getAddtionalInformation();
			$product["notes"] = $key->getNotes();
			$product["length"] = $key->getLength();
			$product["height"] = $key->getHeight();
			$product["depth"] = $key->getDepth();
			$product["weight"] = $key->getWeight();
			$product["category"] = $key->getCategory();
			$product["subcategory"] = $key->getSubCategory();
			$product["material"] = $key->getMaterial();
			$product["cod"] = $key->getCOD();
			$product["price"] = $key->getPrice();
			$product["status"] = $key->getStatus();
			$i = 1;
			foreach ($images as $key) {
				$product["images"][$i] = $key->getPath();
				$i++;
			}
			$result["products"][$key->getId()] = $product;
			
		}
	
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});

$app->get('/products/nameplate', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of products .. ");
	$productMapper = new ProductMapper($this->db);
	$patternMapper = new PatternMapper($this->db);
	$motifMapper = new MotifMapper($this->db);
	$fontMapper = new FontMapper($this->db);
			
	$result = array();
	$products = $productMapper->getProducts();
	if (isset($products)) {
		$this->logger->addInfo("Products Retrived .. ");
		foreach ($products as $key) {
			$productid = intval($key->getId());

			$images = $productMapper->getImagesOfProductsById($productid);
			$colors = $productMapper->getColorsOfProductsById($productid);
			$patterns = $productMapper->getPatternsOfProductsById($productid);
			$motifs = $productMapper->getMotifsOfProductsById($productid);
			$fonts = $productMapper->getFontsOfProductsById($productid);
			
			$product["id"] = $key->getId();
			$product["name"] = $key->getName();
			$product["description"] = $key->getDescription();
			$product["addtional_information"] = $key->getAddtionalInformation();
			$product["notes"] = $key->getNotes();
			$product["per_char_charge"] = $key->getPerCharPriceAfterMaxCharacters();
			$product["max_characters"] = $key->getMaxCharacters();
			$product["price_after_max_font_size"] = $key->getPerCharPriceAfterMaxFontSize();
			$product["max_font_size"] = $key->getMaxFontSize();
			$product["material"] = $key->getMaterial();
			$product["nameplate_used"] = $key->getNameplateUsed();
			$product["category"] = $key->getCategory();
			$product["subcategory"] = $key->getSubCategory();
			$product["cod"] = $key->getCOD();
			$product["letter_type"] = $key->getLetterType();
			$product["fitting_place"] = $key->getFittingPlace();
			$product["length"] = $key->getLength();
			$product["height"] = $key->getHeight();
			$product["weight"] = $key->getWeight();
			$product["price"] = $key->getPrice();
			$product["trending"] = $key->getTrending();
			$product["font_effect"] = $key->getFontEffect();

			$images = array_filter($images);
			if (!empty($images)) {
				$i = 1;
				foreach ($images as $image) {
					$product["images"][$i] = $image->getPath();
					$i++;
				}
			}
			if (!empty($colors)) {
				$i = 1;
				foreach ($colors as $color) {
					$product["colors"][$i] = $color->getColorHashCode();
					$i++;
				}
			}
			if (!empty($patterns)) {
				$i = 1;
				foreach ($patterns as $patternTemp) {
					$patternid = $patternTemp->getPattern();
					$pattern = $patternMapper->getPatternById($patternid);

					$product["patterns"][$i]["id"] = $pattern->getId();
					$product["patterns"][$i]["name"] = $pattern->getName();
					$product["patterns"][$i]["pattern_path"] = $pattern->getPatternPath();

					$i++;
				}
			}
			if (!empty($motifs)) {
				$i = 1;
				foreach ($motifs as $motifTemp) {
					$motifid = $motifTemp->getMotifId();
					$motif = $motifMapper->getMotifById($motifid);

					$product["motifs"][$i]["id"] = $motif->getId();
					$product["motifs"][$i]["name"] = $motif->getName();
					$product["motifs"][$i]["motif_path"] = $motif->getMotifPath();
					$product["motifs"][$i]["description"] = $motif->getDescription();

					$i++;
				}
			}

			if (count($fonts) > 0) {
				$i = 1;
				foreach ($fonts as $fontTemp) {
					$fontid = $fontTemp->getFontId();
					$font = $fontMapper->getFontById($fontid);

					$product["fonts"][$i]["id"] = $font->getId();
					$product["fonts"][$i]["name"] = $font->getName();
					$product["fonts"][$i]["filepath"] = $font->getFilePath();
					$i++;
				}
			}
			
			$result ["products"][$productid] = $product;
		}
	
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});
$app->get('/products/trending', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of products .. ");
	$productMapper = new ProductMapper($this->db);
	$patternMapper = new PatternMapper($this->db);
	$motifMapper = new MotifMapper($this->db);
	$result = array();
	$data = $productMapper->getTrendingProducts();
	if (isset($data) ) {
		$this->logger->addInfo("Products Retrived .. ");
		
		foreach ($data as $key) {
			
			$images = $productMapper->getImagesOfProductsById($key->getId());
			$colors = $productMapper->getColorsOfProductsById($key->getId());
			$patterns = $productMapper->getPatternsOfProductsById($key->getId());
			$motifs = $productMapper->getMotifsOfProductsById($key->getId());
			
			$product["id"] = $key->getId();
			$product["name"] = $key->getName();
			$product["description"] = $key->getDescription();
			$product["addtional_information"] = $key->getAddtionalInformation();
			$product["notes"] = $key->getNotes();
			$product["per_char_charge"] = $key->getPerCharPriceAfterMaxCharacters();
			$product["max_characters"] = $key->getMaxCharacters();
			$product["price_after_max_font_size"] = $key->getPerCharPriceAfterMaxFontSize();
			$product["max_font_size"] = $key->getMaxFontSize();
			$product["material"] = $key->getMaterial();
			$product["nameplate_used"] = $key->getNameplateUsed();
			$product["category"] = $key->getCategory();
			$product["subcategory"] = $key->getSubCategory();
			$product["cod"] = $key->getCOD();
			$product["letter_type"] = $key->getLetterType();
			$product["fitting_place"] = $key->getFittingPlace();
			$product["length"] = $key->getLength();
			$product["height"] = $key->getHeight();
			$product["weight"] = $key->getWeight();
			$product["price"] = $key->getPrice();
			$product["trending"] = $key->getTrending();
			$product["font_effect"] = $key->getFontEffect();

			$i = 1;
			foreach ($images as $image) {
				$product["images"][$i] = $image->getPath();
				$i++;
			}
			$i = 1;
			foreach ($colors as $color) {
				$product["colors"][$i] = $color->getColorHashCode();
				$i++;
			}
			$i = 1;
			foreach ($patterns as $key) {
				$patternid = $key->getPattern();
				$pattern = $patternMapper->getPatternById($patternid);

				$product["patterns"][$i]["id"] = $pattern->getId();
				$product["patterns"][$i]["name"] = $pattern->getName();
				$product["patterns"][$i]["pattern_path"] = $pattern->getPatternPath();

				$i++;
			}
			$i = 1;
			foreach ($motifs as $key) {
				$motifid = $key->getMotifId();
				$motif = $motifMapper->getMotifById($motifid);

				$product["motifs"][$i]["id"] = $motif->getId();
				$product["motifs"][$i]["name"] = $motif->getName();
				$product["motifs"][$i]["motif_path"] = $motif->getMotifPath();
				$product["motifs"][$i]["description"] = $motif->getDescription();

				$i++;
			}
			$result ["products"][$key->getId()] = $product;
		}
	
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});

$app->post('/product/normal', function (Request $request, Response $response, $args){
	$imagesTargetPath = "images/normal/";
    $productDetails = $request->getParsedBody();
    
    $files = $request->getUploadedFiles();
    	
    //add validations here	

    $this->logger->addInfo("Inserting product ". $productDetails["name"] ." into database .. ");
    $productDetails["id"] = 0;
	$productDetails["status"] = 0;
	$product = new NormalProductEntity($productDetails);
    $this->logger->addInfo("Created object for product ". $product->getName() .".. ");

    $productMapper = new NormalProductMapper($this->db);
    $isCreated = $productMapper->save($product);
	//validation check remaining
    $productMapper->setProductId($product->getName());
	$string = $product->getName();
	$imageno = 1;
	$path = "";
    foreach ($files as $key => $value) {

    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);

	    $ext = "";
	    if ($value->getClientMediaType() == "image/jpeg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/jpg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/png" ) {
	    	$ext = ".png";
	    }
	    if (isset($string) && isset($ext)) {
	    	$path = $imagesTargetPath.$string.$imageno.$ext;
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$imagedata["images_id"] = "0";
	    	$imagedata["path"] = $path;
	    	$imagedata["product_id"] = $productMapper->getProductId();
	    	$imagedata["product_type"] = 1;
	    	$image = new ImageEntity($imagedata);
	    	$productMapper->saveImage($image);
	    }
	    $imageno++;
    }
 
    
    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully Created product {$product->getName()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});

$app->post('/product/nameplate', function (Request $request, Response $response, $args){

	$imagesTargetPath = "images/nameplates/";
	$productDetails = $request->getParsedBody();
    $files = $request->getUploadedFiles();
    $this->logger->addInfo("Inserting product ". $productDetails["name"] ." into database .. ");
   	 //creating data array to be passed to Product Entity
   	$productDetails["id"] = 0;
	$productDetails["status"] = 0;
	 
    $product = new ProductEntity($productDetails);
    $this->logger->addInfo("Created object for product ". $product->getName() .".. ");
    $productMapper = new ProductMapper($this->db);
    $isCreated = $productMapper->save($product);
    $productMapper->setProductId($product->getName());
	$string = $product->getName();
	if (isset($productDetails["colors"])) {
		$colors = json_decode($productDetails["colors"],true);
		foreach ($colors as $key => $value) {
			$color['id'] = 0;
            $color['product_id'] = $productMapper->getProductId();
            $color['color_hashcode'] = $value;
	    	$colorEntity = new ColorEntity($color);
	    	$productMapper->saveColor($colorEntity);
    	}
	}
	if (isset($productDetails["motifs"])) {
		$motifs = json_decode($productDetails["motifs"],true);
		foreach ($motifs as $key => $value) {
			$motif['id'] = 0;
            $motif['nameplate_id'] = $productMapper->getProductId();
            $motif['motif_id'] = $value;
    		
	    	$motifEntity = new ProductMotifEntity($motif);
	    	$productMapper->saveMotif($motifEntity);
    	}
	}
	if (isset($productDetails["patterns"])) {
		$patterns = json_decode($productDetails["patterns"],true);
		foreach ($patterns as $key => $value) {
			$color['id'] = 0;
            $color['product_id'] = $productMapper->getProductId();
            $color['pattern'] = $value;	
    	
	    	$patternEntity = new ProductPatternEntity($color);
	    	$productMapper->savePattern($patternEntity);
    	}
	}
	if (isset($productDetails["fonts"])) {

		$fonts = json_decode($productDetails["fonts"],true);

		foreach ($fonts as $key => $value) {
			$font['id'] = 0;
            $font['product_id'] = $productMapper->getProductId();
            $font['font_id'] = $value;	
    	
	    	$fontEntity = new ProductFontEntity($font);
	    	$productMapper->saveFont($fontEntity);
    	}
	}
	$imageno = 1;
	$path = "";
    foreach ($files as $key => $value) {

    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);

	    $ext = "";
	    if ($value->getClientMediaType() == "image/jpeg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/jpg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/png" ) {
	    	$ext = ".png";
	    }
	    if (isset($string) && isset($ext)) {
	    	$path = $imagesTargetPath.$string.$imageno.$ext;
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$imagedata["images_id"] = "0";
	    	$imagedata["path"] = $path;
	    	$imagedata["product_id"] = $productMapper->getProductId();
	    	$imagedata["product_type"] = 2;
	    	$image = new ImageEntity($imagedata);
	    	$productMapper->saveImage($image);
	    }
	    $imageno++;
    }
    
    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }
    $this->logger->addInfo(" Successfully Created product .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});


$app->get('/productnormal/{id}', function (Request $request, Response $response, $args){
    $productid = $args["id"];
    $this->logger->addInfo(" Fetching product for ". $productid ." id.. ");

    $productMapper = new NormalProductMapper($this->db);
	$result = array();
	$data = $productMapper->getProductById($productid);
	$images = $productMapper->getImagesOfProductsById($productid);
	if (isset($data) ) {
		$this->logger->addInfo("Products Retrived .. ");

		$product["id"] = $data->getId();
		$product["name"] = $data->getName();
		$product["description"] = $data->getDescription();
		$product["additionalInformation"] = $data->getAddtionalInformation();
		$product["notes"] = $data->getNotes();
		$product["length"] = $data->getLength();
		$product["height"] = $data->getHeight();
		$product["depth"] = $data->getDepth();
		$product["weight"] = $data->getWeight();
		$product["category"] = $data->getCategory();
		$product["subcategory"] = $data->getSubCategory();
		$product["material"] = $data->getMaterial();
		$product["cod"] = $data->getCOD();
		$product["price"] = $data->getPrice();
		$product["featured"] = $data->getFeatured();
		$product["status"] = $data->getStatus();
		
		$i = 1;
		foreach ($images as $key) {
			$product["images"][$i] = $key->getPath();
			$i++;
		}
		$result ["product"] = $product;

   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data for ". $productid ." id .. ");
	$result["error"] = "cannot process request contact your administrator";

    return $response;
});

$app->get('/product/nameplate/{id}', function (Request $request, Response $response, $args){
    $productid = $args["id"];
    $this->logger->addInfo(" Fetching product for ". $productid ." id.. ");

    $productMapper = new ProductMapper($this->db);
    $patternMapper = new PatternMapper($this->db);
	$motifMapper = new MotifMapper($this->db);
	$fontMapper = new FontMapper($this->db);
	$result = array();
	$data = $productMapper->getProductById($productid);
	$images = $productMapper->getImagesOfProductsById($productid);
	$colors = $productMapper->getColorsOfProductsById($productid);
	$patterns = $productMapper->getPatternsOfProductsById($productid);
	$motifs = $productMapper->getMotifsOfProductsById($productid);
	$fonts = $productMapper->getFontsOfProductsById($productid);

	if (isset($data) ) {
		$this->logger->addInfo("Products Retrived .. ");

		$product["id"] = $data->getId();
		$product["name"] = $data->getName();
		$product["description"] = $data->getDescription();
		$product["addtional_information"] = $data->getAddtionalInformation();
		$product["notes"] = $data->getNotes();
		$product["per_char_charge"] = $data->getPerCharPriceAfterMaxCharacters();
		$product["max_characters"] = $data->getMaxCharacters();
		$product["price_after_max_font_size"] = $data->getPerCharPriceAfterMaxFontSize();
		$product["max_font_size"] = $data->getMaxFontSize();
		$product["material"] = $data->getMaterial();
		$product["nameplate_used"] = $data->getNameplateUsed();
		$product["category"] = $data->getCategory();
		$product["subcategory"] = $data->getSubCategory();
		$product["cod"] = $data->getCOD();
		$product["letter_type"] = $data->getLetterType();
		$product["fitting_place"] = $data->getFittingPlace();
		$product["length"] = $data->getLength();
		$product["height"] = $data->getHeight();
		$product["weight"] = $data->getWeight();
		$product["price"] = $data->getPrice();
		$product["trending"] = $data->getTrending();
		$product["font_effect"] = $data->getFontEffect();
		$i = 1;
		foreach ($images as $image) {
			$product["images"][$i] = $image->getPath();
			$i++;
		}
		$i = 1;
		foreach ($colors as $color) {
			$product["colors"][$i] = $color->getColorHashCode();
			$i++;
		}
		$i = 1;
		foreach ($patterns as $key) {
			$patternid = $key->getPattern();
			$pattern = $patternMapper->getPatternById($patternid);

			$product["patterns"][$i]["id"] = $pattern->getId();
			$product["patterns"][$i]["name"] = $pattern->getName();
			$product["patterns"][$i]["pattern_path"] = $pattern->getPatternPath();

			$i++;
		}
		$i = 1;
		foreach ($motifs as $key) {
			$motifid = $key->getMotifId();
			$motif = $motifMapper->getMotifById($motifid);

			$product["motifs"][$i]["id"] = $motif->getId();
			$product["motifs"][$i]["name"] = $motif->getName();
			$product["motifs"][$i]["motif_path"] = $motif->getMotifPath();
			$product["motifs"][$i]["description"] = $motif->getDescription();

			$i++;
		}

		$i = 1;
		foreach ($fonts as $key) {
			$fontid = $key->getFontId();
			$font = $fontMapper->getFontById($fontid);

			$product["fonts"][$i]["id"] = $font->getId();
			$product["fonts"][$i]["name"] = $font->getName();
			$product["fonts"][$i]["filepath"] = $font->getFilePath();
			$i++;
		}
		$result ["product"] = $product;

   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data for ". $productid ." id .. ");
	$result["error"] = "cannot process request contact your administrator";

    return $response;
});



/*
	Product based on categories
*/
$app->get('/products/categories/{categoryid}/{subcategoryid}', function (Request $request, Response $response, $args){
	$categoryid = $args["categoryid"];
	$subcategoryid = $args["subcategoryid"];
	$this->logger->addInfo(" Request Recieved for listing of products for categories.. ");
	$productMapper = new NormalProductMapper($this->db);
	$result = array();
	$data = $productMapper->getProducts();
	if (isset($data) ) {
		$this->logger->addInfo("Products Retrived .. ");
		
		foreach ($data as $key) {
			
			$images = $productMapper->getImagesOfProductsById($key->getId());
			
			$product["id"] = $key->getId();
			$product["name"] = $key->getName();
			$product["description"] = $key->getDescription();
			$product["additionalInformation"] = $key->getAddtionalInformation();
			$product["material"] = $key->getMaterial();
			$product["cod"] = $key->getCOD();
			$product["price"] = $key->getPrice();
			$product["status"] = $key->getStatus();
			
			$i = 1;
			foreach ($images as $key) {
				$product["images"][$i] = $key->getPath();
				$i++;
			}
			$result ["products"][$key->getId()] = $product;
			
		}
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "1";
	return $response->withJson($result,200);
});


/*
 * cart requests
 */
$app->post('/cart/user', function (Request $request, Response $response, $args){

	$params = $request->getParsedBody();
	$user_id = $params["userid"];
	$this->logger->addInfo("Fetching cart data for user $user_id from database .. ");
	
	$customer = new CustomerMapper($this->db);
    $user = $customer->getUserDetailsByUserId($user_id);
    $cartMapper = new CartMapper();
    $cartDetails = $cartMapper->getCart($user);
    
    return $response;
});

$app->post('/cart/add', function (Request $request, Response $response, $args){
	$params = $request->getParsedBody();
	$result = array();
	if(isset($params["product_id"]) && isset($params["product_type"]))
	{

	    $productid = $params["product_id"];
	    $producttype = $params["product_type"];
	    if ($producttype == "1") {
	    	$productMapper = new NormalProductMapper($this->db);
			$data = $productMapper->getProductById($productid);
			$images = $productMapper->getImagesOfProductsById($productid);
			if(isset($_SESSION["products"])){  //if session var already exist
	            if(isset($_SESSION["products"][$productid])) //check item exist in products array
	            {
	            	if (isset($_SESSION["products"][$productid]["product_quantity"])) {
	            		$currentquantity = $_SESSION["products"][$productid]["product_quantity"];
	            	}
	            	//unset old item
	            	unset($_SESSION["products"][$productid]);
	            	$new_product["product_id"] =  $data->getId();
			    	$new_product["product_name"] =  $data->getName();
			    	$new_product["product_description"] =  $data->getDescription();
			    	$new_product["product_price"] =  $data->getPrice();
			    	$new_product["product_type"] =  "1";
			    	$new_product["product_quantity"] =  $currentquantity + 1;
			    	$i = 1;
			    	foreach ($images as $key) {
						$new_product["product_image"] = $key->getPath();
						break;
					}
	            }else if (isset($data)) {
			    	$new_product["product_id"] =  $data->getId();
			    	$new_product["product_name"] =  $data->getName();
			    	$new_product["product_description"] =  $data->getDescription();
			    	$new_product["product_price"] =  $data->getPrice();
			    	$new_product["product_type"] =  "1";
			    	$new_product["product_quantity"] =  1;
			    	$i = 1;
			    	foreach ($images as $key) {
						$new_product["product_image"] = $key->getPath();
						break;
					}
			    }   
	        }

	        $_SESSION["products"][$productid] = $new_product;

	    }else if($producttype == "2"){
		    $productMapper = new ProductMapper($this->db);
		    $data = $productMapper->getProductById($productid);
			$images = $productMapper->getImagesOfProductsById($productid);
		    if(isset($_SESSION["products"])){  //if session var already exist
	            if(isset($_SESSION["products"][$productid])) //check item exist in products array
	            {
	            	if (isset($_SESSION["products"][$productid]["product_quantity"])) {
	            		$currentquantity = $_SESSION["products"][$productid]["product_quantity"];
	            	}
	            	unset($_SESSION["products"][$productid]);
	            	$new_product["product_id"] =  $data->getId();
			    	$new_product["product_name"] =  $data->getName();
			    	$new_product["product_description"] =  $data->getDescription();
			    	$new_product["product_price"] =  $data->getPrice();
			    	$new_product["product_type"] =  "2";
			    	$new_product["product_quantity"] =  $currentquantity + 1;
			    	$i = 1;
			    	foreach ($images as $key) {
						$new_product["product_image"] = $key->getPath();
						break;
					}
	                 //unset old item
	            }else if (isset($data)) {
			    	$new_product["product_id"] =  $data->getId();
			    	$new_product["product_name"] =  $data->getName();
			    	$new_product["product_description"] =  $data->getDescription();
			    	$new_product["product_price"] =  $data->getPrice();
			    	$new_product["product_type"] =  "2";
			    	$new_product["product_quantity"] =  1;
			    	$i = 1;
			    	foreach ($images as $key) {
						$new_product["product_image"] = $key->getPath();
						break;
					}
			    }   
	        }

	        $_SESSION["products"][$productid] = $new_product;
	    }else{

	    }
	    
	    $total_items = count($_SESSION["products"]); //count total items
	    $result['items'] = $total_items; //output json 

	}



    return $response->withJson($result,200);
});


$app->get('/cart', function (Request $request, Response $response, $args){
	$result = array();
    if(isset($_SESSION["products"]) && count($_SESSION["products"])>0){ 
        $total = 0;
        foreach($_SESSION["products"] as $product){ //loop though items and prepare html content
            
            //set variables to use them in HTML content below
            $productId = $product["product_id"]; 
            $productPrice = $product["product_price"];
            $productQty = $product["product_quantity"];
            
            
            $subtotal = ($productPrice * $productQty);
            $total = ($total + $subtotal);
            $product["subtotal"] = $subtotal;
            $result["products"][$productId] = $product;
        }
        $result["items"] = count($_SESSION["products"]);
        $result["total"] = $total;
        
    }else{
       	$result["items"] = 0;
        $result["total"] = 0; //we have empty cart
    }
	
    return $response->withJson($result,200);
});

$app->post('/cart/remove', function (Request $request, Response $response, $args){
	$params = $request->getParsedBody();
	$result = array();
	if(isset($params["product_id"]) && isset($_SESSION["products"]))
	{
		$productid = $params["product_id"];

	    if(isset($_SESSION["products"][$productid]))
	    {
	        unset($_SESSION["products"][$productid]);
	    }
	    
	    $total_items = count($_SESSION["products"]);
	   	$result['items'] = $total_items;
	}
    return $response->withJson($result,200);
});
/*
	Blog Section
 */
$app->get('/blogs', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of blogs .. ");
	$blogMapper = new BlogMapper($this->db);
	$result = array();
	$data = $blogMapper->getBlogs();
	if (isset($data) ) {
		$this->logger->addInfo("Blogs Retrived .. ");
		
		foreach ($data as $key) {

			$blog["id"] = $key->getId();
			$blog["title"] = $key->getTitle();
			$blog["short_description"] = $key->getShortDescription();
			$blog["content"] = $key->getContent();
			$blog["image_path"] = $key->getImagePath();
			$blog["visible"] = $key->isVisible();
			$result["blogs"][$key->getId()] = $blog;
		}
	
		
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});
$app->get('/blog/home', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of blogs .. ");
	$blogMapper = new BlogMapper($this->db);
	$result = array();
	$data = $blogMapper->getBlogsForHome();
	if (isset($data) ) {
		$this->logger->addInfo("Blogs Retrived .. ");
		
		foreach ($data as $key) {

			$blog["id"] = $key->getId();
			$blog["title"] = $key->getTitle();
			$blog["short_description"] = $key->getShortDescription();
			$blog["content"] = $key->getContent();
			$blog["image_path"] = $key->getImagePath();
			$blog["visible"] = $key->isVisible();
			$blog["released"] = $key->getReleased();
			$result["blogs"][$key->getId()] = $blog;
		}
	
		
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});
$app->get('/blog/{id}', function (Request $request, Response $response, $args){
    $blogid = $args["id"];
    $this->logger->addInfo(" Fetching blog for ". $blogid ." id.. ");

    $blogMapper = new BlogMapper($this->db);
	$result = array();
	$data = $blogMapper->getBlogById($blogid);
	
	if (isset($data) ) {
		$this->logger->addInfo("Blog Retrived .. ");

		$blog["id"] = $data->getId();
		$blog["title"] = $data->getTitle();
		$blog["short_description"] = $data->getShortDescription();
		$blog["content"] = $data->getContent();
		$blog["image_path"] = $data->getImagePath();
		$blog["visible"] = $data->isVisible();
		
		
		$result ["blog"] = $blog;

   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data for ". $blogid ." id .. ");
	$result["error"] = "cannot process request contact your administrator";

    return $response;
});
$app->post('/blog/add', function (Request $request, Response $response, $args){

	$imagesTargetPath = "images/blog/";
    $blogDetails = $request->getParsedBody();
    $file = $request->getUploadedFiles();
    //add validations here	

    $this->logger->addInfo("Inserting blog ". $blogDetails["title"] ." into database .. ");
    $blogTemp["id"] = 0;
    $blogTemp["title"] = $blogDetails["title"];
    $blogTemp["short_description"] = $blogDetails["description"];
    $blogTemp["content"] = $blogDetails["content"];
    
    $blogTemp["visible"] = $blogDetails["visible"];
	
    $string = $blogTemp["title"];
	$imageno = 1;
	$path = "";
    foreach ($file as $key => $value) {

    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);

	    $ext = "";
	    if ($value->getClientMediaType() == "image/jpeg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/jpg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/png" ) {
	    	$ext = ".png";
	    }
	    if (isset($string) && isset($ext)) {
	    	$path = $imagesTargetPath.$string.$imageno.$ext;
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$blogTemp["image_path"] = $path;
	    }
	   
    }
 	$blog = new BlogEntity($blogTemp);
    $this->logger->addInfo("Created object for blog ". $blog->getTitle() .".. ");

    $blogMapper = new BlogMapper($this->db);
    $isCreated = $blogMapper->save($blog);

    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully Created product {$blog->getTitle()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});


/*
	Timeline Images
 */
$app->get('/timeline', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of Timeline Images .. ");
	$timelineImageMapper = new TimelineImageMapper($this->db);
	$result = array();
	$data = $timelineImageMapper->getTimelineImages();
	if (isset($data) ) {
		$this->logger->addInfo("Timeline Images Retrived .. ");
		
		foreach ($data as $key) {

			$image["id"] = $key->getId();
			$image["image_path"] = $key->getImagePath();
			$image["image_number"] = $key->getImageNumber();
			$result["timeline"][$key->getImageNumber()] = $image;
		}
	
		
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});


$app->post('/timeline', function (Request $request, Response $response, $args){
	$imagesTargetPath = "images/timeline/";
    $file = $request->getUploadedFiles();
    	
    //add validations here	
    $imagesUploaded = array();
    $isCreated = false;
    foreach ($file as $key => $value) {
    	$string = uniqid(); //geneartes a unique id using time 
    	$timelineImage["id"] = 0;
    	$this->logger->addInfo("Inserting timeline image number ". $key ." into 
    	filesystem .. ");
		$path = "";
    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);

	    $ext = null;
	    if ($value->getClientMediaType() == "image/jpeg" ) {
	    	$ext = ".jpg";
	    }else if ($value->getClientMediaType() == "image/jpg" ) {
	    	$ext = ".jpg";
	    }


	    if (isset($string) && isset($ext)) {
	    	
	    	$path = $imagesTargetPath.$string.$ext;
	    	$timelineImage["image_path"] = $path;
	    	$timelineImage["image_number"] = $key;
	    	
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	$timelineImageEntity = new TimelineImageEntity($timelineImage);
	    	$timelineImageMapper = new TimelineImageMapper($this->db);
	    	$timelineImageMapper->save($timelineImageEntity);

	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$isCreated =true;
	    	$imagesUploaded["image"][$key] = $string;
	    }
	   
    }
 	
    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot upload image .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully uploaded images {". json_encode($imagesUploaded)."} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});

$app->get('/ourrecentwork', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of Recent work Images .. ");
	$ourRecentWorkMapper = new OurRecentWorkImageMapper($this->db);
	$result = array();
	$data = $ourRecentWorkMapper->getOurRecentWorkImages();
	if (isset($data) ) {
		$this->logger->addInfo("ourrecentwork Images Retrived .. ");
		
		foreach ($data as $key) {

			$image["id"] = $key->getId();
			$image["image_path"] = $key->getImagePath();
			$image["image_number"] = $key->getImageNumber();
			$result["ourrecentwork"][$key->getImageNumber()] = $image;
		}	
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});


$app->post('/ourrecentwork', function (Request $request, Response $response, $args){
	$imagesTargetPath = "images/recent/";
    $file = $request->getUploadedFiles();
    	
    //add validations here	
    $imagesUploaded = array();
    $isCreated = false;
    foreach ($file as $key => $value) {
    	$string = uniqid(); //geneartes a unique id using time 
    	$ourRecentWorkImage["id"] = 0;
		
    	$this->logger->addInfo("Inserting ourrecentwork image number ". $key ." into 
    	filesystem .. ");
		$path = "";
    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to nothing
	    $string = preg_replace("/[\s_]/", "", $string);

	    $ext = null;
	    if ($value->getClientMediaType() == "image/jpeg" ) {
	    	$ext = ".jpg";
	    }else if ($value->getClientMediaType() == "image/jpg" ) {
	    	$ext = ".jpg";
	    }
	    if (isset($string) && isset($ext)) {
	    	$path = $imagesTargetPath.$string.$ext;
	    	$ourRecentWorkImage["image_path"] = $path;
	    	$ourRecentWorkImage["image_number"] = $key;
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");


	    	$ourRecentWorkEntity = new OurRecentWorkImageEntity($ourRecentWorkImage);
	    	$ourRecentWorkMapper = new OurRecentWorkImageMapper($this->db);
	    	$ourRecentWorkMapper->save($ourRecentWorkEntity);
	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$isCreated =true;
	    	$imagesUploaded["image"][$key] = $string;
	    }
	   
    }
 	
    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot upload image .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully uploaded images {". json_encode($imagesUploaded)."} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});


$app->get('/showcase', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of Recent work Images .. ");
	$showcaseMapper = new ShowcaseImageMapper($this->db);
	$result = array();
	$data = $showcaseMapper->getShowcaseImages();
	if (isset($data) ) {
		$this->logger->addInfo("Showcase Images Retrived .. ");
		
		foreach ($data as $key) {

			$image["id"] = $key->getId();
			$image["image_path"] = $key->getImagePath();
			$image["image_number"] = $key->getImageNumber();
			$result["showcase"][$key->getImageNumber()] = $image;
		}	
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});

$app->post('/showcase', function (Request $request, Response $response, $args){
	$imagesTargetPath = "images/showcase/";
    $file = $request->getUploadedFiles();
    	
    //add validations here	
    $imagesUploaded = array();
    $isCreated = false;
    foreach ($file as $key => $value) {
    	$string = uniqid(); //geneartes a unique id using time 
    	$showcaseImage["id"] = 0;
		
    	$this->logger->addInfo("Inserting timeline image number ". $key ." into 
    	filesystem .. ");
		$path = "";
    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to nothing
	    $string = preg_replace("/[\s_]/", "", $string);

	    $ext = null;
	    if ($value->getClientMediaType() == "image/jpeg" ) {
	    	$ext = ".jpg";
	    }else if ($value->getClientMediaType() == "image/jpg" ) {
	    	$ext = ".jpg";
	    }
	    if (isset($string) && isset($ext)) {
	    	$path = $imagesTargetPath.$string.$ext;
	    	$showcaseImage["image_path"] = $path;
	    	$showcaseImage["image_number"] = $key;
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");

	    	$showcaseImageEntity = new ShowcaseImageEntity($showcaseImage);
	    	$showcaseImageMapper = new ShowcaseImageMapper($this->db);
	    	$showcaseImageMapper->save($showcaseImageEntity);

	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$isCreated =true;
	    	$imagesUploaded["image"][$key] = $string;
	    }
    }
    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot upload image .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }
    $this->logger->addInfo(" Successfully uploaded images {". json_encode($imagesUploaded)."} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});

/*
	Motifs Section
 */

$app->get('/motifs', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of motifs .. ");
	$motifMapper = new MotifMapper($this->db);
	$result = array();
	$data = $motifMapper->getMotifs();
	if (isset($data) ) {
		$this->logger->addInfo("Motifs Retrived .. ");
		
		foreach ($data as $key) {
			$motif["id"] = $key->getId();
			$motif["name"] = $key->getName();
			$motif["description"] = $key->getDescription();
			$motif["motif_path"] = $key->getMotifPath();
			$result["motifs"][$key->getId()] = $motif;
		}		
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});

$app->get('/motif/{id}', function (Request $request, Response $response, $args){
    $motifid = $args["id"];
    $this->logger->addInfo(" Fetching motif for ". $motifid ." id.. ");

    $motifMapper = new MotifMapper($this->db);
	$result = array();
	$data = $motifMapper->getMotifById($motifid);
	
	if (isset($data) ) {
		$this->logger->addInfo("Motif Retrived .. ");

		$motif["id"] = $data->getId();
		$motif["name"] = $data->getName();
		$motif["description"] = $data->getDescription();
		$motif["motif_path"] = $data->getMotifPath();
		
		$result ["motif"] = $motif;

   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data for ". $motifid ." id .. ");
	$result["error"] = "cannot process request contact your administrator";

    return $response;
});
$app->post('/motif/add', function (Request $request, Response $response, $args){
	$imagesTargetPath = "images/motifs/";
    $motifDetails = $request->getParsedBody();
    $file = $request->getUploadedFiles();
    	
    //add validations here	

    $this->logger->addInfo("Inserting motif ". $motifDetails["name"] ." into database .. ");
    $motifTemp["id"] = 0;
    $motifTemp["name"] = $motifDetails["name"];
    $motifTemp["description"] = $motifDetails["description"];
    
	
    $string = $motifTemp["name"];
	$imageno = 1;
	$path = "";
    foreach ($file as $key => $value) {

    	//Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);

	    $ext = "";
	    if ($value->getClientMediaType() == "image/jpeg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/jpg" ) {
	    	$ext = ".jpg";
	    }
	    if ($value->getClientMediaType() == "image/png" ) {
	    	$ext = ".png";
	    }
	    if (isset($string) && isset($ext)) {
	    	$path = $imagesTargetPath.$string.$imageno.$ext;
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
	    }
	    if ($value->getError() == UPLOAD_ERR_OK && isset($path)) {
	    	$motifTemp["motif_path"] = $path;
	    }
	   
    }
 	$motif = new MotifEntity($motifTemp);
    $this->logger->addInfo("Created object for motif ". $motif->getName() .".. ");

    $motifMapper = new MotifMapper($this->db);
    $isCreated = $motifMapper->save($motif);

    if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot process data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully added motif {$motif->getName()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);
});





$app->post('/orders', function (Request $request, Response $response, $args){

    return $response;
});

$app->post('/wishlist', function (Request $request, Response $response, $args){

    return $response;
});

/*
	Request Design Section
 */

$app->post('/requestdesign', function (Request $request, Response $response, $args){

	$requestedDesignDetails = $request->getParsedBody();

    $this->logger->addInfo("Requesting Design for  ". $requestedDesignDetails["name"] ." into database .. ");
    $requestedDesignDetails["id"] = 0;
    $request = new RequestDesignEntity($requestedDesignDetails);
    $this->logger->addInfo("Created object for request ". $request->getName() .".. ");

    $requestDesignMapper = new RequestDesignMapper($this->db);
    $isCreated = $requestDesignMapper->save($request);

     if ( !$isCreated ) {
    	$this->logger->addInfo(" Request Recieved but cannot process data .. ");
		$result["error"] = "1";
		return $response->withJson($result,200);
    }

    $this->logger->addInfo(" Successfully added requested design {$request->getName()} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);

});

$app->get('/requesteddesign', function (Request $request, Response $response) {
	$this->logger->addInfo(" Request Recieved for listing of motifs .. ");
	$requestDesignMapper = new RequestDesignMapper($this->db);
	$result = array();
	$data = $requestDesignMapper->getRequestedDesigns();
	if (isset($data) ) {
		$this->logger->addInfo("Motifs Retrived .. ");
		
		foreach ($data as $key) {
			$requestor["id"] = $key->getId();
			$requestor["name"] = $key->getName();
			$requestor["email"] = $key->getEmail();
			$requestor["contact_number"] = $key->getContactNumber();
			$requestor["requirements"] = $key->getRequirements();
			$result["designs"][$key->getId()] = $requestor;
		}		
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});

$app->get('/requesteddesign/{id}', function (Request $request, Response $response, $args){
    $designid = $args["id"];
    $this->logger->addInfo(" Fetching requested Design for ". $designid ." id.. ");

    $requestDesignMapper = new RequestDesignMapper($this->db);
	$result = array();
	$data = $requestDesignMapper->getRequestedDesignsById($designid);
	
	if (isset($data) ) {
		$this->logger->addInfo("Motif Retrived .. ");
		$requestor["id"] = $data->getId();
		$requestor["name"] = $data->getName();
		$requestor["email"] = $data->getEmail();
		$requestor["contact_number"] = $data->getContactNumber();
		$requestor["requirements"] = $data->getRequirements();
		
		$result ["designs"] = $requestor;

   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data for ". $motifid ." id .. ");
	$result["error"] = "cannot process request contact your administrator";

    return $response;
});



/*
	Related Products Section
 */

$app->post('/product/related', function (Request $request, Response $response, $args){

	$relatedProductDetails = $request->getParsedBody();

    $this->logger->addInfo("Inserting Related Designs ". $relatedProductDetails["product_id"] ." into database .. ");
    
    $productId = $relatedProductDetails["product_id"];
  

    $relatedProductMapper = new RelatedProductMapper($this->db);
    if (isset($relatedProductDetails["related_products"])) {
		$products = json_decode($relatedProductDetails["related_products"],true);
		foreach ($products as $key => $value) {

			$product['id'] = 0;
            $product['product_id'] = $productId;
            $product['related_product_id'] = $value;
    		
	    	$relatedProduct = new RelatedProductEntity($product);
	    	$isCreated = $relatedProductMapper->save($relatedProduct);
	    	if ( !$isCreated ) {
		    	$this->logger->addInfo(" Request Recieved but cannot process data .. ");
				$result["error"] = "1";
				return $response->withJson($result,200);
		    }
    	}
	}

    $this->logger->addInfo(" Successfully added related Products for {$productId} .. ");
	$result["success"] = "1";
	return $response->withJson($result,201);

});

$app->get('/product/related/{id}', function (Request $request, Response $response, $args){
    $productid = $args["id"];
    $this->logger->addInfo(" Fetching related products for ". $productid ." id.. ");

    $relatedProductsMapper = new RelatedProductMapper($this->db);
	$result = array();
	$data = $relatedProductsMapper->getRelatedProductsByProductId($productid);
	$productMapper = new ProductMapper($this->db);
	if (isset($data) ) {
		foreach ($data as $key) {
			$realatedProductId = $key->getRelatedProductId();
			$productInformation = $productMapper->getProductById($realatedProductId);
			$images = $productMapper->getImagesOfProductsById($realatedProductId);
			$product["id"] = $productInformation->getId();
			$product["name"] = $productInformation->getName();
			
			foreach ($images as $image) {
				$product["images"] = $image->getPath();
				break;
			}

			$result ["products"][$key->getId()] = $product;

		}
		
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data for ". $motifid ." id .. ");
	$result["error"] = "cannot process request contact your administrator";

    return $response;
});

$app->run();
