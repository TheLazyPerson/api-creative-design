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
$app->add(function ($request, $response, $next) {
	$headers = $request->getHeaders();
	$this->logger->addInfo("Request Recieved ");
	foreach ($headers as $name => $values) {
		// Code .. # for adding data to database for requests
	   	//$this->logger->addInfo($name . ": " . implode(", ", $values));
	   	//$this->logger->addInfo($request->getHeader(''));
	}
	if ($request->hasHeader('HTTP_X_FORWARDED_FOR')) {
	    $this->logger->addInfo($request->getHeader('HTTP_X_FORWARDED_FOR'));
	}
	//extract information from request to analyse requests 
    $response = $next($request, $response);
    
	return $response;
});


$app->get('/testsqlconnection', function(Request $request, Response $response) {
	
	$this->logger->addInfo("Testing Sql Connection .. ");
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


$app->get('/products', function (Request $request, Response $response) {
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
			$result ["products"][$key->getId()] = $product;
		}
	
	
   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data .. ");
	$result["error"] = "cannot process request contact your administrator";
	return $response->withJson($result,200);
});

$app->get('/product/{id}', function (Request $request, Response $response, $args){
    $productid = $args["id"];
    $this->logger->addInfo(" Fetching product for ". $productid ." id.. ");

    $productMapper = new ProductMapper($this->db);
	$result = array();
	$data = $productMapper->getProductById($productid);
	if (isset($data) ) {
		$this->logger->addInfo("Products Retrived .. ");

		$product["id"] = $data->getId();
		$product["name"] = $data->getName();
		$product["description"] = $data->getDescription();
		$product["max_rows"] = $data->getMaxRows();
		$product["max_charcters"] = $data->getMaxCharacters();
		$product["material"] = $data->getMaterial();
		$product["cod"] = $data->getCOD();
		$product["letter_type"] = $data->getLetterType();
		$product["nameplate_used"] = $data->getNameplateUsed();
		$product["fitting_place"] = $data->getFittingPlace();
		$product["length"] = $data->getLength();
		$product["height"] = $data->getHeight();
		$product["depth"] = $data->getDepth();
		$product["weight"] = $data->getWeight();

		$product["images"] = $data->getImages();
		$product["price"] = $data->getPrice();
		$result ["product"] = $product;

   		return $response->withJson($result,200);
	}
	$this->logger->addInfo(" Request Recieved but cannot retrieve data for ". $productid ." id .. ");
	$result["error"] = "cannot process request contact your administrator";

    return $response;
});

$app->post('/product', function (Request $request, Response $response, $args){
    /*
    name:
description:
max_rows:
max_charcters:
material:
cod:
letter_type:
nameplate_used:
fitting_place:
length:
height:
depth:
weight:
images:
price:

     */
    
    $body = $request->getParsedBody();

    $this->logger->addInfo(" Inserting product ". $body["product"]["name"] ." into database .. ");

    return $response;
});



$app->run();
