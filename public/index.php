<?php

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
			
			$material["id"] = $key->getId();
			$material["name"] = $key->getName();
			$material["description"] = $key->getDescription();
		
			$result ["categories"][$key->getId()] = $material;
			
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
			$product["material"] = $key->getMaterial();
			$product["cod"] = $key->getCOD();
			$product["price"] = $key->getPrice();
			$product["status"] = $key->getStatus();
			
			foreach ($images as $image) {
				$product["image"] = $image->getPath();
				break;
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
			$product["material"] = $key->getMaterial();
			$product["cod"] = $key->getCOD();
			$product["price"] = $key->getPrice();
			$product["status"] = $key->getStatus();
			foreach ($images as $image) {
				$product["image"] = $image->getPath();
				break;
			}
			$result ["products"][$key->getId()] = $product;
			
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
	$result = array();
	$data = $productMapper->getProducts();
	if (isset($data) ) {
		$this->logger->addInfo("Products Retrived .. ");
		
		foreach ($data as $key) {
			
			$product["id"] = $key->getId();
			$product["name"] = $key->getName();
			$product["description"] = $key->getDescription();
			$product["max_rows"] = $key->getMaxRows();
			$product["max_charcters"] = $key->getMaxCharacters();
			$product["material"] = $key->getMaterial();
			$product["cod"] = $key->getCOD();
			$product["letter_type"] = $key->getLetterType();
			$product["nameplate_used"] = $key->getNameplateUsed();
			$product["fitting_place"] = $key->getFittingPlace();
			$product["length"] = $key->getLength();
			$product["height"] = $key->getHeight();
			$product["depth"] = $key->getDepth();
			$product["weight"] = $key->getWeight();
			$product["images"] = $key->getImages();
			$product["price"] = $key->getPrice();
			$product["status"] = $key->getStatus();
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

	$string = $productDetails["name"];
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
	
    $productDetails = $request->getParsedBody();
    $files = $request->getUploadedFiles();
    
   	 //creating data array to be passed to Product Entity
    $product["product"]["name"] = $productDetails["name"];
    $product["product"]["price"] = $productDetails["price"];
    $product["product"]["description"] = $productDetails["description"];
    $product["product"]["additionalInformation"] = $productDetails["additionalInformation"];
    $product["product"]["cod"] = $productDetails["cod"];
    $product["product"]["material"] = $productDetails["material"];
    $product["product"]["featured"] = $productDetails["featured"];
    $this->logger->addInfo("Inserting product ". $product["product"]["name"] ." into database .. ");

    $data = $product["product"];

    $product = new ProductEntity($data);
    $this->logger->addInfo("Created object for product ". $product->getName() .".. ");
    
    $productMapper = new ProductMapper($this->db);
    $isCreated = $productMapper->save($product);
    
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


/*
 * cart requests
 */
$app->post('/cart', function (Request $request, Response $response, $args){

	$params = $request->getParsedBody();
	$user_id = $params["userid"];
	$this->logger->addInfo("Fetching cart data for user $user_id from database .. ");
	
	$customer = new CustomerMapper($this->db);
    $user = $customer->getUserDetailsByUserId($user_id);
    $cartMapper = new CartMapper();
    $cartDetails = $cartMapper->getCart($user);
    
    return $response;
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
	Change Images
 */
$app->post('/timeline', function (Request $request, Response $response, $args){
	$imagesTargetPath = "images/timeline/";
    $file = $request->getUploadedFiles();
    	
    //add validations here	
    $imagesUploaded = array();
    $isCreated = false;
    foreach ($file as $key => $value) {
    	$string = "slide-show-". $key ."-low";
		
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
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
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

$app->post('/ourrecentwork', function (Request $request, Response $response, $args){
	$imagesTargetPath = "images/recent/";
    $file = $request->getUploadedFiles();
    	
    //add validations here	
    $imagesUploaded = array();
    $isCreated = false;
    foreach ($file as $key => $value) {
    	$string = "showcase". $key;
		
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
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
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


$app->post('/showcase', function (Request $request, Response $response, $args){
	$imagesTargetPath = "images/showcase/";
    $file = $request->getUploadedFiles();
    	
    //add validations here	
    $imagesUploaded = array();
    $isCreated = false;
    foreach ($file as $key => $value) {
    	$string = "block". $key;
		
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
	    	$value->moveTo($path);
	    	$this->logger->addInfo("Uploaded Image ". $string ." into filesystem at {$path} - {$key} .. ");
	    	
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

    $this->logger->addInfo("Inserting botif ". $motifDetails["name"] ." into database .. ");
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


$app->run();
